<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\ApiIntegration;

class ShiprocketService
{
    protected $loginUrl;
    protected $trackingUrl;
    protected $email;
    protected $password;
    protected $token;

    public function __construct()
    {
        $integration = ApiIntegration::where('type', 'shiprocket')->latest()->first();
        
        if ($integration) {
            $this->email = $integration->email;
            $this->password = $integration->password;
            $this->loginUrl = $integration->meta['login_url'] ?? 'https://apiv2.shiprocket.in/v1/external/auth/login';
            $this->trackingUrl = $integration->meta['tracking_url'] ?? 'https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb}';
        } else {
            // Fallback to config
            $this->email = config('services.shiprocket.email');
            $this->password = config('services.shiprocket.password');
            $this->loginUrl = config('services.shiprocket.login_url', 'https://apiv2.shiprocket.in/v1/external/auth/login');
            $this->trackingUrl = config('services.shiprocket.tracking_url', 'https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb}');
        }
    }

    protected function authenticate()
    {
        // Cache token for 50 minutes
        return Cache::remember('shiprocket_token', 3000, function () {
            try {
                $response = Http::post($this->loginUrl, [
                    'email' => $this->email,
                    'password' => $this->password,
                ]);
                
                if ($response->successful() && isset($response['token'])) {
                    return $response['token'];
                }
                
                \Log::error('Shiprocket authentication failed', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return null;
            } catch (\Exception $e) {
                \Log::error('Shiprocket authentication error', [
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    public function getTrackingStatus($awb)
    {
        if (!$this->email || !$this->password) {
            \Log::warning('Shiprocket credentials not configured');
            return null;
        }
        
        $token = $this->authenticate();
        if (!$token) {
            \Log::error('Shiprocket token not available');
            return null;
        }
        
        try {
            $trackingUrl = str_replace('{awb}', $awb, $this->trackingUrl);
            $response = Http::withToken($token)
                ->get($trackingUrl);
                
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['tracking_data']['shipment_track'][0]['current_status'])) {
                    return $data['tracking_data']['shipment_track'][0]['current_status'];
                }
            }
            
            \Log::warning('Shiprocket tracking failed', [
                'awb' => $awb,
                'response' => $response->json(),
                'status' => $response->status()
            ]);
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Shiprocket tracking error', [
                'awb' => $awb,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getTrackingDetails($awb)
    {
        if (!$this->email || !$this->password) {
            return null;
        }
        
        $token = $this->authenticate();
        if (!$token) {
            return null;
        }
        
        try {
            $trackingUrl = str_replace('{awb}', $awb, $this->trackingUrl);
            $response = Http::withToken($token)
                ->get($trackingUrl);
                
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Shiprocket tracking details error', [
                'awb' => $awb,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Place an order on Shiprocket
     * @param \App\Models\Order $order
     * @return array|null
     */
    public function createOrder($order)
    {
        $token = $this->authenticate();
        if (!$token) {
            \Log::error('Shiprocket: No token for order creation');
            return null;
        }

        // Extract address details from order
        $addressData = $this->extractAddressFromOrder($order);
        
        // Prepare order data as per Shiprocket API
        $orderData = [
            'order_id' => $order->id,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'pickup_location' => 'Default', // You may want to make this dynamic
            'billing_customer_name' => $addressData['name'],
            'billing_last_name' => $addressData['last_name'] ?? '',
            'billing_address' => $addressData['address'],
            'billing_city' => $addressData['city'],
            'billing_pincode' => $addressData['pincode'],
            'billing_state' => $addressData['state'],
            'billing_country' => $addressData['country'] ?? 'India',
            'billing_email' => $order->user->email ?? '',
            'billing_phone' => $addressData['phone'],
            'shipping_is_billing' => true,
            'order_items' => $order->items->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'sku' => $item->product->sku ?? $item->product_id,
                    'units' => $item->quantity,
                    'selling_price' => $item->price,
                ];
            })->toArray(),
            'payment_method' => $order->payment->method ?? 'Prepaid',
            'sub_total' => $order->total_amount,
            'length' => 10,
            'breadth' => 10,
            'height' => 10,
            'weight' => 1,
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', $orderData);
            if ($response->successful()) {
                return $response->json();
            }
            \Log::error('Shiprocket order creation failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Shiprocket order creation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get available courier companies for an order
     * @param \App\Models\Order $order
     * @return array|null
     */
    public function getAvailableCouriers($order)
    {
        $token = $this->authenticate();
        if (!$token) {
            \Log::error('Shiprocket: No token for courier check');
            return null;
        }

        // Prepare data for serviceability API
        $payload = [
            'pickup_postcode' => '110001', // Replace with your warehouse/pickup pincode
            'delivery_postcode' => $order->shippingAddress->pincode ?? '',
            'cod' => $order->payment->method === 'COD' ? 1 : 0,
            'weight' => 1, // You may want to make this dynamic
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->get('https://apiv2.shiprocket.in/v1/external/courier/serviceability/', $payload);
            if ($response->successful()) {
                return $response->json();
            }
            \Log::error('Shiprocket courier check failed', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Shiprocket courier check error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Extract address details from order
     * Handles both Address model relationship and string format
     * 
     * @param \App\Models\Order $order
     * @return array
     */
    protected function extractAddressFromOrder($order)
    {
        // Try to get address from relationship first
        if ($order->shippingAddress) {
            return [
                'name' => $order->shippingAddress->name ?? $order->user->name ?? 'Customer',
                'last_name' => $order->shippingAddress->last_name ?? '',
                'address' => trim(($order->shippingAddress->address_line_1 ?? '') . ' ' . ($order->shippingAddress->address_line_2 ?? '')),
                'city' => $order->shippingAddress->city ?? '',
                'pincode' => $order->shippingAddress->postal_code ?? '',
                'state' => $order->shippingAddress->state ?? '',
                'country' => $order->shippingAddress->country ?? 'India',
                'phone' => $order->shippingAddress->phone ?? '',
            ];
        }

        // If address is stored as string, try to parse it
        if ($order->shipping_address) {
            // Try to extract pincode
            $pincode = '';
            if (preg_match('/-?\s*(\d{6})/', $order->shipping_address, $matches)) {
                $pincode = $matches[1];
            }
            
            // Extract phone if present
            $phone = '';
            if (preg_match('/Phone:\s*([\d\s\+\-]+)/i', $order->shipping_address, $matches)) {
                $phone = trim($matches[1]);
            }
            
            // For string addresses, we'll use the full address
            // This is a fallback - ideally addresses should be in Address model
            return [
                'name' => $order->user->name ?? 'Customer',
                'last_name' => '',
                'address' => $order->shipping_address,
                'city' => '',
                'pincode' => $pincode,
                'state' => '',
                'country' => 'India',
                'phone' => $phone,
            ];
        }

        // Fallback to user data
        return [
            'name' => $order->user->name ?? 'Customer',
            'last_name' => '',
            'address' => '',
            'city' => '',
            'pincode' => '',
            'state' => '',
            'country' => 'India',
            'phone' => $order->user->phone ?? '',
        ];
    }
} 