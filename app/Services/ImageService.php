<?php

namespace App\Services;

class ImageService
{
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    
    const SIZE_MAP = [
        self::SIZE_SMALL => [
            'width' => 200,
            'height' => 200,
            'quality' => 70,
        ],
        self::SIZE_MEDIUM => [
            'width' => 800,
            'height' => 800,
            'quality' => 80,
        ],
        self::SIZE_LARGE => [
            'width' => 1920,
            'height' => 1080,
            'quality' => 90,
        ],
    ];
    
    protected $networkQuality = 'normal';
    
    public function __construct()
    {
        $this->detectNetworkQuality();
    }
    
    protected function detectNetworkQuality()
    {
        if (isset($_SERVER['HTTP_DOWNLINK'])) {
            $downlink = (float)$_SERVER['HTTP_DOWNLINK'];
            if ($downlink < 0.1) {
                $this->networkQuality = 'slow';
            } elseif ($downlink < 1) {
                $this->networkQuality = 'medium';
            } else {
                $this->networkQuality = 'fast';
            }
        }
    }
    
    public function getNetworkQuality()
    {
        return $this->networkQuality;
    }
    
    public function getSizeForContext($context)
    {
        switch ($context) {
            case 'list':
                return $this->adjustSizeForNetwork(self::SIZE_SMALL);
            case 'detail':
                return $this->adjustSizeForNetwork(self::SIZE_MEDIUM);
            case 'preview':
                return $this->adjustSizeForNetwork(self::SIZE_LARGE);
            default:
                return self::SIZE_MEDIUM;
        }
    }
    
    protected function adjustSizeForNetwork($baseSize)
    {
        if ($this->networkQuality === 'slow') {
            switch ($baseSize) {
                case self::SIZE_LARGE:
                    return self::SIZE_MEDIUM;
                case self::SIZE_MEDIUM:
                    return self::SIZE_SMALL;
                case self::SIZE_SMALL:
                default:
                    return self::SIZE_SMALL;
            }
        } elseif ($this->networkQuality === 'medium') {
            switch ($baseSize) {
                case self::SIZE_LARGE:
                    return self::SIZE_MEDIUM;
                default:
                    return $baseSize;
            }
        }
        
        return $baseSize;
    }
    
    public function getImageUrl($originalUrl, $size = self::SIZE_MEDIUM)
    {
        if (!$originalUrl) {
            return null;
        }
        
        if (!str_starts_with($originalUrl, 'http://') && !str_starts_with($originalUrl, 'https://')) {
            return asset('storage/' . $originalUrl);
        }
        
        $sizeConfig = self::SIZE_MAP[$size] ?? self::SIZE_MAP[self::SIZE_MEDIUM];
        
        if (str_contains($originalUrl, 'pic.chatmego.com')) {
            return $this->buildCheveretoUrl($originalUrl, $sizeConfig);
        }
        
        return $originalUrl;
    }
    
    protected function buildCheveretoUrl($url, $sizeConfig)
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $dirname = dirname($path);
        
        $newPath = $dirname . '/' . $filename . '.th.' . $extension;
        
        return $parsed['scheme'] . '://' . $parsed['host'] . $newPath;
    }
    
    public function getResponsiveImage($url, $context = 'list', $attributes = [])
    {
        $size = $this->getSizeForContext($context);
        $imageUrl = $this->getImageUrl($url, $size);
        
        if (!$imageUrl) {
            return '';
        }
        
        $srcset = $this->buildSrcSet($url);
        
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<img src="' . htmlspecialchars($imageUrl) . '" srcset="' . $srcset . '"' . $attrString . '>';
    }
    
    protected function buildSrcSet($url)
    {
        if (!str_contains($url, 'pic.chatmego.com')) {
            return '';
        }
        
        $srcset = [];
        foreach (self::SIZE_MAP as $size => $config) {
            $sizeUrl = $this->getImageUrl($url, $size);
            $srcset[] = $sizeUrl . ' ' . $config['width'] . 'w';
        }
        
        return implode(', ', $srcset);
    }
    
    public function getLazyImage($url, $context = 'list', $attributes = [])
    {
        $size = $this->getSizeForContext($context);
        $imageUrl = $this->getImageUrl($url, $size);
        
        if (!$imageUrl) {
            return '';
        }
        
        $srcset = $this->buildSrcSet($url);
        
        $defaultAttrs = [
            'loading' => 'lazy',
            'data-src' => $imageUrl,
            'class' => 'lazy-image',
        ];
        
        $mergedAttrs = array_merge($defaultAttrs, $attributes);
        
        $attrString = '';
        foreach ($mergedAttrs as $key => $value) {
            $attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<img data-src="' . htmlspecialchars($imageUrl) . '" srcset="' . $srcset . '"' . $attrString . '>';
    }
}
