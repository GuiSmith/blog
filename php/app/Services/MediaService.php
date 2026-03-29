<?php

namespace App\Services;

class MediaService
{
    private $uploadDir;
    private $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/images';
        $this->ensureDirectoryExists();
    }

    /**
     * Get allowed MIME types
     */
    public function getAllowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }

    /**
     * Upload an avatar image
     * @throws \Exception If upload fails
     */
    public function uploadAvatar(array $fileData): string
    {
        // Validate upload error
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Upload failed: ' . $this->getUploadErrorMessage($fileData['error']));
        }

        // Validate file size (2MB limit)
        if ($fileData['size'] > 2 * 1024 * 1024) {
            throw new \Exception('File too large. Maximum size is 2MB.');
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileData['tmp_name']);
        finfo_close($finfo);

        if (!isset($this->allowedMimeTypes[$mimeType])) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, and WebP images are allowed.');
        }

        // Generate secure filename
        $extension = $this->allowedMimeTypes[$mimeType];
        $filename = $this->generateSecureFilename($extension);

        // Move file to storage
        $destination = $this->uploadDir . '/' . $filename;
        if (!move_uploaded_file($fileData['tmp_name'], $destination)) {
            throw new \Exception('Failed to save uploaded file.');
        }

        return $filename;
    }

    /**
     * Get image file information
     * @throws \Exception If file not found or invalid
     */
    public function getImageInfo(string $filename): array
    {
        // Validate filename format
        if (!preg_match('/^[a-f0-9]{32}\.[a-z]{3,4}$/', $filename)) {
            throw new \Exception('Invalid filename format.');
        }

        $filePath = $this->getImagePath($filename);
        
        if (!$filePath || !file_exists($filePath)) {
            throw new \Exception('File not found.');
        }

        // Get MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        // Validate MIME type
        if (!in_array($mimeType, array_keys($this->allowedMimeTypes))) {
            throw new \Exception('Invalid file type.');
        }

        return [
            'path' => $filePath,
            'mimeType' => $mimeType,
            'size' => filesize($filePath)
        ];
    }

    /**
     * Get full path to image file
     */
    public function getImagePath(string $filename): ?string
    {
        // Sanitize filename
        $safeFilename = basename($filename);
        
        if ($safeFilename !== $filename) {
            return null;
        }

        $filePath = $this->uploadDir . '/' . $safeFilename;
        
        return file_exists($filePath) ? $filePath : null;
    }

    /**
     * Generate secure filename using random_bytes
     */
    private function generateSecureFilename(string $extension): string
    {
        $randomName = bin2hex(random_bytes(16));
        return $randomName . '.' . $extension;
    }

    /**
     * Ensure upload directory exists
     */
    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Get upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive.';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension.';
            default:
                return 'Unknown upload error.';
        }
    }
}