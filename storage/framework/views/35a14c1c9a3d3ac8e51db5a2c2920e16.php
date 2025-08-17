<?php
$orderUser = $order->user;
$shipping = $order->shippingAddress;
$billing = $order->billingAddress;
$payment = $order->payment;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            background: #fff;
        }
        
        /* Swiggy-style Header */
        .invoice-header {
            text-align: center;
            padding: 20px 15px;
            border-bottom: 2px solid #000;
            background: #fff;
        }
        
        .company-logo {
            font-size: 28px;
            font-weight: bold;
            color: #ff5722;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0 0 0;
            color: #000;
        }
        
        /* Invoice Details Grid */
        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .invoice-details td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            line-height: 1.3;
        }
        
        .detail-label {
            font-weight: bold;
            width: 20%;
        }
        
        .detail-value {
            width: 30%;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 10px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            line-height: 1.2;
        }
        
        .items-table th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }
        
        .items-table .description {
            text-align: left;
            width: 35%;
            padding: 6px 8px;
        }
        
        .items-table .hsn {
            width: 10%;
        }
        
        .items-table .unit-measure {
            width: 12%;
        }
        
        .items-table .quantity {
            width: 8%;
        }
        
        .items-table .unit-price {
            width: 10%;
        }
        
        .items-table .amount {
            width: 10%;
        }
        
        .items-table .discount {
            width: 8%;
        }
        
        .items-table .net-amount {
            width: 12%;
        }
        
        /* Tax Breakdown Section */
        .tax-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 10px;
        }
        
        .tax-section td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .tax-breakdown {
            width: 60%;
        }
        
        .tax-summary {
            width: 40%;
            text-align: right;
        }
        
        .tax-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        /* Footer sections */
        .footer-section {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .footer-section td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
        }
        
        .amount-words {
            width: 50%;
            font-weight: bold;
        }
        
        .signature-section {
            width: 50%;
            text-align: right;
        }
        
        /* Utility Classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Swiggy-style Header -->
        <div class="invoice-header">
            <div class="company-logo">Swiggy</div>
            <div class="invoice-title">TAX INVOICE</div>
        </div>
        
        <!-- Invoice Details Grid -->
        <table class="invoice-details">
            <tr>
                <td class="detail-label">Invoice From:</td>
                <td class="detail-value">
                    Swiggy Limited (formerly known as Bundl<br>
                    Technologies Private Limited)
                </td>
                <td class="detail-label">Invoice To:</td>
                <td class="detail-value">Customer</td>
            </tr>
            <tr>
                <td class="detail-label">PAN:</td>
                <td class="detail-value">AAFCB7706D</td>
                <td class="detail-label">Legal Name:</td>
                <td class="detail-value">
                    <?php if($orderUser): ?>
                        <?php echo e($orderUser->name); ?>

                    <?php else: ?>
                        Customer
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="detail-label">Email ID:</td>
                <td class="detail-value">invoicing@swiggy.in</td>
                <td class="detail-label">Address:</td>
                <td class="detail-value">
                    <?php if($billing): ?>
                        <?php echo e($billing->address_line_1); ?><br>
                        <?php if($billing->address_line_2): ?><?php echo e($billing->address_line_2); ?><br><?php endif; ?>
                        <?php echo e($billing->city); ?>, <?php echo e($billing->state); ?><br>
                        <?php echo e($billing->postal_code); ?>, <?php echo e($billing->country); ?>

                    <?php else: ?>
                        <?php echo nl2br(e($order->billing_address)); ?>

                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="detail-label">GSTIN:</td>
                <td class="detail-value">29AAFCB7706D1ZU</td>
                <td class="detail-label"></td>
                <td class="detail-value"></td>
            </tr>
            <tr>
                <td class="detail-label">Address:</td>
                <td class="detail-value">
                    No. 55, Sy No 8-14, Ground Floor, I & J Block,<br>
                    Embassy Tech Village, Outer Ring Road,<br>
                    Devarabisanahalli, Varthur Hobli, Bengaluru<br>
                    East Taluk, Bengaluru, Karnataka, 560103
                </td>
                <td class="detail-label">Category:</td>
                <td class="detail-value">B2C</td>
            </tr>
            <tr>
                <td class="detail-label">Pincode:</td>
                <td class="detail-value">560103</td>
                <td class="detail-label">Transaction Type:</td>
                <td class="detail-value">REG</td>
            </tr>
            <tr>
                <td class="detail-label">State Code:</td>
                <td class="detail-value">29</td>
                <td class="detail-label">Invoice Type:</td>
                <td class="detail-value">RG</td>
            </tr>
            <tr>
                <td class="detail-label">Document:</td>
                <td class="detail-value">INV</td>
                <td class="detail-label">Whether Reverse Charges Applicable:</td>
                <td class="detail-value">No</td>
            </tr>
            <tr>
                <td class="detail-label">Invoice No:</td>
                <td class="detail-value"><?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?>WIMS<?php echo e(str_pad($order->id, 5, '0', STR_PAD_LEFT)); ?></td>
                <td class="detail-label"></td>
                <td class="detail-value"></td>
            </tr>
            <tr>
                <td class="detail-label">Date of Invoice:</td>
                <td class="detail-value"><?php echo e($order->created_at->format('d-m-Y')); ?></td>
                <td class="detail-label"></td>
                <td class="detail-value"></td>
            </tr>
        </table>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th class="description">Description</th>
                    <th class="hsn">HSN</th>
                    <th class="unit-measure">Unit Of Measure</th>
                    <th class="quantity">Quantity</th>
                    <th class="unit-price">Unit Price</th>
                    <th class="amount">Amount(Rs.)</th>
                    <th class="discount">Discount</th>
                    <th class="net-amount">Net Assessable Value(Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $itemTotal = $item->price * $item->quantity;
                ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td class="description"><?php echo e($item->product->name ?? 'Handling Fees for Order ' . $order->id); ?></td>
                    <td>999799</td>
                    <td>OTH</td>
                    <td><?php echo e($item->quantity); ?></td>
                    <td><?php echo e(number_format($item->price, 2)); ?></td>
                    <td><?php echo e(number_format($itemTotal, 2)); ?></td>
                    <td>0.00</td>
                    <td><?php echo e(number_format($itemTotal, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td colspan="8" class="text-right font-bold">Subtotal</td>
                    <td class="text-right font-bold"><?php echo e(number_format($order->subtotal_amount ?? $order->total_amount, 2)); ?></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Tax Breakdown Section -->
        <table class="tax-section">
            <tr>
                <td class="tax-breakdown">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none; padding: 5px;"><strong>HSN Code</strong></td>
                            <td style="border: none; padding: 5px;"><strong>Code Description</strong></td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px;">999799</td>
                            <td style="border: none; padding: 5px;">Other Services</td>
                        </tr>
                    </table>
                </td>
                <td class="tax-summary">
                    <div><strong>Taxes</strong></div>
                    <div class="tax-row">
                        <span>CGST</span>
                        <span>9%</span>
                        <span><?php echo e(number_format(($order->total_amount - ($order->subtotal_amount ?? $order->total_amount)) / 2, 2)); ?></span>
                    </div>
                    <div class="tax-row">
                        <span>SGST/UTGST</span>
                        <span>9%</span>
                        <span><?php echo e(number_format(($order->total_amount - ($order->subtotal_amount ?? $order->total_amount)) / 2, 2)); ?></span>
                    </div>
                    <div class="tax-row">
                        <span>State CESS</span>
                        <span>0%</span>
                        <span>0.00</span>
                    </div>
                    <div class="tax-row font-bold">
                        <span>Total taxes</span>
                        <span></span>
                        <span><?php echo e(number_format($order->total_amount - ($order->subtotal_amount ?? $order->total_amount), 2)); ?></span>
                    </div>
                    <div class="tax-row font-bold" style="border-top: 1px solid #000; margin-top: 5px; padding-top: 5px;">
                        <span>Invoice Total</span>
                        <span></span>
                        <span><?php echo e(number_format($order->total_amount, 2)); ?></span>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Footer Section -->
        <table class="footer-section">
            <tr>
                <td class="amount-words">
                    <strong>Invoice total in words</strong><br>
                    <?php echo e(ucwords(\App\Helpers\NumberToWords::convert($order->total_amount))); ?> Rupees Only
                </td>
                <td class="signature-section">
                    <strong>Authorized Signature</strong><br><br><br>
                    <div style="font-size: 8px;">
                        Digitally Signed by<br>
                        Swiggy Limited<br>
                        <?php echo e($order->created_at->format('d-m-Y')); ?>

                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/admin/orders/tcpdf_invoice.blade.php ENDPATH**/ ?>