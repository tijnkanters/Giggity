<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('new_booking')
                ->label('New Booking')
                ->icon('heroicon-o-plus')
                ->color('danger')
                ->url(EventResource::getUrl('create', ['type' => 'booking'])),
            Actions\Action::make('new_travel')
                ->label('New Travel')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->url(EventResource::getUrl('create', ['type' => 'travel'])),
        ];
    }
}
