<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Event;
use Filament\Widgets\Widget;

class StatsOverview extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.stats-overview';

    public function getStats(): array
    {
        return [
            [
                'label' => 'Upcoming',
                'value' => Event::upcoming()->count(),
                'color' => '#f59e0b',
            ],
            [
                'label' => 'Confirmed',
                'value' => Booking::confirmed()->count(),
                'color' => '#22c55e',
            ],
            [
                'label' => 'Options',
                'value' => Booking::pendingOption()->count(),
                'color' => '#f97316',
            ],
        ];
    }
}
