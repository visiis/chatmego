<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Gift;
use App\Services\PicBedService;
use Illuminate\Support\Facades\Storage;

$gifts = Gift::all();
$picBedService = new PicBedService();

foreach ($gifts as $gift) {
    $image = $gift->image;
    
    if (!$image) {
        continue;
    }
    
    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
        continue;
    }
    
    echo "Processing gift ID {$gift->id}: {$gift->name}\n";
    echo "Current image: $image\n";
    
    $imagePath = $image;
    if (!Storage::disk('public')->exists($imagePath)) {
        $imagePath = 'gifts/' . basename($imagePath);
    }
    
    if (!Storage::disk('public')->exists($imagePath)) {
        echo "  File not found, skipping\n";
        continue;
    }
    
    $fullPath = Storage::disk('public')->path($imagePath);
    echo "  Full path: $fullPath\n";
    
    if (file_exists($fullPath)) {
        $result = $picBedService->upload($fullPath, 'gifts');
        
        if ($result['success']) {
            $gift->update(['image' => $result['url']]);
            Storage::disk('public')->delete($imagePath);
            echo "  Uploaded to: " . $result['url'] . "\n";
        } else {
            echo "  Upload failed: " . $result['message'] . "\n";
        }
    } else {
        echo "  File does not exist\n";
    }
    
    echo "---\n";
}

echo "Done!\n";
