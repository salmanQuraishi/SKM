<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Store an uploaded file to storage/app/public and mirror to public/storage
     * to guarantee it works seamlessly on both local environments and shared/live hosting (cPanel).
     *
     * @param UploadedFile $file
     * @param string $folder (e.g. 'products', 'brands', 'categories', 'banners', 'models', 'variants')
     * @return string Relative web path starting with 'storage/' (e.g. 'storage/products/filename.jpg')
     */
    public static function upload(UploadedFile $file, string $folder): string
    {
        // 1. Store via Laravel's configured public disk (storage/app/public/$folder/...)
        $path = $file->store($folder, 'public');

        // 2. Mirror file to public/storage so web servers without symlink support can serve it as static file
        self::mirrorToPublic($path);

        return 'storage/' . $path;
    }

    /**
     * Mirror a stored file from storage/app/public to public/storage
     *
     * @param string $subPath Relative path inside public disk, e.g. 'products/abc123xyz.jpg'
     */
    public static function mirrorToPublic(string $subPath): void
    {
        try {
            $source = storage_path('app/public/' . $subPath);
            if (!file_exists($source)) {
                return;
            }

            $targetDir = public_path('storage/' . dirname($subPath));
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $targetFile = public_path('storage/' . $subPath);

            // Avoid copying onto itself if public/storage is already a working symlink
            $realSource = @realpath($source);
            $realTarget = @realpath($targetFile);
            if ($realSource && $realTarget && $realSource === $realTarget) {
                return;
            }

            @copy($source, $targetFile);
        } catch (\Throwable $e) {
            // Silently ignore if permissions disallow write; route fallback handles it
        }
    }

    /**
     * Mirror all existing files from storage/app/public to public/storage
     * Useful when deploying to shared hosting where storage:link cannot be executed via SSH.
     */
    public static function syncAllStorageFiles(): void
    {
        try {
            $sourceRoot = storage_path('app/public');
            if (!file_exists($sourceRoot)) {
                return;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceRoot, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $subPath = str_replace('\\', '/', substr($item->getPathname(), strlen($sourceRoot) + 1));
                    self::mirrorToPublic($subPath);
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }
}
