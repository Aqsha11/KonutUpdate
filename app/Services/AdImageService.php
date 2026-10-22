<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdImageService
{
    public const MAX_WIDTH = 800;

    public const MAX_HEIGHT = 1200;

    public const QUALITY = 85;

    public function storeUploadedFile(UploadedFile $file): string
    {
        return $this->storePath($file->getRealPath());
    }

    public function storePath(string $absolutePath): string
    {
        $path = $this->newPath();
        Storage::disk('public')->put($path, $this->encode($absolutePath));

        return $path;
    }

    /**
     * @return array{contents: string, width: int, height: int}
     */
    public function optimized(string $absolutePath): array
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($absolutePath);
        $image->resizeDown(self::MAX_WIDTH, self::MAX_HEIGHT);

        $width = $image->width();
        $height = $image->height();
        $contents = (string) $image->toWebp(self::QUALITY);

        return [
            'contents' => $contents,
            'width' => $width,
            'height' => $height,
        ];
    }

    public function newPath(): string
    {
        return 'ads/'.Str::random(40).'.webp';
    }

    private function encode(string $absolutePath): string
    {
        return $this->optimized($absolutePath)['contents'];
    }
}
