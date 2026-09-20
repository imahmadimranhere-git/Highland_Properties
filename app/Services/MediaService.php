<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Every uploaded photograph is converted once, here, at upload time:
 *
 *   path        the original the user sent (kept as a fallback / re-export)
 *   webp_path   downscaled to 1600px wide WebP  — used on detail pages
 *   thumb_path  downscaled to 480px wide WebP   — used on every listing grid
 *
 * Doing this on upload rather than on request means a project listing page
 * serves ~40KB thumbnails instead of the 3MB camera JPEGs an agent uploads.
 *
 * Plain GD is used rather than a package: PHP ships with it, it handles the
 * four formats this project accepts, and it keeps composer.json clean.
 */
class MediaService
{
    public function __construct(
        private readonly int $fullWidth = 1600,
        private readonly int $thumbWidth = 480,
        private readonly string $disk = 'public',
    ) {
    }

    public function store(UploadedFile $file, string $folder, ?string $altText = null): Media
    {
        $path = $file->store($folder, $this->disk);
        $absolute = Storage::disk($this->disk)->path($path);

        $media = new Media([
            'disk' => $this->disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $altText,
            'folder' => $folder,
            'uploaded_by' => Auth::id(),
        ]);

        if ($this->isConvertible($file->getMimeType())) {
            [$width, $height] = getimagesize($absolute) ?: [null, null];
            $media->width = $width;
            $media->height = $height;

            $media->webp_path = $this->convert($absolute, $folder, $this->fullWidth, 82, 'full');
            $media->thumb_path = $this->convert($absolute, $folder, $this->thumbWidth, 76, 'thumb');
        }

        $media->save();

        return $media;
    }

    /** Removes every derivative as well as the database row. */
    public function delete(Media $media): void
    {
        $disk = Storage::disk($media->disk);

        foreach (array_filter([$media->path, $media->webp_path, $media->thumb_path]) as $path) {
            $disk->delete($path);
        }

        $media->delete();
    }

    /** SVG is already small and vector; GIF animation would be lost. */
    private function isConvertible(string $mime): bool
    {
        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)
            && function_exists('imagewebp');
    }

    private function convert(string $absolute, string $folder, int $maxWidth, int $quality, string $suffix): ?string
    {
        $image = $this->read($absolute);

        if (! $image) {
            return null;
        }

        $resized = $this->downscale($image, $maxWidth);
        $relative = $folder . '/' . Str::random(32) . '-' . $suffix . '.webp';
        $target = Storage::disk($this->disk)->path($relative);

        Storage::disk($this->disk)->makeDirectory($folder);
        imagewebp($resized, $target, $quality);

        imagedestroy($resized);

        if ($resized !== $image) {
            imagedestroy($image);
        }

        return $relative;
    }

    private function read(string $absolute): \GdImage|false
    {
        $info = getimagesize($absolute);

        $image = match ($info['mime'] ?? null) {
            'image/jpeg' => imagecreatefromjpeg($absolute),
            'image/png' => imagecreatefrompng($absolute),
            'image/webp' => imagecreatefromwebp($absolute),
            default => false,
        };

        // Phone photos carry rotation in EXIF; without this they save sideways.
        if ($image && ($info['mime'] ?? null) === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($absolute);
            $rotate = match ($exif['Orientation'] ?? 1) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };

            if ($rotate !== 0) {
                $rotated = imagerotate($image, $rotate, 0);
                imagedestroy($image);
                $image = $rotated;
            }
        }

        return $image;
    }

    /** Only ever shrinks. A 600px logo is never blown up to 1600px. */
    private function downscale(\GdImage $image, int $maxWidth): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= $maxWidth) {
            return $image;
        }

        $newHeight = (int) round($height * ($maxWidth / $width));
        $canvas = imagecreatetruecolor($maxWidth, $newHeight);

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);

        return $canvas;
    }
}
