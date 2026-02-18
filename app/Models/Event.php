<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\EventType;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'organization_id',
        'created_by_user_id',
        'type',
        'name',
        'date',
        // Booking-specific
        'set_time_from',
        'set_time_to',
        'set_info',
        'extra_information',
        'venue_name',
        'venue_location',
        'hotel_name',
        'hotel_location',
        'hotel_extra_info',
        'status',
        // Travel-specific
        'time_from',
        'time_to',
        'flight_number',
        'leave_from_name',
        'leave_from_location',
        'arrival_at_name',
        'arrival_at_location',
    ];

    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'status' => BookingStatus::class,
            'date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    /**
     * STI: Return the appropriate model subclass based on type.
     */
    public function newFromBuilder($attributes = [], $connection = null): static
    {
        $attributes = (object) $attributes;
        $type = $attributes->type ?? null;

        $model = match ($type) {
            'booking' => new Booking(),
            'travel' => new Travel(),
            default => new self(),
        };

        $model->exists = true;
        $model->setRawAttributes((array) $attributes, true);
        $model->setConnection($connection ?: $this->getConnectionName());
        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    // Relationships

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'event_id');
    }

    // Scopes

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', today());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('date', '<', today());
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeInMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopeBookings(Builder $query): Builder
    {
        return $query->where('type', EventType::BOOKING);
    }

    public function scopeTravels(Builder $query): Builder
    {
        return $query->where('type', EventType::TRAVEL);
    }
}
