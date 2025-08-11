<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CHECKING PRODUCT IMAGE PATHS ===\n\n";
    
    // Check the order item directly from database
    $orderItem = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('order_items.order_id', 5)
        ->select('products.name', 'products.image', 'products.id')
        ->first();
    
    if ($orderItem) {
        echo "Product found:\n";
        echo "ID: " . $orderItem->id . "\n";
        echo "Name: " . $orderItem->name . "\n";
        echo "Image field: " . ($orderItem->image ?? 'NULL') . "\n";
        
        if ($orderItem->image) {
            $imagePath = $orderItem->image;
            $fullPath = public_path('storage/' . $imagePath);
            $assetUrl = asset('storage/' . $imagePath);
            
            echo "Asset URL: " . $assetUrl . "\n";
            echo "Full file path: " . $fullPath . "\n";
            echo "File exists: " . (file_exists($fullPath) ? "YES" : "NO") . "\n";
            
            // Check alternative paths
            $altPaths = [
                public_path($imagePath),
                public_path('images/' . basename($imagePath)),
                public_path('uploads/' . basename($imagePath)),
                storage_path('app/public/' . $imagePath)
            ];
            
            echo "\nChecking alternative locations:\n";
            foreach ($altPaths as $path) {
                echo "  " . $path . " - " . (file_exists($path) ? "EXISTS" : "NOT FOUND") . "\n";
            }
        }
    } else {
        echo "No product found for order #5\n";
    }
    
    // Check storage symlink
    echo "\n=== STORAGE SYMLINK CHECK ===\n";
    $storageLink = public_path('storage');
    echo "Storage symlink exists: " . (is_link($storageLink) || is_dir($storageLink) ? "YES" : "NO") . "\n";
    
    if (is_link($storageLink)) {
        echo "Symlink target: " . readlink($storageLink) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
