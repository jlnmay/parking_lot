<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPlate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MonthlyParker;

class ParkerVehicle extends Model
{
    use SoftDeletes;
    use HasPlate;
    use HasFactory;

    protected $fillable = ['monthly_parker_id', 'plate_raw'];

    public function parker(): BelongsTo
    {
        return $this->belongsTo(MonthlyParker::class, 'monthly_parker_id');
    }
}
