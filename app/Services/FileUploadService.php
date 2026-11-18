<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Store an uploaded file to storage/app/public, automatically optimize heavy images
     * for low bandwidth usage, and mirror to public/storage for shared hosting (cPanel).
     *
     * @param UploadedFile $file
     * @param string $folder (e.g. 'products', 'brands', 'categories', 'banners', 'models', 'variants')
     * @return string Relative web path starting with 'storage/' (e.g. 'storage/products/filename.jpg')
     */
    public static function upload(UploadedFile $file, string $folder): string
    {
        // 1. Store via Laravel's configured public disk (storage/app/public/$folder/...)
        $path = $file->store($folder, 'public');

        // 2. Automatically resize/compress oversized raster images (keeps exact filename & transparency)
        $storedFullPath = storage_path('app/public/' . $path);
        $maxWidth = ($folder === 'banners') ? 1600 : 1200;
        self::optimizeImage($storedFullPath, $maxWidth);

        // 3. Mirror optimized file to public/storage so web servers without symlink support serve it statically
        self::mirrorToPublic($path);

        return 'storage/' . $path;
    }

    /**
     * Optimize an image file in-place using PHP GD without changing its filename or extension.
     * Preserves aspect ratio & PNG transparency. Only overwrites if the output is genuinely smaller.
     */
    public static function optimizeImage(string $fullPath, int $maxWidth = 1200, int $jpegQuality = 82): bool
    {
        try {
            if (!extension_loaded('gd') || !file_exists($fullPath) || !is_file($fullPath)) {
                return false;
            }

            $originalSize = filesize($fullPath);
            if ($originalSize === false || $originalSize < 150 * 1024) {
                // Already lightweight (< 150 KB)
                return false;
            }

            $info = @getimagesize($fullPath);
            if (!$info || empty($info[0]) || empty($info[1]) || empty($info[2])) {
                return false;
            }

            [$width, $height, $type] = $info;
            if (!in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
                return false;
            }

            $src = match ($type) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
                IMAGETYPE_PNG => @imagecreatefrompng($fullPath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : false,
                default => false,
            };

            if (!$src) {
                return false;
            }

            // Resize proportionally if wider than $maxWidth
            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) round(($height / $width) * $newWidth);
                $dst = imagecreatetruecolor($newWidth, $newHeight);

                if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                    imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($src);
            } else {
                $dst = $src;
                if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                }
            }

            $tmpPath = $fullPath . '.opt.tmp';
            $saved = false;

            if ($type === IMAGETYPE_JPEG) {
                imageinterlace($dst, true);
                $saved = @imagejpeg($dst, $tmpPath, $jpegQuality);
            } elseif ($type === IMAGETYPE_PNG) {
                $saved = @imagepng($dst, $tmpPath, 9, PNG_ALL_FILTERS);
            } elseif ($type === IMAGETYPE_WEBP && function_exists('imagewebp')) {
                $saved = @imagewebp($dst, $tmpPath, $jpegQuality);
            }

            imagedestroy($dst);

            if ($saved && file_exists($tmpPath)) {
                $newSize = filesize($tmpPath);
                if ($newSize !== false && $newSize > 0 && $newSize < $originalSize) {
                    @rename($tmpPath, $fullPath);
                    return true;
                }
                @unlink($tmpPath);
            }
        } catch (\Throwable $e) {
            // Never fail upload due to optional image optimization
        }

        return false;
    }

    /**
     * Mirror a stored file from storage/app/public to public/storage
     *
     * @param string $subPath Relative path inside public disk, e.g. 'products/abc123xyz.jpg'
     */
    public static function mirrorToPublic(string $subPath): void
    {
        try {
            $cleanSubPath = ltrim(preg_replace('#^storage/#', '', ltrim(str_replace('\\', '/', $subPath), '/')), '/');
            $source = storage_path('app/public/' . $cleanSubPath);
            if (!file_exists($source)) {
                return;
            }

            $targetDir = public_path('storage/' . dirname($cleanSubPath));
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $targetFile = public_path('storage/' . $cleanSubPath);

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
