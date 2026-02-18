<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrganizationRole: string implements HasLabel
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MEMBER = 'member';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::MANAGER => 'Manager',
            self::MEMBER => 'Member',
        };
    }
}
