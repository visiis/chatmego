<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Gift;
use App\Services\PicBedService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToPicBed extends Command
{
    protected $signature = 'images:migrate-to-picbed';
    
    protected $description = '将本地存储的图片迁移到图床';

    public function handle()
    {
        $picBedService = new PicBedService();
        
        $this->info('开始迁移用户头像...');
        $this->migrateUserAvatars($picBedService);
        
        $this->info('开始迁移礼物图片...');
        $this->migrateGiftImages($picBedService);
        
        $this->info('图片迁移完成！');
    }
    
    protected function migrateUserAvatars($picBedService)
    {
        $users = User::whereNotNull('avatar')
            ->where('avatar', 'not like', 'http://%')
            ->where('avatar', 'not like', 'https://%')
            ->get();
        
        $total = $users->count();
        $success = 0;
        $failed = 0;
        
        $this->progressBar = $this->output->createProgressBar($total);
        
        foreach ($users as $user) {
            $localPath = $user->avatar;
            
            if (Storage::disk('public')->exists($localPath)) {
                $fullPath = Storage::disk('public')->path($localPath);
                
                $result = $picBedService->upload($fullPath, 'avatars');
                
                if ($result['success']) {
                    $user->update(['avatar' => $result['url']]);
                    Storage::disk('public')->delete($localPath);
                    $success++;
                } else {
                    $this->error("用户 {$user->id} 头像迁移失败: " . $result['message']);
                    $failed++;
                }
            }
            
            $this->progressBar->advance();
        }
        
        $this->progressBar->finish();
        $this->newLine();
        $this->info("用户头像迁移完成: 成功 {$success} 个, 失败 {$failed} 个");
    }
    
    protected function migrateGiftImages($picBedService)
    {
        $gifts = Gift::whereNotNull('image')
            ->where('image', 'not like', 'http://%')
            ->where('image', 'not like', 'https://%')
            ->get();
        
        $total = $gifts->count();
        $success = 0;
        $failed = 0;
        
        $this->progressBar = $this->output->createProgressBar($total);
        
        foreach ($gifts as $gift) {
            $localPath = $gift->image;
            
            if (Storage::disk('public')->exists($localPath)) {
                $fullPath = Storage::disk('public')->path($localPath);
                
                $result = $picBedService->upload($fullPath, 'gifts');
                
                if ($result['success']) {
                    $gift->update(['image' => $result['url']]);
                    Storage::disk('public')->delete($localPath);
                    $success++;
                } else {
                    $this->error("礼物 {$gift->id} 图片迁移失败: " . $result['message']);
                    $failed++;
                }
            }
            
            $this->progressBar->advance();
        }
        
        $this->progressBar->finish();
        $this->newLine();
        $this->info("礼物图片迁移完成: 成功 {$success} 个, 失败 {$failed} 个");
    }
}
