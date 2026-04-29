<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$gifts = \App\Models\Gift::all();

foreach ($gifts as $gift) {
    echo "ID: " . $gift->id . "\n";
    echo "Name: " . $gift->name . "\n";
    echo "Image: " . $gift->image . "\n";
    echo "Is PicBed URL: " . (str_contains($gift->image, 'pic.chatmego.com') ? 'YES' : 'NO') . "\n";
    echo "---\n";
}
