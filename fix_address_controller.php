<?php

// Script to fix AddressController validation rules
$controllerPath = 'app/Http/Controllers/AddressController.php';

echo "🔧 Fixing AddressController validation rules...\n";

// Read the current file
$content = file_get_contents($controllerPath);

if (!$content) {
    echo "❌ Could not read AddressController file\n";
    exit(1);
}

echo "✅ Read AddressController file\n";

// Replace 'name' validation with 'first_name' and 'last_name' in both store and update methods
$oldValidation = "'name' => 'required|string|max:255',";
$newValidation = "'first_name' => 'required|string|max:255',\n            'last_name' => 'required|string|max:255',";

// Count occurrences
$count = substr_count($content, $oldValidation);
echo "📋 Found {$count} occurrences of 'name' validation rule\n";

if ($count > 0) {
    // Replace all occurrences
    $newContent = str_replace($oldValidation, $newValidation, $content);
    
    // Write the updated content back
    if (file_put_contents($controllerPath, $newContent)) {
        echo "✅ Updated AddressController validation rules\n";
        echo "📝 Replaced 'name' with 'first_name' and 'last_name' in {$count} locations\n";
    } else {
        echo "❌ Failed to write updated content\n";
        exit(1);
    }
} else {
    echo "⚠️ No 'name' validation rules found to replace\n";
}

echo "\n🎉 AddressController validation rules fixed!\n";
echo "✅ Now the controller expects 'first_name' and 'last_name' fields\n";
echo "✅ This matches the updated form fields in the address modal\n";
