<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Travel extends Event
{
    protected $table = 'events';

    protected $attributes = [
        'type' => 'travel',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('travel', function (Builder $query) {
            $query->where('type', 'travel');
        });
    }

    public function getRouteAttribute(): string
    {
        return ($this->leave_from_name ?? '?') . ' → ' . ($this->arrival_at_name ?? '?');
    }
}
