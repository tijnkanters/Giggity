<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Models\File;
use App\Enums\EventType;
use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Travel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    /**
     * Remove Booking/Travel global scopes so the unified list shows both types.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope('booking')
            ->withoutGlobalScope('travel');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('type')
                ->default(fn() => request()->query('type', 'booking')),

            // --- Shared fields ---
            Forms\Components\Section::make('Event Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('date')
                        ->required()
                        ->minDate(fn(?Model $record): ?string => $record ? null : today()->toDateString()),
                ])
                ->columns(2),

            // --- Booking-specific ---
            Forms\Components\Section::make('Set Times')
                ->schema([
                    Forms\Components\TimePicker::make('set_time_from')
                        ->label('Set Time From')
                        ->seconds(false),
                    Forms\Components\TimePicker::make('set_time_to')
                        ->label('Set Time To')
                        ->seconds(false),
                    Forms\Components\Textarea::make('set_info')
                        ->label('Set Info')
                        ->rows(3),
                    Forms\Components\Textarea::make('extra_information')
                        ->label('Extra Information')
                        ->rows(3),
                    Forms\Components\Select::make('status')
                        ->options(BookingStatus::class)
                        ->default(BookingStatus::OPTION)
                        ->required(),
                ])
                ->columns(2)
                ->visible(fn(Get $get): bool => $get('type') === 'booking'),

            Forms\Components\Section::make('Venue')
                ->schema([
                    Forms\Components\TextInput::make('venue_name')
                        ->label('Venue Name'),
                    Forms\Components\TextInput::make('venue_location')
                        ->label('Venue Location')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('openVenueMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Get $get): ?string => $get('venue_location')
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($get('venue_location'))
                                    : null)
                                ->openUrlInNewTab()
                                ->visible(fn(Get $get): bool => filled($get('venue_location')))
                        ),
                ])
                ->columns(2)
                ->visible(fn(Get $get): bool => $get('type') === 'booking'),

            Forms\Components\Section::make('Hotel')
                ->schema([
                    Forms\Components\TextInput::make('hotel_name')
                        ->label('Hotel Name'),
                    Forms\Components\TextInput::make('hotel_location')
                        ->label('Hotel Location')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('openHotelMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Get $get): ?string => $get('hotel_location')
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($get('hotel_location'))
                                    : null)
                                ->openUrlInNewTab()
                                ->visible(fn(Get $get): bool => filled($get('hotel_location')))
                        ),
                    Forms\Components\Textarea::make('hotel_extra_info')
                        ->label('Hotel Extra Info')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn(Get $get): bool => $get('type') === 'booking'),

            // --- Travel-specific ---
            Forms\Components\Section::make('Travel Details')
                ->schema([
                    Forms\Components\TimePicker::make('time_from')
                        ->label('Departure Time')
                        ->seconds(false),
                    Forms\Components\TimePicker::make('time_to')
                        ->label('Arrival Time')
                        ->seconds(false),
                    Forms\Components\TextInput::make('flight_number')
                        ->label('Flight Number'),
                ])
                ->columns(3)
                ->visible(fn(Get $get): bool => $get('type') === 'travel'),

            Forms\Components\Section::make('Departure')
                ->schema([
                    Forms\Components\TextInput::make('leave_from_name')
                        ->label('Departure From'),
                    Forms\Components\TextInput::make('leave_from_location')
                        ->label('Location')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('openDepartureMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Get $get): ?string => $get('leave_from_location')
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($get('leave_from_location'))
                                    : null)
                                ->openUrlInNewTab()
                                ->visible(fn(Get $get): bool => filled($get('leave_from_location')))
                        ),
                ])
                ->columns(2)
                ->visible(fn(Get $get): bool => $get('type') === 'travel'),

            Forms\Components\Section::make('Arrival')
                ->schema([
                    Forms\Components\TextInput::make('arrival_at_name')
                        ->label('Arrival At'),
                    Forms\Components\TextInput::make('arrival_at_location')
                        ->label('Location')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('openArrivalMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Get $get): ?string => $get('arrival_at_location')
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($get('arrival_at_location'))
                                    : null)
                                ->openUrlInNewTab()
                                ->visible(fn(Get $get): bool => filled($get('arrival_at_location')))
                        ),
                ])
                ->columns(2)
                ->visible(fn(Get $get): bool => $get('type') === 'travel'),

            // --- Files (both types) ---
            Forms\Components\Section::make('Files')
                ->schema([
                    Forms\Components\FileUpload::make('attachments')
                        ->multiple()
                        ->maxSize(10240)
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->directory('event-files')
                        ->disk('public')
                        ->visibility('public')
                        ->previewable(false)
                        ->reorderable()
                        ->openable()
                        ->downloadable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('venue_name')
                    ->label('Venue')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('route')
                    ->label('Route')
                    ->getStateUsing(fn(Model $record): ?string => $record instanceof Travel ? $record->route : null)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('flight_number')
                    ->label('Flight')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(EventType::class),
                Tables\Filters\SelectFilter::make('status')
                    ->options(BookingStatus::class),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder => $query
                            ->when($data['from'], fn(Builder $q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn(Builder $q, $date) => $q->whereDate('date', '<=', $date))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Event Details')
                ->schema([
                    Infolists\Components\TextEntry::make('type')
                        ->badge(),
                    Infolists\Components\TextEntry::make('name')
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('date')
                        ->weight('bold')
                        ->date(),
                    Infolists\Components\TextEntry::make('creator.name')
                        ->label('Created By')
                        ->weight('bold'),
                ])
                ->columns(2),

            // Booking details
            Infolists\Components\Section::make('Set Times')
                ->schema([
                    Infolists\Components\TextEntry::make('set_time_from')
                        ->label('From')
                        ->weight('bold')
                        ->time(),
                    Infolists\Components\TextEntry::make('set_time_to')
                        ->label('To')
                        ->weight('bold')
                        ->time(),
                    Infolists\Components\TextEntry::make('set_info')
                        ->label('Set Info')
                        ->weight('bold')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('extra_information')
                        ->label('Extra Information')
                        ->weight('bold')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('status')
                        ->badge(),
                ])
                ->columns(2)
                ->visible(fn(Model $record): bool => $record->type === EventType::BOOKING),

            Infolists\Components\Section::make('Venue')
                ->schema([
                    Infolists\Components\TextEntry::make('venue_name')
                        ->label('Name')
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('venue_location')
                        ->label('Location')
                        ->weight('bold')
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('openVenueMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Model $record): ?string => $record->venue_location
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($record->venue_location)
                                    : null)
                                ->openUrlInNewTab()
                        ),
                ])
                ->columns(2)
                ->visible(fn(Model $record): bool => $record->type === EventType::BOOKING),

            Infolists\Components\Section::make('Hotel')
                ->schema([
                    Infolists\Components\TextEntry::make('hotel_name')
                        ->label('Name')
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('hotel_location')
                        ->label('Location')
                        ->weight('bold')
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('openHotelMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Model $record): ?string => $record->hotel_location
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($record->hotel_location)
                                    : null)
                                ->openUrlInNewTab()
                        ),
                    Infolists\Components\TextEntry::make('hotel_extra_info')
                        ->label('Extra Info')
                        ->weight('bold')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn(Model $record): bool => $record->type === EventType::BOOKING),

            // Travel details
            Infolists\Components\Section::make('Travel Details')
                ->schema([
                    Infolists\Components\TextEntry::make('time_from')
                        ->label('Departure Time')
                        ->weight('bold')
                        ->time(),
                    Infolists\Components\TextEntry::make('time_to')
                        ->label('Arrival Time')
                        ->weight('bold')
                        ->time(),
                    Infolists\Components\TextEntry::make('flight_number')
                        ->weight('bold')
                        ->label('Flight Number'),
                ])
                ->columns(3)
                ->visible(fn(Model $record): bool => $record->type === EventType::TRAVEL),

            Infolists\Components\Section::make('Route')
                ->schema([
                    Infolists\Components\TextEntry::make('leave_from_name')
                        ->label('Departure From')
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('leave_from_location')
                        ->label('Departure Location')
                        ->weight('bold')
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('openDepartureMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Model $record): ?string => $record->leave_from_location
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($record->leave_from_location)
                                    : null)
                                ->openUrlInNewTab()
                        ),
                    Infolists\Components\TextEntry::make('arrival_at_name')
                        ->label('Arrival At')
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('arrival_at_location')
                        ->label('Arrival Location')
                        ->weight('bold')
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('openArrivalMap')
                                ->icon('heroicon-o-map-pin')
                                ->url(fn(Model $record): ?string => $record->arrival_at_location
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($record->arrival_at_location)
                                    : null)
                                ->openUrlInNewTab()
                        ),
                ])
                ->columns(2)
                ->visible(fn(Model $record): bool => $record->type === EventType::TRAVEL),

            // Files
            Infolists\Components\Section::make('Files')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('files')
                        ->hiddenLabel()
                        ->schema([
                            Infolists\Components\TextEntry::make('filename')
                                ->hiddenLabel()
                                ->icon('heroicon-o-paper-clip')
                                ->suffixActions([
                                    Infolists\Components\Actions\Action::make('view')
                                        ->icon('heroicon-o-eye')
                                        ->url(fn(File $record): string => asset('storage/' . $record->path))
                                        ->openUrlInNewTab(),
                                    Infolists\Components\Actions\Action::make('download')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->action(fn(File $record) => response()->download(
                                            storage_path('app/public/' . $record->path),
                                            $record->filename,
                                        )),
                                ]),
                        ])
                        ->columns(1),
                ])
                ->visible(fn(Model $record): bool => $record->files->isNotEmpty()),

            // Timestamps
            Infolists\Components\Section::make('Metadata')
                ->schema([
                    Infolists\Components\TextEntry::make('created_at')
                        ->dateTime(),
                    Infolists\Components\TextEntry::make('updated_at')
                        ->dateTime(),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
