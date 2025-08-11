<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

try {
    echo "=== PRODUCT IMAGE DEBUG ===\n\n";
    
    // Set client context for multi-tenant
    session(['client_id' => 1]);
    
    // Get the order with items from client database
    $order = Order::on('mysql')->with(['items.product'])->find(5);
    
    if (!$order) {
        echo "❌ Order #5 not found\n";
        exit;
    }
    
    echo "✅ Order #5 found with " . $order->items->count() . " items\n\n";
    
    foreach ($order->items as $index => $item) {
        echo "--- Item " . ($index + 1) . " ---\n";
        
        if ($item->product) {
            echo "Product ID: " . $item->product->id . "\n";
            echo "Product Name: " . $item->product->name . "\n";
            echo "Product Image Field: " . ($item->product->image ?? 'NULL') . "\n";
            
            if ($item->product->image) {
                $imagePath = $item->product->image;
                $fullPath = public_path('storage/' . $imagePath);
                $assetUrl = asset('storage/' . $imagePath);
                
                echo "Image Path: " . $imagePath . "\n";
                echo "Full File Path: " . $fullPath . "\n";
                echo "Asset URL: " . $assetUrl . "\n";
                echo "File Exists: " . (file_exists($fullPath) ? "✅ YES" : "❌ NO") . "\n";
                
                if (file_exists($fullPath)) {
                    echo "File Size: " . filesize($fullPath) . " bytes\n";
                } else {
                    // Check if file exists in different locations
                    $altPaths = [
                        public_path($imagePath),
                        storage_path('app/public/' . $imagePath),
                        public_path('images/' . $imagePath),
                        public_path('uploads/' . $imagePath)
                    ];
                    
                    echo "Checking alternative paths:\n";
                    foreach ($altPaths as $altPath) {
                        echo "  " . $altPath . " - " . (file_exists($altPath) ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
                    }
                }
            } else {
                echo "❌ No image field set for this product\n";
            }
        } else {
            echo "❌ Product not found for this item\n";
        }
        
        echo "\n";
    }
    
    // Check storage link
    echo "--- STORAGE LINK CHECK ---\n";
    $storageLink = public_path('storage');
    echo "Storage link exists: " . (is_link($storageLink) ? "✅ YES" : "❌ NO") . "\n";
    
    if (is_link($storageLink)) {
        echo "Storage link target: " . readlink($storageLink) . "\n";
    }
    
    // List some sample products to see their image fields
    echo "\n--- SAMPLE PRODUCTS ---\n";
    $products = Product::on('mysql')->take(5)->get();
    foreach ($products as $product) {
        echo "Product #{$product->id}: {$product->name} - Image: " . ($product->image ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
