<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Share Your Experience</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .product-card {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            display: flex;
            align-items: center;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .review-button {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            margin-top: 10px;
        }
        .stars {
            color: #ffc107;
            font-size: 18px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">VergeFlow</div>
            <h2>How was your recent purchase?</h2>
            <p>We'd love to hear about your experience!</p>
        </div>

        <div class="order-info">
            <strong>Order #{{ $order->id }}</strong><br>
            Delivered on: {{ $order->updated_at->format('M d, Y') }}
        </div>

        <p>Hi {{ $user->name }},</p>
        
        <p>We hope you're enjoying your recent purchase! Your feedback helps other customers make informed decisions and helps us improve our products and service.</p>

        <p>Would you mind taking a few minutes to review the following items?</p>

        @foreach($products as $product)
            <div class="product-card">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                @else
                    <div class="product-image" style="background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="color: #6c757d;"></i>
                    </div>
                @endif
                <div class="product-info">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="stars">
                        ★★★★★
                    </div>
                    <a href="{{ route('products.show', $product) }}?review=1" class="review-button">
                        Write a Review
                    </a>
                </div>
            </div>
        @endforeach

        <div style="background: #e7f3ff; padding: 20px; border-radius: 6px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #0066cc;">Why review?</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Help other customers make informed decisions</li>
                <li>Share your experience with the community</li>
                <li>Help us improve our products and service</li>
                <li>Build trust in our marketplace</li>
            </ul>
        </div>

        <p>Thank you for choosing VergeFlow. We appreciate your business and look forward to serving you again!</p>

        <div class="footer">
            <p>Best regards,<br>The VergeFlow Team</p>
            <p style="font-size: 12px; margin-top: 15px;">
                If you no longer wish to receive these emails, you can 
                <a href="{{ route('profile.notifications') }}" style="color: #007bff;">update your preferences</a>
            </p>
        </div>
    </div>
</body>
</html>
