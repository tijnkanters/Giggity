<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 3;

    protected static string $view = 'filament.widgets.quick-actions';
}
