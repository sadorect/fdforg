<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class GalleryImageProcessor
{
    private const MAX_WIDTH = 2400;
    private const MAX_HEIGHT = 2400;
    private const JPEG_QUALITY = 82;
    private const WEBP_QUALITY = 82;

    public function storeOptimized(UploadedFile $file): string
    {
        $manager = $this->resolveImageManager();

        if (!$manager) {
            return $file->store('gallery', 'public');
        }

        try {
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);

            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $extension = 'jpg';
            }

            $filename = Str::uuid()->toString() . '.' . $extension;
            $path = 'gallery/' . $filename;

            $encoded = match ($extension) {
                'png' => $image->toPng(),
                'webp' => $image->toWebp(self::WEBP_QUALITY),
                default => $image->toJpeg(self::JPEG_QUALITY),
            };

            Storage::disk('public')->put($path, (string) $encoded);

            return $path;
        } catch (\Throwable $exception) {
            Log::warning('Gallery image optimization failed; storing original upload.', [
                'filename' => $file->getClientOriginalName(),
                'error' => $exception->getMessage(),
            ]);

            return $file->store('gallery', 'public');
        }
    }

    private function resolveImageManager(): ?ImageManager
    {
        if (class_exists(\Imagick::class)) {
            return new ImageManager(new ImagickDriver());
        }

        if (extension_loaded('gd') || function_exists('gd_info')) {
            return new ImageManager(new GdDriver());
        }

        return null;
    }
}
