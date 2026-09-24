<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Support\Plate;
use PHPUnit\Framework\Attributes\DataProvider;

class PlateTest extends TestCase
{
    public static function normalizeCases(): array
    {
        return [
            'spaces + dashes'   => [' abc-1234 ', 'ABC1234'],
            'space'             => ['ABC 1234', 'ABC1234'],
            'lowercase'         => ['abc1234', 'ABC1234'],
            'two dashes'        => ['AB-123-CD', 'AB123CD'],
            'lower spaced'      => ['ab 123 cd', 'AB123CD'],
            'digits first'      => ['1234 ABC', '1234ABC'],
            'accent'            => ['ÁBC-1234', 'ABC1234'],
            'empty'             => ['', ''],
            'whitespace'        => ['   ', ''],
            'symbols only'      => ['---', ''],
            'null'              => [null, ''],
        ];
    }

    #[DataProvider('normalizeCases')]
    public function test_normalize(?string $input, string $expected): void
    {
        $this->assertSame($expected, Plate::normalize($input));
    }

    public function test_non_latin_input_yields_only_safe_characters(): void
    {
        // Transliteration is library-dependent, so only assert the alphabet.
        $this->assertMatchesRegularExpression('/^[A-Z0-9]*$/', Plate::normalize('АВС 1234'));
    }

    public function test_clean_keeps_case_and_separators(): void
    {
        $this->assertSame('ab-123 cd', Plate::clean('  ab-123   cd '));
        $this->assertSame('', Plate::clean(null));
    }

    public function test_is_valid_uses_config_limits(): void
    {
        config(['parking.plate.min_length' => 5, 'parking.plate.max_length' => 8]);

        $this->assertFalse(Plate::isValid('ABCD'));      // min - 1
        $this->assertTrue(Plate::isValid('ABCDE'));      // min
        $this->assertTrue(Plate::isValid('ABCDEFGH'));   // max
        $this->assertFalse(Plate::isValid('ABCDEFGHI')); // max + 1
    }

    public function test_configured_max_fits_the_column(): void
    {
        $this->assertLessThanOrEqual(16, config('parking.plate.max_length'));
        $this->assertLessThanOrEqual(
            config('parking.plate.max_length'),
            16
        );
    }
}
