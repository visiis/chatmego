<?php

namespace App\Observers;

use App\Models\Gift;
use App\Services\PicBedService;
use Illuminate\Support\Facades\Storage;

class GiftObserver
{
    public function saved(Gift $gift): void
    {
        if (!$gift->image) {
            return;
        }
        
        if (str_starts_with($gift->image, 'http://') || str_starts_with($gift->image, 'https://')) {
            if (str_contains($gift->image, 'pic.chatmego.com')) {
                return;
            }
        }
        
        $imagePath = $gift->image;
        
        if (!Storage::disk('public')->exists($imagePath)) {
            $imagePath = 'gifts/' . basename($imagePath);
        }
        
        if (!Storage::disk('public')->exists($imagePath)) {
            $imagePath = str_replace('storage/', '', $gift->image);
        }
        
        if (Storage::disk('public')->exists($imagePath)) {
            $fullPath = Storage::disk('public')->path($imagePath);
            if (file_exists($fullPath)) {
                $picBedService = new PicBedService();
                $result = $picBedService->upload($fullPath, 'gifts');
                
                if ($result['success']) {
                    $gift->update(['image' => $result['url']]);
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }
    }
}
