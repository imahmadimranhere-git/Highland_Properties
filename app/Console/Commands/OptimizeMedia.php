<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Console\Command;

/**
 * php artisan media:optimize          converts images that have no WebP yet
 * php artisan media:optimize --force  rebuilds every image's WebP + thumbnail
 */
class OptimizeMedia extends Command
{
    protected $signature = 'media:optimize {--force : Rebuild derivatives for every image}';

    protected $description = 'Create missing WebP and thumbnail versions of uploaded images';

    public function handle(MediaService $media): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP GD with WebP support is not enabled. Enable extension=gd in php.ini first.');

            return self::FAILURE;
        }

        $query = Media::query()
            ->whereIn('mime', ['image/jpeg', 'image/png', 'image/webp'])
            ->when(! $this->option('force'), fn ($q) => $q->where(fn ($w) => $w->whereNull('webp_path')->orWhereNull('thumb_path')));

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('Every image already has a WebP and a thumbnail.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $done = 0;

        // lazyById keeps memory flat however large the library grows.
        foreach ($query->lazyById(100) as $item) {
            $done += $media->regenerate($item) ? 1 : 0;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Optimised {$done} of {$total} images.");

        return self::SUCCESS;
    }
}
