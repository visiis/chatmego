<?php

use App\Services\ImageService;

if (!function_exists('image_url')) {
    /**
     * 获取图片的正确 URL
     * 如果是完整 URL 则直接返回，否则拼接 storage 路径
     */
    function image_url($path)
    {
        if (!$path) {
            return null;
        }
        
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        return asset('storage/' . $path);
    }
}

if (!function_exists('avatar_url')) {
    function avatar_url($path, $default = null)
    {
        if (!$path) {
            return $default ?? asset('images/default-avatar.svg');
        }
        
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            if (str_contains($path, 'pic.chatmego.com')) {
                $parsed = parse_url($path);
                $pathInfo = $parsed['path'] ?? '';
                $extension = pathinfo($pathInfo, PATHINFO_EXTENSION);
                $filename = pathinfo($pathInfo, PATHINFO_FILENAME);
                $dirname = dirname($pathInfo);
                $newPath = $dirname . '/' . $filename . '.th.' . $extension;
                return $parsed['scheme'] . '://' . $parsed['host'] . $newPath;
            }
            return $path;
        }
        
        return asset('storage/' . $path);
    }
}

if (!function_exists('lazy_image')) {
    /**
     * 生成带有懒加载效果的图片标签
     */
    function lazy_image($url, $context = 'list', $attributes = [])
    {
        $imageService = new ImageService();
        
        if (!$url) {
            return '';
        }
        
        $size = $imageService->getSizeForContext($context);
        $imageUrl = $imageService->getImageUrl($url, $size);
        
        if (!$imageUrl) {
            return '';
        }
        
        $srcset = $imageService->buildSrcSet($url);
        
        $defaultAttrs = [
            'loading' => 'lazy',
            'class' => 'lazy-image',
            'alt' => '',
        ];
        
        $mergedAttrs = array_merge($defaultAttrs, $attributes);
        
        $attrString = '';
        foreach ($mergedAttrs as $key => $value) {
            $attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<img src="' . htmlspecialchars($imageUrl) . '" srcset="' . $srcset . '"' . $attrString . '>';
    }
}

if (!function_exists('progressive_image')) {
    /**
     * 生成渐进式加载的图片标签
     */
    function progressive_image($url, $context = 'list', $attributes = [])
    {
        $imageService = new ImageService();
        
        if (!$url) {
            return '';
        }
        
        $size = $imageService->getSizeForContext($context);
        $imageUrl = $imageService->getImageUrl($url, $size);
        
        if (!$imageUrl) {
            return '';
        }
        
        $placeholderUrl = $imageService->getImageUrl($url, 'small');
        
        $defaultAttrs = [
            'loading' => 'lazy',
            'class' => 'progressive-image',
            'alt' => '',
            'data-src' => $imageUrl,
        ];
        
        $mergedAttrs = array_merge($defaultAttrs, $attributes);
        
        $attrString = '';
        foreach ($mergedAttrs as $key => $value) {
            $attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<div class="progressive-image-wrapper"><img src="' . htmlspecialchars($placeholderUrl) . '"' . $attrString . '></div>';
    }
}
