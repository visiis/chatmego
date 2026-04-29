<?php

namespace App\Extensions\Storage;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToRetrieveMetadata;
use GuzzleHttp\Client;

class PicBedAdapter implements FilesystemAdapter
{
    protected $client;
    protected $apiUrl;
    protected $uploadUrl;
    protected $deleteUrl;

    public function __construct(array $config)
    {
        $this->apiUrl = rtrim($config['api_url'], '/');
        $this->uploadUrl = $this->apiUrl . '/upload';
        $this->deleteUrl = $this->apiUrl . '/delete';
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);
    }

    public function fileExists(string $path): bool
    {
        try {
            $response = $this->client->get($path);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $tmpFile = tempnam(sys_get_temp_dir(), 'picbed');
            file_put_contents($tmpFile, $contents);
            
            $response = $this->client->post($this->uploadUrl, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'filename' => basename($path),
                        'contents' => fopen($tmpFile, 'r'),
                    ],
                ],
            ]);
            
            unlink($tmpFile);
            
            $result = json_decode($response->getBody(), true);
            if (!isset($result['success']) || !$result['success']) {
                throw new UnableToWriteFile($result['message'] ?? '上传失败');
            }
        } catch (\Exception $e) {
            throw new UnableToWriteFile($e->getMessage());
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        try {
            $tmpFile = tempnam(sys_get_temp_dir(), 'picbed');
            file_put_contents($tmpFile, $contents);
            
            $response = $this->client->post($this->uploadUrl, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'filename' => basename($path),
                        'contents' => fopen($tmpFile, 'r'),
                    ],
                ],
            ]);
            
            unlink($tmpFile);
            
            $result = json_decode($response->getBody(), true);
            if (!isset($result['success']) || !$result['success']) {
                throw new UnableToWriteFile($result['message'] ?? '上传失败');
            }
        } catch (\Exception $e) {
            throw new UnableToWriteFile($e->getMessage());
        }
    }

    public function read(string $path): string
    {
        try {
            $response = $this->client->get($path);
            return (string) $response->getBody();
        } catch (\Exception $e) {
            throw new UnableToReadFile($e->getMessage());
        }
    }

    public function readStream(string $path)
    {
        try {
            $response = $this->client->get($path, ['stream' => true]);
            return $response->getBody()->detach();
        } catch (\Exception $e) {
            throw new UnableToReadFile($e->getMessage());
        }
    }

    public function delete(string $path): void
    {
        try {
            $response = $this->client->post($this->deleteUrl, [
                'form_params' => [
                    'url' => $path,
                ],
            ]);
            
            $result = json_decode($response->getBody(), true);
            if (!isset($result['success']) || !$result['success']) {
                throw new UnableToDeleteFile($result['message'] ?? '删除失败');
            }
        } catch (\Exception $e) {
            throw new UnableToDeleteFile($e->getMessage());
        }
    }

    public function deleteDirectory(string $path): void
    {
    }

    public function createDirectory(string $path, Config $config): void
    {
    }

    public function setVisibility(string $path, string $visibility): void
    {
    }

    public function visibility(string $path): FileAttributes
    {
        throw new UnableToRetrieveMetadata('Visibility not supported');
    }

    public function mimeType(string $path): FileAttributes
    {
        throw new UnableToRetrieveMetadata('MIME type not supported');
    }

    public function lastModified(string $path): FileAttributes
    {
        throw new UnableToRetrieveMetadata('Last modified not supported');
    }

    public function fileSize(string $path): FileAttributes
    {
        throw new UnableToRetrieveMetadata('File size not supported');
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return [];
    }

    public function move(string $source, string $destination, Config $config): void
    {
    }

    public function copy(string $source, string $destination, Config $config): void
    {
    }
}