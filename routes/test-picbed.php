<?php

use Illuminate\Support\Facades\Route;

// 测试会员等级功能
Route::get('/test-member-level', function () {
    try {
        $levels = \App\Models\MemberLevel::getAllLevels();
        
        if ($levels->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => '没有找到会员等级数据',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => '会员等级功能正常！',
            'levels' => $levels->toArray(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => '会员等级功能出错',
            'error' => $e->getMessage(),
        ], 500);
    }
});

// 测试图床配置
Route::get('/test-picbed', function () {
    $config = [
        'api_url' => config('filesystems.disks.picbed.api_url'),
        'api_key_set' => !empty(config('filesystems.disks.picbed.api_key')),
    ];
    
    return response()->json([
        'success' => true,
        'message' => '图床配置已加载',
        'config' => $config,
        'note' => '图床上传测试需要通过实际文件上传接口测试',
    ]);
});