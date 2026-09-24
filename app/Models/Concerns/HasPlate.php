<?php

namespace App\Models\Concerns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\Plate;

trait HasPlate
{

    protected function plateRaw(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null
                ? ['plate_raw' => null, 'plate' => null]
                : ['plate_raw' => Plate::clean($value), 'plate' => Plate::normalize($value)],
        );
    }

    public function scopeWherePlate(Builder $q, string $input): Builder
    {
        return $q->where('plate', Plate::normalize($input));
    }
}