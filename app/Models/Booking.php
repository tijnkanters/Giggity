<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Event
{
    protected $table = 'events';

    protected $attributes = [
        'type' => 'booking',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('booking', function (Builder $query) {
            $query->where('type', 'booking');
        });
    }

    public function getStatusColorAttribute(): ?string
    {
        return $this->status?->getColor();
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::CONFIRMED);
    }

    public function scopePendingOption(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::OPTION);
    }
}
