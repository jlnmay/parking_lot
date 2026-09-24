<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use App\Support\Plate;

class ValidPlate implements ValidationRule
{
    public bool $implicit = true;   // runs on empty input too, so the rule is self-contained

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Plate::isValid(Plate::normalize($value))) {
            $fail(__('validation.plate', [
                'min' => config('parking.plate.min_length'),
                'max' => config('parking.plate.max_length'),
            ]));
        }
    }
}
