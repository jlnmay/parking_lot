<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyParker extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['customer_name', 'plan', 'expiration_date'];

    protected $casts = ['expiration_date' => 'date'];

    public function vehicles(): HasMany
    {
        return $this->hasMany(ParkerVehicle::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (MonthlyParker $parker) {
            if (!$parker->isForceDeleting()) {
                $parker->vehicles()->delete(); // soft-delete children, not cascade
            }
        });
    }
}
