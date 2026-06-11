<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['phone' => '13800138000'],
            [
                'name' => '测试用户',
                'email' => 'test@example.com',
                'password' => Hash::make('123456'),
                'gender' => 1,
                'age' => 25,
                'height' => '175',
                'weight' => '65',
                'hobbies' => '阅读,旅行',
                'specialty' => '编程',
                'love_declaration' => '寻找有缘人',
                'points' => 100,
                'total_points_earned' => 100,
                'coins' => 0,
                'is_active' => true,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['phone' => '13900139000'],
            [
                'name' => '测试用户2',
                'email' => 'test2@example.com',
                'password' => Hash::make('123456'),
                'gender' => 2,
                'age' => 23,
                'height' => '165',
                'weight' => '55',
                'hobbies' => '音乐,电影',
                'specialty' => '绘画',
                'love_declaration' => '期待遇见你',
                'points' => 200,
                'total_points_earned' => 200,
                'coins' => 100,
                'is_active' => true,
                'status' => 'active',
            ]
        );
    }
}
