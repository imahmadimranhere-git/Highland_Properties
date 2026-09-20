<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'settings.all';

    protected $fillable = ['group', 'key', 'value', 'type'];

    // The whole table is tiny, so it is cached as one array and read
    // from memory. This avoids a settings query on every page render.
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Named values() and not all(): Eloquent already defines a static all()
     * and overriding it with a different signature is a PHP error.
     *
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::query()->pluck('value', 'key')->all()
        );
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::values()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );
    }
}
