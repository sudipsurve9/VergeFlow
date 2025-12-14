<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Charge;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout()
    {
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $total = $cartItems->sum(function($item) {
            return $item->total;
        });

        // Get user's saved addresses
        $addresses = Address::where('user_id', Auth::id())->get();
        $defaultShippingAddress = $addresses->where('is_default_shipping', true)->first();
        $defaultBillingAddress = $addresses->where('is_default_billing', true)->first();

        return view('orders.checkout', compact('cartItems', 'total', 'addresses', 'defaultShippingAddress', 'defaultBillingAddress'));
    }

    public function processCheckout(Request $request)
    {
        Log::info('OrderController@processCheckout called', ['user_id' => Auth::id()]);
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        Log::info('Cart items at checkout', ['count' => $cartItems->count(), 'items' => $cartItems->toArray()]);
        
        $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        DB::beginTransaction();
        
        try {
            $total = $cartItems->sum(function($item) {
                return $item->total;
            });

            $order = Order::create([
                'order_number' => 'ORD-' . time(),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'phone' => $request->phone,
                'notes' => $request->notes
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->final_price,
                    'total' => $cartItem->total
                ]);

                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            $cartItems->each(function($item) {
                $item->delete();
            });

            DB::commit();

            // Automatically book courier after order is created
            try {
                // Reload order with relationships
                $order->load(['items.product', 'user']);
                
                $autoBookingService = new \App\Services\AutoCourierBookingService();
                $bookingResult = $autoBookingService->autoBookCourier($order);
                
                if ($bookingResult['success']) {
                    Log::info('Automatic courier booking successful', [
                        'order_id' => $order->id,
                        'awb_code' => $bookingResult['awb_code'] ?? null
                    ]);
                    // Order will be updated with tracking details by the service
                } else {
                    Log::warning('Automatic courier booking failed', [
                        'order_id' => $order->id,
                        'message' => $bookingResult['message'] ?? 'Unknown error'
                    ]);
                    // Order is still created, but requires manual courier booking
                }
            } catch (\Exception $e) {
                // Don't fail the order creation if auto-booking fails
                Log::error('Auto courier booking exception', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('orders.show', $order->id)->with('success', 'Order placed successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage() ?: 'Something went wrong. Please try again.']);
        }
    }

    public function store(Request $request)
    {
        Log::info('OrderController@store called', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        try {
            // Custom validation for multi-tenant context
            $request->validate([
                'shipping_address_id' => 'required|integer',
                'billing_address_id' => 'required|integer',
                'phone' => 'required|string|max:20',
                'payment_method' => 'required|string|in:cod,stripe',
                'notes' => 'nullable|string|max:500',
                'stripeToken' => 'required_if:payment_method,stripe'
            ]);

            $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty');
            }

            // Validate addresses belong to user
            $shippingAddress = Address::where('user_id', Auth::id())
                                    ->where('id', $request->shipping_address_id)
                                    ->first();
            $billingAddress = Address::where('user_id', Auth::id())
                                   ->where('id', $request->billing_address_id)
                                   ->first();

            if (!$shippingAddress || !$billingAddress) {
                return back()->with('error', 'Invalid address selected')->withInput();
            }

            DB::beginTransaction();
            
            $total = $cartItems->sum(function($item) {
                return $item->total;
            });

            // Stripe payment processing
            if ($request->payment_method === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $charge = Charge::create([
                    'amount' => (int)($total * 100), // amount in cents
                    'currency' => 'inr',
                    'description' => 'Order Payment for Vault64',
                    'source' => $request->stripeToken,
                    'metadata' => [
                        'user_id' => Auth::id(),
                    ],
                ]);
                if ($charge->status !== 'succeeded') {
                    throw new \Exception('Payment failed.');
                }
            }

            // Format addresses for storage (using already validated addresses)
            $shipping_address = $shippingAddress->getFormattedAddress();
            $billing_address = $billingAddress->getFormattedAddress();

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'stripe' ? 'paid' : 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $shipping_address . '\nPhone: ' . $request->phone,
                'billing_address' => $billing_address,
                'notes' => $request->notes
            ]);


            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->final_price,
                    'total' => $cartItem->total
                ]);

                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            $cartItems->each(function($item) {
                $item->delete();
            });

            DB::commit();

            // Automatically book courier after order is created
            try {
                $autoBookingService = new \App\Services\AutoCourierBookingService();
                $bookingResult = $autoBookingService->autoBookCourier($order);
                
                if ($bookingResult['success']) {
                    Log::info('Automatic courier booking successful', [
                        'order_id' => $order->id,
                        'awb_code' => $bookingResult['awb_code'] ?? null
                    ]);
                    // Order will be updated with tracking details by the service
                } else {
                    Log::warning('Automatic courier booking failed', [
                        'order_id' => $order->id,
                        'message' => $bookingResult['message'] ?? 'Unknown error'
                    ]);
                    // Order is still created, but requires manual courier booking
                }
            } catch (\Exception $e) {
                // Don't fail the order creation if auto-booking fails
                Log::error('Auto courier booking exception', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('orders.show', $order->id)->with('success', 'Order placed successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Order placement failed: ' . $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with([
            'items.product', 
            'statusHistories' => function($q) { 
                $q->orderBy('created_at'); 
            },
            'shippingAddress',
            'billingAddress'
        ])->findOrFail($id);
        
        // Ensure user can only view their own orders
        if (auth()->id() !== $order->user_id) {
            abort(403, 'Unauthorized access to order.');
        }
        
        return view('orders.show_clean', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order cannot be cancelled');
        }

        DB::beginTransaction();
        
        try {
            $order->update(['status' => 'cancelled']);

            foreach ($order->items as $orderItem) {
                $orderItem->product->increment('stock_quantity', $orderItem->quantity);
            }

            DB::commit();
            return back()->with('success', 'Order cancelled successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Generate a Swiggy-style invoice PDF using raw TCPDF commands for the user
     */
    public function tcpdfInvoice(\App\Models\Order $order)
    {
        // Load order relationships with explicit connection
        $order->load(['items.product', 'payment']);
        
        // In multi-tenant setup, user might be in different database
        // Try to load user from current connection first, then main
        if ($order->user_id) {
            $order->user = \App\Models\User::find($order->user_id);
            
            // If not found in current connection, try main connection
            if (!$order->user) {
                $order->user = \App\Models\User::on('main')->find($order->user_id);
            }
        }
        
        // Debug: Log order data including raw address fields
        $shippingAddressData = $order->shipping_address ? json_decode($order->shipping_address, true) : null;
        $billingAddressData = $order->billing_address ? json_decode($order->billing_address, true) : null;
        
        \Log::info('Order Debug Data', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'user_exists' => $order->user ? true : false,
            'user_name' => $order->user ? $order->user->name : 'null',
            'user_phone' => $order->user ? $order->user->phone : 'null',
            'user_email' => $order->user ? $order->user->email : 'null',
            'user_connection_tried' => 'current and main',
            'shipping_address_raw' => $order->shipping_address,
            'billing_address_raw' => $order->billing_address,
            'items_count' => $order->items->count(),
            'total_amount' => $order->total_amount
        ]);
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Swiggy Limited');
        $pdf->SetAuthor('Swiggy Limited');
        $pdf->SetTitle('Tax Invoice - Order #' . $order->id);
        $pdf->SetSubject('GST Invoice');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, '', '');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Header Section - Swiggy Logo
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(255, 87, 34); // Orange color for Swiggy
        $pdf->Cell(0, 10, 'Swiggy', 0, 1, 'C');
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0); // Black
        $pdf->Cell(0, 8, 'TAX INVOICE', 0, 1, 'C');
        
        $pdf->Ln(5);
        
        // Invoice Details Section - Clean table with proper column boundaries
        
        // Define column widths for perfect alignment - adjusted for longer text
        $col1 = 35; // Label column
        $col2 = 55; // Value column  
        $col3 = 55; // Label column
        $col4 = 35; // Value column
        
        // Row 1: Invoice From and Invoice To headers
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col1, 6, 'Invoice From:', 1, 0, 'L', false);
        
        // Get client company name - force Vault64 for now
        $client = \App\Models\Client::on('main')->where('subdomain', 'vault64')->first();
        
        if (!$client) {
            // Fallback to ID 1 if subdomain lookup fails
            $client = \App\Models\Client::on('main')->find(1);
        }
        
        $companyName = $client ? $client->company_name : 'Vault64 Original Store';
        
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell($col2, 6, substr($companyName, 0, 35), 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col3, 6, 'Invoice To:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $customerName = $order->user ? $order->user->name : 'Customer';
        $pdf->Cell($col4, 6, substr($customerName, 0, 35), 1, 1, 'L', false);
        
        // Row 2: Company name continuation or empty
        $pdf->Cell($col1, 6, '', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell($col2, 6, '', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col3, 6, 'Legal Name:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        // Use the same customer name variable that was set above
        $pdf->Cell($col4, 6, substr($customerName, 0, 35), 1, 1, 'L', false);
        
        // Row 3: PAN and Customer Address
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col1, 6, 'PAN:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $clientPAN = $client ? ($client->getSetting('pan_number') ?? 'Not Available') : 'Not Available';
        $pdf->Cell($col2, 6, $clientPAN, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col3, 6, 'Address:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        
        // Get customer address from order - handle string format addresses
        $customerAddress = '';
        
        // Addresses are stored as strings, not JSON
        if ($order->shipping_address) {
            // Remove phone number from address if present
            $addressParts = explode("\n", $order->shipping_address);
            $customerAddress = trim($addressParts[0]); // Take first line, ignore phone
        } elseif ($order->billing_address) {
            $customerAddress = trim($order->billing_address);
        }
        
        if (empty($customerAddress)) {
            $customerAddress = 'Address not available';
        }
        
        $pdf->Cell($col4, 6, substr($customerAddress, 0, 35), 1, 1, 'L', false);
        
        // Row 4: Email ID and Customer Phone
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col1, 6, 'Email ID:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $clientEmail = $client ? $client->contact_email : 'email@notavailable.com';
        $pdf->Cell($col2, 6, $clientEmail, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col3, 6, 'Phone:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        // Extract customer phone from shipping address or user
        $customerPhone = 'N/A';
        if ($order->user && $order->user->phone) {
            $customerPhone = $order->user->phone;
        } elseif ($order->shipping_address && strpos($order->shipping_address, 'Phone:') !== false) {
            // Extract phone from shipping address string
            $addressParts = explode("\n", $order->shipping_address);
            foreach ($addressParts as $part) {
                if (strpos($part, 'Phone:') !== false) {
                    $customerPhone = trim(str_replace('Phone:', '', $part));
                    break;
                }
            }
        }
        $pdf->Cell($col4, 6, $customerPhone, 1, 1, 'L', false);
        
        // Row 5: GSTIN and empty customer cells
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col1, 6, 'GSTIN:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $clientGSTIN = $client ? ($client->getSetting('gstin_number') ?? 'Not Available') : 'Not Available';
        $pdf->Cell($col2, 6, $clientGSTIN, 1, 0, 'L', false);
        $pdf->Cell($col3, 6, '', 1, 0, 'L', false);
        $pdf->Cell($col4, 6, '', 1, 1, 'L', false);
        
        // Row 6-9: Company Address (4 rows) with proper customer column structure
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col1, 6, 'Address:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        
        // Get client address and split into multiple lines
        $clientAddress = $client ? $client->address : 'Address not available';
        $addressLines = explode(',', $clientAddress);
        $addressLines = array_map('trim', $addressLines);
        
        // First address line
        $firstLine = isset($addressLines[0]) ? substr($addressLines[0], 0, 35) : '';
        $pdf->Cell($col2, 6, $firstLine, 1, 0, 'L', false);
        $pdf->Cell($col3, 6, '', 1, 0, 'L', false);
        $pdf->Cell($col4, 6, '', 1, 1, 'L', false);
        
        // Second address line
        $pdf->Cell($col1, 6, '', 1, 0, 'L', false);
        $secondLine = isset($addressLines[1]) ? substr($addressLines[1], 0, 35) : '';
        $pdf->Cell($col2, 6, $secondLine, 1, 0, 'L', false);
        $pdf->Cell($col3, 6, '', 1, 0, 'L', false);
        $pdf->Cell($col4, 6, '', 1, 1, 'L', false);
        
        // Third address line
        $pdf->Cell($col1, 6, '', 1, 0, 'L', false);
        $thirdLine = isset($addressLines[2]) ? substr($addressLines[2], 0, 35) : '';
        $pdf->Cell($col2, 6, $thirdLine, 1, 0, 'L', false);
        $pdf->Cell($col3, 6, '', 1, 0, 'L', false);
        $pdf->Cell($col4, 6, '', 1, 1, 'L', false);
        
        // Fourth address line
        $pdf->Cell($col1, 6, '', 1, 0, 'L', false);
        $fourthLine = isset($addressLines[3]) ? substr($addressLines[3], 0, 35) : '';
        $pdf->Cell($col2, 6, $fourthLine, 1, 0, 'L', false);
        $pdf->Cell($col3, 6, '', 1, 0, 'L', false);
        $pdf->Cell($col4, 6, '', 1, 1, 'L', false);
        
        // Row 11: Phone and Date
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col1, 6, 'Phone:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $clientPhone = $client ? ($client->contact_phone ?? 'Not Available') : 'Not Available';
        $pdf->Cell($col2, 6, $clientPhone, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col3, 6, 'Date:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($col4, 6, $order->created_at->format('d-m-Y'), 1, 1, 'L', false);
        
        // Row 12: Invoice Number and Whether Reverse Charges Applicable
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col1, 6, 'Invoice Number:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $invoiceNumber = str_pad($order->id, 6, '0', STR_PAD_LEFT) . 'WIMS' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $pdf->Cell($col2, 6, $invoiceNumber, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->Cell($col3, 6, 'Whether Reverse Charges Applicable:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($col4, 6, 'No', 1, 1, 'L', false);
        
        // Row 10: Pincode and Category
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col1, 6, 'Pincode:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $clientPincode = $client ? ($client->getSetting('pincode') ?? 'Not Available') : 'Not Available';
        $pdf->Cell($col2, 6, $clientPincode, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col3, 6, 'Category:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($col4, 6, 'B2C', 1, 1, 'L', false);
        
        // Row 12: Document and Invoice Type
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col1, 6, 'Document:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($col2, 6, 'INV', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col3, 6, 'Invoice Type:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($col4, 6, 'RG', 1, 1, 'L', false);
        
        // Row 13: Invoice No and Whether Reverse Charges
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell($col1, 6, 'Invoice No:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $invoiceNo = str_pad($order->id, 6, '0', STR_PAD_LEFT) . 'WIMS' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $pdf->Cell($col2, 6, $invoiceNo, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->Cell($col3, 6, 'Whether Reverse Charges Applicable:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell($col4, 6, 'No', 1, 1, 'L', false);
        
        // Row 14: Date of Invoice
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($col1, 6, 'Date of Invoice:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($col2, 6, $order->created_at->format('d-m-Y'), 1, 0, 'L', false);
        $pdf->Cell($col3 + $col4, 6, '', 1, 1, 'L', false);
        
        $pdf->Ln(5);
        
        // Items Table Header - Exact match to image
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(15, 8, 'Sr No', 1, 0, 'C', false);
        $pdf->Cell(60, 8, 'Description', 1, 0, 'C', false);
        $pdf->Cell(20, 8, 'HSN', 1, 0, 'C', false);
        $pdf->Cell(25, 8, 'Unit Of Measure', 1, 0, 'C', false);
        $pdf->Cell(15, 8, 'Quantity', 1, 0, 'C', false);
        $pdf->Cell(20, 8, 'Unit Price', 1, 0, 'C', false);
        $pdf->Cell(25, 8, 'Amount(Rs.)', 1, 1, 'C', false);
        
        // Items Table Body - Real order data
        $pdf->SetFont('helvetica', '', 8);
        $srNo = 1;
        $subtotal = 0;
        
        foreach ($order->items as $item) {
            $itemTotal = $item->price * $item->quantity;
            $subtotal += $itemTotal;
            $productName = $item->product ? $item->product->name : 'Product #' . $item->product_id;
            
            $pdf->Cell(15, 8, $srNo, 1, 0, 'C', false);
            $pdf->Cell(60, 8, substr($productName, 0, 25), 1, 0, 'L', false);
            $pdf->Cell(20, 8, '999799', 1, 0, 'C', false);
            $pdf->Cell(25, 8, 'OTH', 1, 0, 'C', false);
            $pdf->Cell(15, 8, $item->quantity, 1, 0, 'C', false);
            $pdf->Cell(20, 8, number_format($item->price, 2), 1, 0, 'R', false);
            $pdf->Cell(25, 8, number_format($itemTotal, 2), 1, 1, 'R', false);
            $srNo++;
        }
        
        // Subtotal row
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(155, 8, 'Subtotal', 1, 0, 'R', false);
        $pdf->Cell(25, 8, number_format($subtotal, 2), 1, 1, 'R', false);
        
        $pdf->Ln(5);
        
        // Tax Breakdown Section - Calculate actual taxes
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 6, 'Tax Breakdown', 0, 1, 'L');
        
        // Calculate taxes from order
        $cgstAmount = $order->cgst_amount ?? 0;
        $sgstAmount = $order->sgst_amount ?? 0;
        $cessAmount = $order->cess_amount ?? 0;
        $totalAmount = $order->total_amount ?? $subtotal;
        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(60, 6, 'CGST (9%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($cgstAmount, 2), 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'SGST/UTGST (9%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($sgstAmount, 2), 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'State CESS (0%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($cessAmount, 2), 1, 1, 'R', false);
        
        $pdf->Ln(3);
        
        // Invoice Total
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(60, 8, 'Invoice Total', 1, 0, 'L', false);
        $pdf->Cell(30, 8, number_format($totalAmount, 2), 1, 1, 'R', false);
        
        $pdf->Ln(10);
        
        // Amount in Words
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 6, 'Invoice total in words', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $amountInWords = ucwords(\App\Helpers\NumberToWords::convert($totalAmount)) . ' Rupees Only';
        $pdf->Cell(0, 6, $amountInWords, 0, 1, 'L');
        
        $pdf->Ln(15);
        
        // Authorized Signature
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 6, 'Authorized Signature', 0, 1, 'R');
        $pdf->Ln(10);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(0, 4, 'Digitally Signed by', 0, 1, 'R');
        $pdf->Cell(0, 4, 'Swiggy Limited', 0, 1, 'R');
        $pdf->Cell(0, 4, $order->created_at->format('d-m-Y'), 0, 1, 'R');
        
        // Output PDF
        $pdf->Output('invoice_order_' . $order->id . '.pdf', 'I');
        exit;
    }
}
