<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 获取所有用户
$users = DB::table('users')->select('id', 'name', 'email', 'password')->get();

echo "测试用户密码验证:\n";
echo "==================\n\n";

foreach ($users as $user) {
    // 检查密码是否为 Bcrypt 格式
    $isBcrypt = str_starts_with($user->password, '$2y$12$');
    $isValidLength = strlen($user->password) === 60;
    
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    echo "  - Is Bcrypt format: " . ($isBcrypt ? 'YES' : 'NO') . "\n";
    echo "  - Valid length (60): " . ($isValidLength ? 'YES' : 'NO') . "\n";
    
    // 测试常见密码
    $commonPasswords = ['123456', 'admin', 'password', '12345678', 'admin123'];
    foreach ($commonPasswords as $pwd) {
        if (Hash::check($pwd, $user->password)) {
            echo "  ✓ Password found: {$pwd}\n";
            break;
        }
    }
    echo "\n";
}
