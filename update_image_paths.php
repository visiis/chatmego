<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Gift;

echo "更新用户头像路径...\n";
$users = User::whereNotNull('avatar')
    ->where('avatar', 'not like', 'https://%')
    ->get();

$count = 0;
foreach ($users as $user) {
    $filename = basename($user->avatar);
    $filename = preg_replace('/\.[^.]+$/', '.jpg', $filename);
    $user->avatar = 'https://pic.chatmego.com/images/2026/04/29/' . $filename;
    $user->save();
    $count++;
}

echo "更新了 $count 个用户头像\n";

echo "\n更新礼物图片路径...\n";
$gifts = Gift::whereNotNull('image')
    ->where('image', 'not like', 'https://%')
    ->get();

$count = 0;
foreach ($gifts as $gift) {
    $filename = basename($gift->image);
    $filename = preg_replace('/\.[^.]+$/', '.jpg', $filename);
    $gift->image = 'https://pic.chatmego.com/images/2026/04/29/' . $filename;
    $gift->save();
    $count++;
}

echo "更新了 $count 个礼物图片\n";

echo "\n完成！\n";
?>