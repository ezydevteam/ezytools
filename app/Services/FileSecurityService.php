<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileSecurityService
{
    /**
     * Allowed MIME types per tool type.
     */
    private array $allowedMimes = [
        'pdf'   => ['application/pdf'],
        'image' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'],
        'video' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm'],
        'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
        'text'  => ['text/plain'],
        'doc'   => [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'spreadsheet' => [
            'text/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
    ];

    /**
     * Max file sizes in bytes per type and tier.
     */
    private array $maxSizes = [
        'pdf_free'         => 10 * 1024 * 1024,   // 10MB
        'pdf_pro'          => 50 * 1024 * 1024,    // 50MB
        'image_free'       => 5  * 1024 * 1024,    // 5MB
        'image_pro'        => 20 * 1024 * 1024,    // 20MB
        'video_free'       => 50 * 1024 * 1024,    // 50MB
        'video_pro'        => 200 * 1024 * 1024,   // 200MB
        'audio_free'       => 10 * 1024 * 1024,    // 10MB
        'audio_pro'        => 50 * 1024 * 1024,    // 50MB
        'text_free'        => 5  * 1024 * 1024,    // 5MB
        'text_pro'         => 10 * 1024 * 1024,    // 10MB
        'doc_free'         => 10 * 1024 * 1024,    // 10MB
        'doc_pro'          => 50 * 1024 * 1024,    // 50MB
        'spreadsheet_free' => 10 * 1024 * 1024,    // 10MB
        'spreadsheet_pro'  => 50 * 1024 * 1024,    // 50MB
    ];

    /**
     * Whitelisted file extensions.
     */
    private array $allowedExtensions = [
        'pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg',
        'mp4', 'mp3', 'wav', 'ogg', 'webm',
        'txt', 'csv', 'json', 'xml',
        'doc', 'docx', 'xls', 'xlsx',
    ];

    /**
     * Validate an uploaded file for security threats.
     *
     * @return string[] Array of error messages (empty = valid)
     */
    public function validate(UploadedFile $file, string $type, bool $isPro = false): array
    {
        $errors = [];

        // 1. Check file is actually valid
        if (!$file->isValid()) {
            $errors[] = 'Invalid file upload.';
            return $errors;
        }

        // 2. Verify MIME type using finfo (NOT just extension)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        $allowedForType = $this->allowedMimes[$type] ?? [];
        if (!in_array($realMime, $allowedForType)) {
            $errors[] = "Invalid file type: {$realMime}. Allowed: " . implode(', ', $allowedForType);
            return $errors; // Stop here — wrong type
        }

        // 3. Check file size
        $sizeKey = $type . '_' . ($isPro ? 'pro' : 'free');
        $maxSize = $this->maxSizes[$sizeKey] ?? 10 * 1024 * 1024;

        if ($file->getSize() > $maxSize) {
            $maxMB = $maxSize / 1024 / 1024;
            $errors[] = "File too large. Maximum: {$maxMB}MB";
        }

        // 4. Check for PHP code injection in images
        if ($type === 'image') {
            $content = file_get_contents($file->getRealPath());
            if (preg_match('/<\?php|<\?=/i', $content)) {
                $errors[] = 'Malicious content detected in file.';
                Log::warning('PHP injection attempt detected in file upload', [
                    'ip' => request()->ip(),
                    'filename' => $file->getClientOriginalName(),
                    'mime' => $realMime,
                ]);
            }

            // Verify it is a valid image (not a disguised script)
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo === false && $realMime !== 'image/svg+xml') {
                $errors[] = 'File is not a valid image.';
            }
        }

        // 5. Check filename for path traversal attempts
        $filename = $file->getClientOriginalName();
        if (preg_match('/[\/\\\\\.]{2,}/', $filename)) {
            $errors[] = 'Invalid filename (path traversal detected).';
            Log::warning('Path traversal attempt in file upload', [
                'ip' => request()->ip(),
                'filename' => $filename,
            ]);
        }

        // 6. PDF-specific: verify PDF magic bytes
        if ($type === 'pdf') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fread($handle, 5);
            fclose($handle);
            if (!str_starts_with($header, '%PDF')) {
                $errors[] = 'File is not a valid PDF.';
            }
        }

        // 7. Check for null bytes in filename (common attack vector)
        if (str_contains($filename, "\0")) {
            $errors[] = 'Invalid filename.';
            Log::warning('Null byte injection attempt', [
                'ip' => request()->ip(),
            ]);
        }

        return $errors;
    }

    /**
     * Generate a safe filename using UUID + whitelisted extension.
     */
    public function safeName(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, $this->allowedExtensions)) {
            $ext = 'bin';
        }

        return Str::uuid() . '.' . $ext;
    }

    /**
     * Store a file in the private disk with secure permissions.
     */
    public function storePrivate(UploadedFile $file, string $folder): string
    {
        $safeName = $this->safeName($file);

        Storage::disk('local')->putFileAs($folder, $file, $safeName);

        // Set secure permissions on Linux
        if (PHP_OS_FAMILY !== 'Windows') {
            $fullPath = storage_path('app/private/' . $folder . '/' . $safeName);
            if (file_exists($fullPath)) {
                chmod($fullPath, 0640);
            }
        }

        return $folder . '/' . $safeName;
    }

    /**
     * Get the max allowed size (in bytes) for a given type and tier.
     */
    public function getMaxSize(string $type, bool $isPro = false): int
    {
        $sizeKey = $type . '_' . ($isPro ? 'pro' : 'free');
        return $this->maxSizes[$sizeKey] ?? 10 * 1024 * 1024;
    }

    /**
     * Get the max allowed size formatted as MB string.
     */
    public function getMaxSizeMB(string $type, bool $isPro = false): string
    {
        return round($this->getMaxSize($type, $isPro) / 1024 / 1024) . 'MB';
    }
}
