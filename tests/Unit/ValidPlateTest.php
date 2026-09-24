<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Rules\ValidPlate;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;

class ValidPlateTest extends TestCase
{
    private function passes(mixed $value): bool
    {
        return Validator::make(['plate' => $value], ['plate' => [new ValidPlate]])->passes();
    }

    public static function validCases(): array
    {
        return [['ab-123 cd'], ['1234 ABC'], ['ABC 1234']];
    }

    #[DataProvider('validCases')]
    public function test_valid_layouts_pass(string $value): void
    {
        $this->assertTrue($this->passes($value));
    }

    public function test_empty_whitespace_symbols_and_null_fail(): void
    {
        foreach (['', '   ', '---', null] as $value) {
            $this->assertFalse($this->passes($value), var_export($value, true));
        }
    }

    public function test_limits_come_from_config_and_appear_in_message(): void
    {
        config(['parking.plate.min_length' => 6, 'parking.plate.max_length' => 7]);

        $v = Validator::make(['plate' => 'ABC12'], ['plate' => [new ValidPlate]]);
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('between 6 and 7', $v->errors()->first('plate'));
        $this->assertTrue($this->passes('ABC123'));
        $this->assertFalse($this->passes('ABC12345'));
    }
}
