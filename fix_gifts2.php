<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Gift;

$gifts = Gift::all();

foreach ($gifts as $gift) {
    $image = $gift->image;
    
    if (!$image) {
        continue;
    }
    
    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
        continue;
    }
    
    $filename = basename($image);
    
    echo "Processing gift ID {$gift->id}: {$gift->name}\n";
    echo "Current image: $image\n";
    
    $picbedUrl = 'https://pic.chatmego.com/images/2026/04/29/' . $filename;
    
    echo "  Updating to: $picbedUrl\n";
    
    $gift->update(['image' => $picbedUrl]);
    
    echo "---\n";
}

echo "Done!\n";
