<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@example.com';
$password = 'password123';

$user = User::where('email', $email)->first();

if ($user) {
    echo "User found: " . $user->email . "\n";
    echo "Password hash: " . $user->password . "\n";
    
    // 测试密码验证
    $check = Hash::check($password, $user->password);
    echo "Password check: " . ($check ? "PASS" : "FAIL") . "\n";
} else {
    echo "User not found!\n";
}