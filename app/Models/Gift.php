<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Gift extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price_type',
        'price',
        'image',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
    ];

    protected static function booted(): void
    {
        static::retrieved(function (self $gift) {
            // 在模型加载时，如果图片是本地路径，自动上传到图床
            if ($gift->image && !str_starts_with($gift->image, 'http')) {
                $path = storage_path('app/public/' . $gift->image);
                if (!file_exists($path)) {
                    $path = storage_path('app/' . $gift->image);
                }
                
                if (file_exists($path)) {
                    try {
                        $picBedService = app(\App\Services\PicBedService::class);
                        $result = $picBedService->upload($path, 'gifts');
                        if ($result['success']) {
                            $thumbnailUrl = self::convertToThumbnailUrl($result['url']);
                            $gift->update(['image' => $thumbnailUrl]);
                            unlink($path);
                        }
                    } catch (\Exception $e) {
                        // 上传失败保持本地路径
                    }
                }
            }
        });
        
        static::saving(function (self $gift) {
            Log::info('Gift saving hook called', [
                'id' => $gift->id,
                'image' => $gift->image,
            ]);
            
            // 获取原始图片值
            $originalImage = $gift->getOriginal('image');
            
            // 如果 image 字段为空或为空数组，保持原有图片不变
            if (empty($gift->image) || (is_array($gift->image) && empty($gift->image))) {
                $gift->image = $originalImage;
                return;
            }
            
            // 如果是数组，取第一个元素
            if (is_array($gift->image)) {
                $gift->image = $gift->image[0] ?? $originalImage;
            }
            
            // 如果已经是 HTTP URL，检查是否需要转换为缩略图
            if (str_starts_with($gift->image, 'http')) {
                if (!str_contains($gift->image, '.th.')) {
                    $gift->image = self::convertToThumbnailUrl($gift->image);
                }
                return;
            }
            
            // 如果是本地路径，上传到图床
            $path = storage_path('app/public/' . $gift->image);
            if (!file_exists($path)) {
                $path = storage_path('app/' . $gift->image);
            }
            if (!file_exists($path)) {
                $path = public_path('storage/' . $gift->image);
            }
            if (!file_exists($path)) {
                $path = public_path($gift->image);
            }
            
            Log::info('Trying to find file', ['path' => $path, 'exists' => file_exists($path)]);
            
            if (file_exists($path)) {
                try {
                    $picBedService = app(\App\Services\PicBedService::class);
                    $result = $picBedService->upload($path, 'gifts');
                    
                    Log::info('PicBed upload result', ['result' => $result]);
                    
                    if ($result['success']) {
                        $gift->image = self::convertToThumbnailUrl($result['url']);
                        unlink($path);
                        Log::info('Image updated successfully', ['thumbnail_url' => $gift->image]);
                    } else {
                        $gift->image = $originalImage;
                        Log::error('PicBed upload failed', ['message' => $result['message'] ?? 'Unknown error']);
                    }
                } catch (\Exception $e) {
                    $gift->image = $originalImage;
                    Log::error('PicBed upload exception', ['error' => $e->getMessage()]);
                }
            } else {
                // 文件未找到，保持原有图片
                $gift->image = $originalImage;
                Log::warning('Uploaded file not found', ['image' => $gift->image]);
            }
        });
    }

    public static function convertToThumbnailUrl($url): string
    {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            return $url;
        }
        
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        
        if (str_ends_with($filename, '.th')) {
            return $url;
        }
        
        $dirname = dirname($path);
        
        $newPath = $dirname . '/' . $filename . '.th.' . $extension;
        
        return $parsed['scheme'] . '://' . $parsed['host'] . $newPath;
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(UserGift::class);
    }
}