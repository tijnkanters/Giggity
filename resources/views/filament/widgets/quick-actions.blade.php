<x-filament-widgets::widget>
    <x-filament::section heading="Quick Actions">
        <div class="flex gap-3">
            <x-filament::button icon="heroicon-o-plus" color="danger" tag="a"
                :href="\App\Filament\Resources\EventResource::getUrl('create', ['type' => 'booking'])">
                New Booking
            </x-filament::button>

            <x-filament::button icon="heroicon-o-paper-airplane" color="info" tag="a"
                :href="\App\Filament\Resources\EventResource::getUrl('create', ['type' => 'travel'])">
                New Travel
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>