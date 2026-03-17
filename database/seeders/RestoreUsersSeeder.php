<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestoreUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = json_decode(file_get_contents(storage_path('users_data.json')), true);

        foreach ($usersData as $userData) {
            // 移除不存在的字段
            unset($userData['is_admin']);
            
            // 使用 DB 直接插入以避免模型事件
            DB::table('users')->updateOrInsert(
                ['id' => $userData['id']],
                $userData
            );
        }

        $this->command->info('用户数据恢复完成！');
    }
}
