<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\OrderStatusChanged;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'items.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Orders are typically created through the checkout process
        return redirect()->route('admin.orders.index')
            ->with('info', 'Orders are created through the checkout process.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Orders are typically created through the checkout process
        return redirect()->route('admin.orders.index')
            ->with('info', 'Orders are created through the checkout process.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load([
            'user', 
            'items.product', 
            'payment', 
            'statusHistory.user',
            'shippingAddress',
            'billingAddress'
        ]);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $order->status;
        $data = $request->all();

        DB::transaction(function () use ($order, $data, $oldStatus, $request) {
            $order->update($data);

            // Create status history entry
            if ($oldStatus !== $data['status']) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $data['status'],
                    'notes' => $request->notes,
                    'user_id' => auth()->id(),
                ]);
                // Send notification to user
                $order->user->notify(new OrderStatusChanged($order, $oldStatus, $data['status']));
            }
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Check if order has payments
        if ($order->payment()->exists()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cannot delete order with associated payments.');
        }

        // Delete related records
        $order->items()->delete();
        $order->statusHistory()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url',
        ]);

        $oldStatus = $order->status;
        $data = $request->all();

        DB::transaction(function () use ($order, $data, $oldStatus, $request) {
            $order->update([
                'status' => $data['status'],
                'tracking_number' => $data['tracking_number'] ?? $order->tracking_number,
                'tracking_url' => $data['tracking_url'] ?? $order->tracking_url,
            ]);

            // Create status history entry
            if ($oldStatus !== $data['status']) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $data['status'],
                    'notes' => $request->notes,
                    'user_id' => auth()->id(),
                ]);
                // Send notification to user
                $order->user->notify(new OrderStatusChanged($order, $oldStatus, $data['status']));
            }
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'Order cannot be cancelled in its current status.');
        }

        DB::transaction(function () use ($order) {
            $oldStatus = $order->status;
            $order->update(['status' => 'cancelled']);

            // Restore stock quantities
            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            // Create status history entry
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'notes' => 'Order cancelled by admin',
                'user_id' => auth()->id(),
            ]);
            // Send notification to user
            $order->user->notify(new OrderStatusChanged($order, $oldStatus, 'cancelled'));
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }

    /**
     * Refund order
     */
    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $order->total_amount,
            'refund_reason' => 'required|string',
        ]);

        if ($order->status !== 'delivered') {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'Only delivered orders can be refunded.');
        }

        DB::transaction(function () use ($order, $request) {
            $oldStatus = $order->status;
            $order->update([
                'status' => 'refunded',
                'refund_amount' => $request->refund_amount,
                'refund_reason' => $request->refund_reason,
            ]);

            // Create status history entry
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'refunded',
                'notes' => 'Refund: ' . $request->refund_reason,
                'user_id' => auth()->id(),
            ]);
            // Send notification to user
            $order->user->notify(new OrderStatusChanged($order, $oldStatus, 'refunded'));
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order refunded successfully.');
    }

    /**
     * Print invoice
     */
    public function printInvoice(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'shippingAddress', 'billingAddress']);
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Export orders
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $query = Order::with(['user', 'items.product']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // TODO: Implement export logic based on format
        return redirect()->route('admin.orders.index')
            ->with('info', 'Export feature will be implemented soon.');
    }

    /**
     * Place order on Shiprocket
     */
    public function placeOnShiprocket($id)
    {
        $order = \App\Models\Order::with(['user', 'items.product', 'payment', 'shippingAddress'])->findOrFail($id);
        $shiprocket = new \App\Services\ShiprocketService();
        $result = $shiprocket->createOrder($order);
        if ($result && isset($result['awb_code'])) {
            $order->tracking_number = $result['awb_code'];
            $order->save();
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order placed on Shiprocket. AWB: ' . $result['awb_code']);
        } elseif ($result && isset($result['message'])) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Shiprocket: ' . $result['message']);
        } else {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to place order on Shiprocket.');
        }
    }

    /**
     * Check available Shiprocket couriers for an order (AJAX)
     */
    public function checkShiprocketCouriers($id)
    {
        $order = \App\Models\Order::with(['user', 'items.product', 'payment', 'shippingAddress'])->findOrFail($id);
        $shiprocket = new \App\Services\ShiprocketService();
        $result = $shiprocket->getAvailableCouriers($order);
        if ($result && isset($result['data']['available_courier_companies'])) {
            return response()->json([
                'success' => true,
                'couriers' => $result['data']['available_courier_companies'],
            ]);
        } elseif ($result && isset($result['message'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch courier companies from Shiprocket.'
            ]);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoicePdf($id)
    {
        $order = \App\Models\Order::with(['user', 'items.product', 'payment', 'shippingAddress'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        $filename = 'invoice_order_' . $order->id . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate a custom invoice PDF using TCPDF
     */
    public function tcpdfInvoice(Order $order)
    {
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
