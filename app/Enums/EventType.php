<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventType: string implements HasLabel, HasColor
{
    case BOOKING = 'booking';
    case TRAVEL = 'travel';

    public function getLabel(): string
    {
        return match ($this) {
            self::BOOKING => 'Booking',
            self::TRAVEL => 'Travel',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BOOKING => 'danger',
            self::TRAVEL => 'info',
        };
    }
}
