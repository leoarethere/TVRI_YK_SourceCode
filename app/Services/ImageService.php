<?php

namespace App\Services;

use App\Support\PublicStorageWriter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    /**
     * Processes, compresses and stores an image.
     *
     * @param  UploadedFile  $file  The uploaded image file.
     * @param  string  $folder  The storage folder (e.g., 'post-images').
     * @return string|null The path to the stored image.
     *
     * @throws \Exception
     */
    public function processAndStore(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1200,
        int $quality = 70
    ): string {
        try {
            $filename = Str::random(40).'.jpg';
            $path = $folder.'/'.$filename;

            $manager = new ImageManager(new Driver);
            $image = $manager->read($file);

            $image->scaleDown(width: $maxWidth);

            $encodedImage = $image->toJpeg(quality: $quality);

            PublicStorageWriter::put($path, (string) $encodedImage);

            return $path;
        } catch (\Exception $e) {
            Log::error("Image processing error in folder {$folder}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Deletes an image from public storage.
     *
     * @param  string|null  $path  The path to the image.
     */
    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function deleteImageQuietly(?string $path): void
    {
        try {
            $this->deleteImage($path);
        } catch (\Throwable $exception) {
            Log::warning('Unable to delete image from storage.', [
                'path' => $path,
                'exception' => $exception,
            ]);
        }
    }
}
