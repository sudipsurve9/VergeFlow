<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        
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
        $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'stripeToken' => 'required_if:payment_method,stripe',
        ]);

        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        DB::beginTransaction();
        
        try {
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

            $shipping_address = $request->shipping_address ?? (
                $request->address_line1 . ', ' .
                $request->address_line2 . ', ' .
                ($request->landmark ? $request->landmark . ', ' : '') .
                $request->locality . ', ' .
                $request->city . ', ' .
                $request->state . ' - ' .
                $request->pincode
            );

            $billing_address = $request->billing_address ?? (
                $request->billing_address_line1 . ', ' .
                $request->billing_address_line2 . ', ' .
                ($request->billing_landmark ? $request->billing_landmark . ', ' : '') .
                $request->billing_locality . ', ' .
                $request->billing_city . ', ' .
                $request->billing_state . ' - ' .
                $request->billing_pincode
            );

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'stripe' ? 'paid' : 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $shipping_address,
                'billing_address' => $billing_address,
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
            return back()->with('error', $e->getMessage() ?: 'Something went wrong. Please try again.');
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'statusHistories' => function($q) { $q->orderBy('created_at'); }])->findOrFail($id);
        return view('orders.show', compact('order'));
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
     * Generate a custom invoice PDF using TCPDF for the user
     */
    public function tcpdfInvoice(\App\Models\Order $order)
    {
        // Ensure the logged-in user owns the order
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }
        $order->load(['user', 'items.product', 'payment', 'shippingAddress', 'billingAddress']);
        $pdf = new \TCPDF();
        $pdf->SetCreator('Vault 64');
        $pdf->SetAuthor('Vault 64');
        $pdf->SetTitle('Invoice Order #' . $order->id);
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();

        $html = view('admin.orders.tcpdf_invoice', compact('order'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('invoice_order_' . $order->id . '.pdf', 'I');
        exit;
    }
}
