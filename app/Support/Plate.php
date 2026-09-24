<?php

namespace App\Support;

use Illuminate\Support\Str;

final class Plate
{
    public static function normalize(?string $value): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper(Str::ascii((string) $value)));
    }

    public static function clean(?string $value): string   // what goes in plate_raw
    {
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    public static function isValid(string $normalized): bool
    {
        $len = strlen($normalized);
        return $len >= config('parking.plate.min_length')
            && $len <= config('parking.plate.max_length');
    }
}