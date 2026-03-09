<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasLabel, HasColor
{
    case OPTION = 'option';
    case OFFER_RECEIVED = 'offer_received';
    case OFFER_REJECTED = 'offer_rejected';
    case CONTRACT_SENT = 'contract_sent';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPTION => 'Option',
            self::OFFER_RECEIVED => 'Offer Received',
            self::OFFER_REJECTED => 'Offer Rejected',
            self::CONTRACT_SENT => 'Contract Sent',
            self::CONFIRMED => 'Confirmed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OPTION,
            self::OFFER_RECEIVED,
            self::OFFER_REJECTED,
            self::CONTRACT_SENT => 'warning',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function isPending(): bool
    {
        return in_array($this, [
            self::OPTION,
            self::OFFER_RECEIVED,
            self::OFFER_REJECTED,
            self::CONTRACT_SENT,
        ]);
    }
}
