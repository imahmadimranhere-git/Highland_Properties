<?php

namespace App\Services;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams rows straight to the browser as CSV.
 *
 * Nothing is built in memory: rows are written one at a time as the query
 * cursor produces them, so a 50,000-lead export uses the same memory as a
 * 50-lead one. The UTF-8 byte order mark makes Excel read Urdu names and
 * the rupee sign correctly instead of showing garbled characters.
 */
class CsvExporter
{
    /**
     * @param  array<int, string>  $headings
     * @param  Closure(Closure(array): void): void  $producer  receives a $write callback
     */
    public static function stream(string $filename, array $headings, Closure $producer): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $producer) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headings);

            $producer(function (array $row) use ($out) {
                fputcsv($out, $row);
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
