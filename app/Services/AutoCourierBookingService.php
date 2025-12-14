<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Address;
use App\Models\ApiIntegration;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AutoCourierBookingService
{
    protected $shiprocketService;
    protected $pickupPincode; // Your warehouse/pickup location pincode

    public function __construct()
    {
        $this->shiprocketService = new ShiprocketService();
        // Get pickup pincode from settings or config (default to 110001 for Delhi)
        $this->pickupPincode = config('services.shiprocket.pickup_pincode', '110001');
    }

    /**
     * Automatically book courier for an order
     * 
     * @param Order $order
     * @return array
     */
    public function autoBookCourier(Order $order)
    {
        try {
            Log::info('Auto courier booking started', ['order_id' => $order->id]);

            // Load order relationships
            $order->load(['items.product', 'user']);
            
            // Try to load shipping address if address_id is stored
            // If not, we'll extract from string
            if (isset($order->shippingAddressRelation)) {
                $order->shippingAddress = $order->shippingAddressRelation;
            } elseif (property_exists($order, 'shipping_address_id') && $order->shipping_address_id) {
                $order->shippingAddress = Address::find($order->shipping_address_id);
            }

            // Get delivery pincode from shipping address
            $deliveryPincode = $this->extractPincode($order);
            
            if (!$deliveryPincode) {
                Log::warning('No delivery pincode found for order', ['order_id' => $order->id]);
                return [
                    'success' => false,
                    'message' => 'Delivery pincode not found. Please update shipping address.',
                    'requires_manual_review' => true
                ];
            }

            // Check if Shiprocket integration is configured
            $shiprocketIntegration = ApiIntegration::where('type', 'shiprocket')->latest()->first();
            
            if (!$shiprocketIntegration) {
                Log::warning('Shiprocket integration not configured', ['order_id' => $order->id]);
                return [
                    'success' => false,
                    'message' => 'Shiprocket integration not configured. Please configure in admin panel.',
                    'requires_manual_review' => true
                ];
            }

            // Check courier availability for the pincode
            $courierAvailable = $this->checkCourierAvailability($deliveryPincode, $order);
            
            if (!$courierAvailable) {
                Log::warning('No courier available for pincode', [
                    'order_id' => $order->id,
                    'pincode' => $deliveryPincode
                ]);
                return [
                    'success' => false,
                    'message' => 'No courier service available for this pincode. Order will be processed manually.',
                    'requires_manual_review' => true
                ];
            }

            // Automatically create shipment with Shiprocket
            $result = $this->shiprocketService->createOrder($order);
            
            if ($result && isset($result['order_id']) && isset($result['shipment_id'])) {
                // Success! Update order with tracking details
                $awbCode = $result['awb_code'] ?? null;
                $courierName = $result['courier_name'] ?? 'Shiprocket';
                $trackingUrl = $result['tracking_url'] ?? null;

                $order->update([
                    'tracking_number' => $awbCode,
                    'courier_name' => $courierName,
                    'status' => 'processing', // Update status to processing
                    'delivery_status' => 'in_transit'
                ]);

                // Add status history
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'processing',
                    'comment' => "Automatic courier booking successful. AWB: {$awbCode}, Courier: {$courierName}",
                ]);

                Log::info('Auto courier booking successful', [
                    'order_id' => $order->id,
                    'awb_code' => $awbCode,
                    'courier' => $courierName
                ]);

                return [
                    'success' => true,
                    'message' => "Courier booked automatically. AWB: {$awbCode}",
                    'awb_code' => $awbCode,
                    'courier_name' => $courierName,
                    'tracking_url' => $trackingUrl
                ];
            } else {
                // Booking failed but order is still created
                $errorMessage = $result['message'] ?? 'Unknown error occurred';
                
                Log::error('Auto courier booking failed', [
                    'order_id' => $order->id,
                    'error' => $errorMessage,
                    'response' => $result
                ]);

                // Add status history for manual review
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'comment' => "Automatic courier booking failed: {$errorMessage}. Requires manual review.",
                ]);

                return [
                    'success' => false,
                    'message' => "Automatic courier booking failed: {$errorMessage}. Order will be processed manually.",
                    'requires_manual_review' => true,
                    'error' => $errorMessage
                ];
            }

        } catch (\Exception $e) {
            Log::error('Auto courier booking exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Add status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'comment' => "Automatic courier booking error: {$e->getMessage()}. Requires manual review.",
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred during automatic courier booking. Order will be processed manually.',
                'requires_manual_review' => true,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract pincode from order shipping address
     * 
     * @param Order $order
     * @return string|null
     */
    protected function extractPincode(Order $order)
    {
        // Try to get pincode from shippingAddress relationship first
        if ($order->shippingAddress && $order->shippingAddress->postal_code) {
            return $order->shippingAddress->postal_code;
        }

        // If shipping address is stored as string, try to extract pincode
        if ($order->shipping_address) {
            // Try to extract pincode from address string (format: "Address, City, State - PINCODE")
            if (preg_match('/-?\s*(\d{6})/', $order->shipping_address, $matches)) {
                return $matches[1];
            }
            
            // Try to find 6-digit number (Indian pincode format)
            if (preg_match('/(\d{6})/', $order->shipping_address, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Check if courier service is available for the delivery pincode
     * 
     * @param string $deliveryPincode
     * @param Order $order
     * @return bool
     */
    protected function checkCourierAvailability($deliveryPincode, Order $order)
    {
        try {
            // Check serviceability using Shiprocket API
            $result = $this->shiprocketService->getAvailableCouriers($order);
            
            if ($result && isset($result['data']['available_courier_companies'])) {
                $availableCouriers = $result['data']['available_courier_companies'];
                return !empty($availableCouriers);
            }

            // If API check fails, assume serviceable (will fail at booking stage if not)
            return true;
        } catch (\Exception $e) {
            Log::warning('Courier availability check failed', [
                'pincode' => $deliveryPincode,
                'error' => $e->getMessage()
            ]);
            // Assume serviceable - will fail at booking if not
            return true;
        }
    }

    /**
     * Get best available courier for the order
     * (Can be enhanced to select based on cost, speed, etc.)
     * 
     * @param Order $order
     * @return array|null
     */
    public function getBestCourier(Order $order)
    {
        try {
            $result = $this->shiprocketService->getAvailableCouriers($order);
            
            if ($result && isset($result['data']['available_courier_companies'])) {
                $couriers = $result['data']['available_courier_companies'];
                
                // Sort by estimated delivery days (if available) or use first one
                if (!empty($couriers)) {
                    // For now, return first available courier
                    // Can be enhanced to select based on:
                    // - Lowest cost
                    // - Fastest delivery
                    // - Customer preference
                    return $couriers[0];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Get best courier failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

