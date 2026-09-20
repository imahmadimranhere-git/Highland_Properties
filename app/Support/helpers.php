<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a website setting. The whole settings table is cached in memory,
     * so calling this fifty times in one page costs one query at most.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('money')) {
    /** Formats a price the way the site displays it: PKR 1.25 Crore style. */
    function money(int|float|string|null $amount, string $prefix = 'PKR'): string
    {
        if ($amount === null || $amount === '') {
            return '—';
        }

        $amount = (float) $amount;

        return match (true) {
            $amount >= 10000000 => $prefix . ' ' . rtrim(rtrim(number_format($amount / 10000000, 2), '0'), '.') . ' Crore',
            $amount >= 100000 => $prefix . ' ' . rtrim(rtrim(number_format($amount / 100000, 2), '0'), '.') . ' Lac',
            default => $prefix . ' ' . number_format($amount),
        };
    }
}
