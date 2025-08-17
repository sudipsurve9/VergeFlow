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
        // Ensure the logged-in user owns the order
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }
        
        $order->load(['user', 'items.product', 'payment', 'shippingAddress', 'billingAddress']);
        
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
        
        // Header Section - Company Logo and Title
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(255, 87, 34); // Orange color for Swiggy
        $pdf->Cell(0, 15, 'Swiggy', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 0); // Black
        $pdf->Cell(0, 10, 'TAX INVOICE', 0, 1, 'C');
        
        // Add border line
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(5);
        
        // Invoice Details Section
        $y_start = $pdf->GetY();
        
        // Left column - Invoice From
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(15, $y_start);
        $pdf->Cell(40, 6, 'Invoice From:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(50, 6, 'Swiggy Limited (formerly known as Bundl', 1, 1, 'L', false);
        
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->Cell(40, 6, '', 1, 0, 'L', false);
        $pdf->Cell(50, 6, 'Technologies Private Limited)', 1, 1, 'L', false);
        
        // PAN
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 6, 'PAN:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(50, 6, 'AAFCB7706D', 1, 1, 'L', false);
        
        // Email ID
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 6, 'Email ID:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(50, 6, 'invoicing@swiggy.in', 1, 1, 'L', false);
        
        // GSTIN
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 6, 'GSTIN:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(50, 6, '29AAFCB7706D1ZU', 1, 1, 'L', false);
        
        // Address
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 12, 'Address:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->MultiCell(50, 6, "No. 55, Sy No 8-14, Ground Floor, I & J Block,\nEmbassy Tech Village, Outer Ring Road,\nDevarabisanahalli, Varthur Hobli, Bengaluru\nEast Taluk, Bengaluru, Karnataka, 560103", 1, 'L', false);
        
        // Right column - Invoice To
        $pdf->SetXY(105, $y_start);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 6, 'Invoice To:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(50, 6, 'Customer', 1, 1, 'L', false);
        
        // Legal Name
        $pdf->SetXY(105, $y_start + 6);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 6, 'Legal Name:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 8);
        $customerName = $order->user ? $order->user->name : 'Customer';
        $pdf->Cell(50, 6, $customerName, 1, 1, 'L', false);
        
        // Customer Address
        $pdf->SetXY(105, $y_start + 12);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 18, 'Address:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        
        $customerAddress = '';
        if ($order->billingAddress) {
            $customerAddress = $order->billingAddress->address_line_1 . "\n";
            if ($order->billingAddress->address_line_2) {
                $customerAddress .= $order->billingAddress->address_line_2 . "\n";
            }
            $customerAddress .= $order->billingAddress->city . ", " . $order->billingAddress->state . "\n";
            $customerAddress .= $order->billingAddress->postal_code . ", " . $order->billingAddress->country;
        } else {
            $customerAddress = str_replace('<br>', "\n", strip_tags($order->billing_address));
        }
        
        $pdf->MultiCell(50, 6, $customerAddress, 1, 'L', false);
        
        // Additional details
        $current_y = max($pdf->GetY(), $y_start + 30);
        $pdf->SetXY(15, $current_y);
        
        // Category, Transaction Type, etc.
        $details = [
            ['Pincode:', '560103', 'Category:', 'B2C'],
            ['State Code:', '29', 'Transaction Type:', 'REG'],
            ['Document:', 'INV', 'Invoice Type:', 'RG'],
            ['Invoice No:', str_pad($order->id, 6, '0', STR_PAD_LEFT) . 'WIMS' . str_pad($order->id, 5, '0', STR_PAD_LEFT), 'Whether Reverse Charges Applicable:', 'No'],
            ['Date of Invoice:', $order->created_at->format('d-m-Y'), '', '']
        ];
        
        foreach ($details as $row) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(40, 6, $row[0], 1, 0, 'L', false);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(50, 6, $row[1], 1, 0, 'L', false);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(50, 6, $row[2], 1, 0, 'L', false);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(45, 6, $row[3], 1, 1, 'L', false);
        }
        
        $pdf->Ln(5);
        
        // Items Table Header
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(15, 8, 'Sr No', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Description', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'HSN', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Unit Of Measure', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Unit Price', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Amount(Rs.)', 1, 1, 'C', true);
        
        // Items Table Body
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetFillColor(255, 255, 255);
        
        foreach ($order->items as $index => $item) {
            $itemTotal = $item->price * $item->quantity;
            $productName = $item->product->name ?? 'Handling Fees for Order ' . $order->id;
            
            $pdf->Cell(15, 6, ($index + 1), 1, 0, 'C', false);
            $pdf->Cell(60, 6, substr($productName, 0, 40), 1, 0, 'L', false);
            $pdf->Cell(20, 6, '999799', 1, 0, 'C', false);
            $pdf->Cell(25, 6, 'OTH', 1, 0, 'C', false);
            $pdf->Cell(15, 6, $item->quantity, 1, 0, 'C', false);
            $pdf->Cell(20, 6, number_format($item->price, 2), 1, 0, 'R', false);
            $pdf->Cell(20, 6, number_format($itemTotal, 2), 1, 1, 'R', false);
        }
        
        // Subtotal
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(155, 6, 'Subtotal', 1, 0, 'R', false);
        $pdf->Cell(20, 6, number_format($order->subtotal_amount ?? $order->total_amount, 2), 1, 1, 'R', false);
        
        $pdf->Ln(5);
        
        // Tax Breakdown Section
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'Tax Breakdown', 0, 1, 'L');
        
        $taxAmount = $order->total_amount - ($order->subtotal_amount ?? $order->total_amount);
        $cgstAmount = $taxAmount / 2;
        $sgstAmount = $taxAmount / 2;
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(60, 6, 'CGST (9%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($cgstAmount, 2), 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'SGST/UTGST (9%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($sgstAmount, 2), 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'State CESS (0%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, '0.00', 1, 1, 'R', false);
        
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(60, 6, 'Total taxes', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($taxAmount, 2), 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'Invoice Total', 1, 0, 'L', false);
        $pdf->Cell(30, 6, number_format($order->total_amount, 2), 1, 1, 'R', false);
        
        $pdf->Ln(10);
        
        // Amount in Words
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Invoice total in words', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $amountInWords = ucwords(\App\Helpers\NumberToWords::convert($order->total_amount)) . ' Rupees Only';
        $pdf->Cell(0, 6, $amountInWords, 0, 1, 'L');
        
        $pdf->Ln(10);
        
        // Authorized Signature
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Authorized Signature', 0, 1, 'R');
        $pdf->Ln(15);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(0, 4, 'Digitally Signed by', 0, 1, 'R');
        $pdf->Cell(0, 4, 'Swiggy Limited', 0, 1, 'R');
        $pdf->Cell(0, 4, $order->created_at->format('d-m-Y'), 0, 1, 'R');
        
        // Output PDF
        $pdf->Output('invoice_order_' . $order->id . '.pdf', 'I');
        exit;
    }
}
