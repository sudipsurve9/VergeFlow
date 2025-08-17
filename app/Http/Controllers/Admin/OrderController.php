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
     * Generate a custom invoice PDF using raw TCPDF commands
     */
    public function tcpdfInvoice(Order $order)
    {
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
        
        // Header Section - Swiggy Logo
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(255, 87, 34); // Orange color for Swiggy
        $pdf->Cell(0, 10, 'Swiggy', 0, 1, 'C');
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0); // Black
        $pdf->Cell(0, 8, 'TAX INVOICE', 0, 1, 'C');
        
        $pdf->Ln(5);
        
        // Invoice Details Section - Exact layout from image
        
        // Row 1: Invoice From and Invoice To headers
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Invoice From:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'Swiggy Limited (formerly known as Bundl', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Invoice To:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'Customer', 1, 1, 'L', false);
        
        // Row 2: Continue company name and Legal Name
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, '', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'Technologies Private Limited)', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Legal Name:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $customerName = $order->user ? $order->user->name : 'Customer';
        $pdf->Cell(45, 6, $customerName, 1, 1, 'L', false);
        
        // Row 3: PAN and Customer Address
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'PAN:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'AAFCB7706D', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Address:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'tetst, test, ttre, teet, teet - 421202', 1, 1, 'L', false);
        
        // Row 4: Email ID
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Email ID:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'invoicing@swiggy.in', 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
        // Row 5: GSTIN
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'GSTIN:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, '29AAFCB7706D1ZU', 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
        // Row 6: Address (multi-line) - start
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Address:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'No. 55, Sy No 8-14, Ground Floor, I & J', 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
        // Row 7: Address continuation
        $pdf->Cell(45, 6, '', 1, 0, 'L', false);
        $pdf->Cell(45, 6, 'Block, Embassy Tech Village, Outer Ring Road,', 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
        // Row 8: Address continuation
        $pdf->Cell(45, 6, '', 1, 0, 'L', false);
        $pdf->Cell(45, 6, 'Devarabisanahalli, Varthur Hobli, Bengaluru', 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
        // Row 9: Address end
        $pdf->Cell(45, 6, '', 1, 0, 'L', false);
        $pdf->Cell(45, 6, 'East Taluk, Bengaluru, Karnataka, 560103', 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
        // Row 10: Pincode and Category
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Pincode:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, '560103', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Category:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'B2C', 1, 1, 'L', false);
        
        // Row 11: State Code and Transaction Type
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'State Code:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, '29', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Transaction Type:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'REG', 1, 1, 'L', false);
        
        // Row 12: Document and Invoice Type
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Document:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'INV', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Invoice Type:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'RG', 1, 1, 'L', false);
        
        // Row 13: Invoice No and Whether Reverse Charges
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Invoice No:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $invoiceNo = str_pad($order->id, 6, '0', STR_PAD_LEFT) . 'WIMS' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $pdf->Cell(45, 6, $invoiceNo, 1, 0, 'L', false);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Whether Reverse Charges Applicable:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, 'No', 1, 1, 'L', false);
        
        // Row 14: Date of Invoice
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, 6, 'Date of Invoice:', 1, 0, 'L', false);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(45, 6, $order->created_at->format('d-m-Y'), 1, 0, 'L', false);
        $pdf->Cell(90, 6, '', 1, 1, 'L', false);
        
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
        
        // Items Table Body - Match exact data from image
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(15, 8, '1', 1, 0, 'C', false);
        $pdf->Cell(60, 8, '1967 Camaro SS', 1, 0, 'L', false);
        $pdf->Cell(20, 8, '999799', 1, 0, 'C', false);
        $pdf->Cell(25, 8, 'OTH', 1, 0, 'C', false);
        $pdf->Cell(15, 8, '1', 1, 0, 'C', false);
        $pdf->Cell(20, 8, '249.99', 1, 0, 'R', false);
        $pdf->Cell(25, 8, '249.99', 1, 1, 'R', false);
        
        // Subtotal row
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(155, 8, 'Subtotal', 1, 0, 'R', false);
        $pdf->Cell(25, 8, '249.99', 1, 1, 'R', false);
        
        $pdf->Ln(5);
        
        // Tax Breakdown Section - Exact layout from image
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 6, 'Tax Breakdown', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(60, 6, 'CGST (9%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, '0.00', 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'SGST/UTGST (9%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, '0.00', 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'State CESS (0%)', 1, 0, 'L', false);
        $pdf->Cell(30, 6, '0.00', 1, 1, 'R', false);
        
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(60, 6, 'Total taxes', 1, 0, 'L', false);
        $pdf->Cell(30, 6, '0.00', 1, 1, 'R', false);
        
        $pdf->Cell(60, 6, 'Invoice Total', 1, 0, 'L', false);
        $pdf->Cell(30, 6, '249.99', 1, 1, 'R', false);
        
        $pdf->Ln(10);
        
        // Amount in Words
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 6, 'Invoice total in words', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $amountInWords = ucwords(\App\Helpers\NumberToWords::convert($order->total_amount)) . ' Rupees Only';
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
