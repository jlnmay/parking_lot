<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkerVehicle extends Model
{
    use SoftDeletes;

    protected $fillable = ['monthly_parker_id', 'plate'];

    public function parker(): BelongsTo
    {
        return $this->belongsTo(MonthlyParker::class, 'monthly_parker_id');
    }
}
