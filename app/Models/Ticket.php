<?php

namespace App\Models;

use App\Models\Concerns\HasPlate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    use HasPlate;

    protected $fillable = [
        'plate_raw',   // NOT 'plate'
    ];

    protected static function booted(): void
    {
        static::created(function (Ticket $ticket) {
            $ticket->ticket_number = 'TKT-' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT);
            $ticket->saveQuietly();
        });
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}