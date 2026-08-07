<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * ZKTeco device sync needs a real local filesystem path to push a photo's
 * actual bytes onto biometric hardware. Profile photos live on the local
 * "public" disk, so this just resolves the stored path to its absolute
 * filesystem path.
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

        $disk = Storage::disk('public');

        if (! $disk->exists($storedPath)) {
            return $callback(null);
        }

        return $callback($disk->path($storedPath));
    }
}
