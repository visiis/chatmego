<?php

namespace App\Services;

use GuzzleHttp\Client;

class PicBedService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $apiUrl = rtrim(config('filesystems.disks.picbed.api_url'), '/');
        $this->apiKey = config('filesystems.disks.picbed.api_key', '');
        $this->client = new Client([
            'timeout' => 60,
            'verify' => false,
        ]);
        
        // 移除末尾的 /api-v1 或 /api/1，然后统一添加 /api/1
        $apiUrl = preg_replace('/(\/api-v1|\/api\/1)$/', '', $apiUrl);
        $this->apiUrl = $apiUrl . '/api/1';
    }

    protected function compressImage($filePath, $quality = 80)
    {
        if (!file_exists($filePath)) {
            return $filePath;
        }

        $info = getimagesize($filePath);
        if (!$info) {
            return $filePath;
        }

        $mime = $info['mime'];
        $maxWidth = 1920;
        $maxHeight = 1080;

        list($width, $height) = $info;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($filePath);
                imagealphablending($source, true);
                imagesavealpha($source, true);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($filePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $source = imagecreatefromwebp($filePath);
                } else {
                    return $filePath;
                }
                break;
            default:
                return $filePath;
        }

        if (!$source) {
            return $filePath;
        }

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
            imagefill($dest, 0, 0, $transparent);
        }

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $compressedPath = sys_get_temp_dir() . '/compressed_' . uniqid() . '.jpg';
        
        imagejpeg($dest, $compressedPath, $quality);
        
        imagedestroy($source);
        imagedestroy($dest);

        return $compressedPath;
    }

    protected function getOptimalQuality($fileSize)
    {
        if ($fileSize < 500 * 1024) {
            return 90;
        } elseif ($fileSize < 2 * 1024 * 1024) {
            return 80;
        } elseif ($fileSize < 5 * 1024 * 1024) {
            return 70;
        } elseif ($fileSize < 10 * 1024 * 1024) {
            return 60;
        } else {
            return 50;
        }
    }

    public function upload($file, $directory = 'avatars', $autoCompress = true)
    {
        try {
            $uploadUrl = $this->apiUrl . '/upload';
            
            $originalFilePath = '';
            $filename = '';
            
            if (is_string($file)) {
                $originalFilePath = $file;
                $filename = basename($file);
            } elseif (is_resource($file)) {
                $tempPath = sys_get_temp_dir() . '/upload_' . time() . '.tmp';
                file_put_contents($tempPath, stream_get_contents($file));
                $originalFilePath = $tempPath;
                $filename = 'upload_' . time() . '.jpg';
            } else {
                return ['success' => false, 'message' => '无效的文件参数'];
            }

            if (!file_exists($originalFilePath)) {
                return ['success' => false, 'message' => '文件不存在: ' . $originalFilePath];
            }

            if ($autoCompress) {
                $fileSize = filesize($originalFilePath);
                $quality = $this->getOptimalQuality($fileSize);
                $file = $this->compressImage($originalFilePath, $quality);
                $filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
            } else {
                $file = $originalFilePath;
            }

            $headers = [];
            if ($this->apiKey) {
                $headers['X-API-Key'] = $this->apiKey;
            }
            
            $response = $this->client->post($uploadUrl, [
                'multipart' => [
                    [
                        'name' => 'source',
                        'filename' => $filename,
                        'contents' => fopen($file, 'r'),
                    ],
                ],
                'headers' => $headers,
            ]);

            if ($autoCompress && $file !== $originalFilePath && file_exists($file)) {
                unlink($file);
            }

            $result = json_decode($response->getBody(), true);
            
            if (isset($result['status_code']) && $result['status_code'] === 200) {
                $url = '';
                if (isset($result['image']['url'])) {
                    $url = $result['image']['url'];
                } elseif (isset($result['url'])) {
                    $url = $result['url'];
                } elseif (isset($result['data']['url'])) {
                    $url = $result['data']['url'];
                }
                
                return [
                    'success' => true,
                    'url' => $url,
                    'message' => '上传成功',
                    'raw' => $result,
                ];
            }

            return ['success' => false, 'message' => $result['error']['message'] ?? $result['message'] ?? '上传失败', 'raw' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function uploadFromUrl($url)
    {
        try {
            $uploadUrl = $this->apiUrl . '/upload';
            
            $formParams = [
                'source' => $url,
            ];

            if ($this->apiKey) {
                $formParams['key'] = $this->apiKey;
            }

            $response = $this->client->post($uploadUrl, [
                'form_params' => $formParams,
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (isset($result['status_code']) && $result['status_code'] === 200) {
                return [
                    'success' => true,
                    'url' => $result['image']['url'] ?? ($result['url'] ?? ''),
                    'message' => '上传成功',
                ];
            }

            return ['success' => false, 'message' => $result['error']['message'] ?? '上传失败'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($url)
    {
        try {
            $deleteUrl = $this->apiUrl . '/delete';
            
            $formParams = [
                'url' => $url,
            ];

            if ($this->apiKey) {
                $formParams['key'] = $this->apiKey;
            }

            $response = $this->client->post($deleteUrl, [
                'form_params' => $formParams,
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (isset($result['status_code']) && $result['status_code'] === 200) {
                return ['success' => true, 'message' => '删除成功'];
            }

            return ['success' => false, 'message' => $result['error']['message'] ?? '删除失败'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function listFiles($directory = '')
    {
        try {
            $listUrl = $this->apiUrl . '/media';
            
            $query = [];
            
            if ($this->apiKey) {
                $query['key'] = $this->apiKey;
            }

            if ($directory) {
                $query['album_id'] = $directory;
            }

            $response = $this->client->get($listUrl, [
                'query' => $query,
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (isset($result['status_code']) && $result['status_code'] === 200) {
                return ['success' => true, 'files' => $result['media'] ?? []];
            }

            return ['success' => false, 'message' => $result['error']['message'] ?? '获取失败'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
