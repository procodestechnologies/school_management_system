<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * ZKTeco device sync needs a real local filesystem path to push a photo's
 * actual bytes onto biometric hardware. Profile photos live on Cloudinary,
 * which has no local path, so this downloads the photo to a temp file for
 * the duration of the callback and cleans it up afterward.
 */
class ProfilePhotoResolver
{
    /**
     * @template TReturn
     *
     * @param  callable(?string $localPath): TReturn  $callback
     * @return TReturn
     */
    public function withLocalPath(?string $storedPath, callable $callback): mixed
    {
        if (! $storedPath) {
            return $callback(null);
        }

        $disk = Storage::disk('cloudinary');

        if (! $disk->exists($storedPath)) {
            return $callback(null);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'photo_').'.jpg';
        file_put_contents($tempPath, $disk->get($storedPath));

        try {
            return $callback($tempPath);
        } finally {
            @unlink($tempPath);
        }
    }
}
