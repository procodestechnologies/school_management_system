<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AvifEncoder;
use Intervention\Image\Encoders\BmpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncoderInterface;

/**
 * Compresses uploaded images before they hit storage. Quality-80 encoding
 * on lossy formats (JPEG/WebP/AVIF) cuts file size substantially with
 * negligible visible loss. Lossless formats (PNG/GIF/BMP) have no quality
 * knob, so they're just re-encoded through the same pipeline as-is.
 */
class ImageCompressionService
{
    private const QUALITY = 80;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    public function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');

        $encoded = $this->manager->decodePath($file->getRealPath())
            ->encode($this->encoderFor($extension));

        $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;

        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }

    private function encoderFor(string $extension): EncoderInterface
    {
        return match ($extension) {
            'jpg', 'jpeg' => new JpegEncoder(quality: self::QUALITY),
            'webp' => new WebpEncoder(quality: self::QUALITY),
            'avif' => new AvifEncoder(quality: self::QUALITY),
            'gif' => new GifEncoder,
            'bmp' => new BmpEncoder,
            default => new PngEncoder,
        };
    }
}
