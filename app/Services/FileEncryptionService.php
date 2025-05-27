<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileEncryptionService
{
    private $key;

    public function __construct()
    {
        $this->key = config('app.file_encryption_key');
    }

    public function encryptFile($filePath)
    {
        $content = Storage::get($filePath);
        $iv = random_bytes(16);
        $encryptedContent = openssl_encrypt($content, 'aes-256-cbc', $this->key, 0, $iv);
        $finalContent = base64_encode($iv . $encryptedContent);
        Storage::put($filePath, $finalContent);
    }

    public function decryptFile($filePath)
    {
        $content = Storage::get($filePath);
        $decoded = base64_decode($content);
        $iv = substr($decoded, 0, 16);
        $encryptedContent = substr($decoded, 16);
        return openssl_decrypt($encryptedContent, 'aes-256-cbc', $this->key, 0, $iv);
    }
}
