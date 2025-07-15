<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;

class ProductsImport
{
    public function import($file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // skip header
        while (($row = fgetcsv($handle)) !== false) {
            $category = Category::firstOrCreate(['name' => $row[5]]);
            Product::create([
                'name' => $row[0],
                'description' => $row[1],
                'price' => $row[2],
                'stock_quantity' => $row[3],
                'sku' => $row[4],
                'category_id' => $category->id,
                // Add other fields as needed
            ]);
        }
        fclose($handle);
    }
} 