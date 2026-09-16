<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            // ticket_number depends on id, so generate after insert
        });

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
