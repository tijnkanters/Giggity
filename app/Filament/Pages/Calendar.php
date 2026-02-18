<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Calendar';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.calendar';

    public function getTitle(): string
    {
        return 'Calendar';
    }
}
