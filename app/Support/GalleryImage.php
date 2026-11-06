<?php

namespace App\Support;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Re-encodes an uploaded gallery photo before it is published: upright, at most
 * 1800px on the long side, saved as WebP. Re-encoding drops EXIF data, so a phone
 * photo never publishes the place it was taken.
 */
class GalleryImage
{
    public const MAX_SIDE = 1800;

    public static function store(UploadedFile $file, string $folder = 'gallery'): string
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        if (! $image instanceof GdImage) {
            throw new RuntimeException('Unreadable image.');
        }

        $image = self::upright($image, $file);
        $image = self::fit($image);

        ob_start();
        imagewebp($image, null, 82);
        $bytes = (string) ob_get_clean();

        $path = $folder.'/'.Str::uuid().'.webp';
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }

    private static function upright(GdImage $image, UploadedFile $file): GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }
        $orientation = (int) (@exif_read_data($file->getRealPath())['Orientation'] ?? 1);
        $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

        return $angle ? (imagerotate($image, $angle, 0) ?: $image) : $image;
    }

    private static function fit(GdImage $image): GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $scale = min(1, self::MAX_SIDE / max($w, $h));
        if ($scale >= 1) {
            return $image;
        }

        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);
        $resized = imagecreatetruecolor($nw, $nh);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);

        return $resized;
    }
}
