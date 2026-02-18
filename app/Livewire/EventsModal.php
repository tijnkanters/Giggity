<?php

namespace App\Livewire;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class EventsModal extends Component
{
    public ?string $selectedDate = null;
    public bool $showModal = false;

    #[On('date-clicked')]
    public function openModal(string $date): void
    {
        $this->selectedDate = $date;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedDate = null;
    }

    #[Computed]
    public function events()
    {
        if (!$this->selectedDate) {
            return collect();
        }

        return Event::withoutGlobalScope('booking')
            ->withoutGlobalScope('travel')
            ->forDate($this->selectedDate)
            ->where(function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->orWhereNull('status');
            })
            ->orderByRaw("COALESCE(set_time_from, time_from) ASC")
            ->get();
    }

    public function getEventUrl(int $eventId): string
    {
        return EventResource::getUrl('view', ['record' => $eventId]);
    }

    #[Computed]
    public function formattedDate(): string
    {
        return $this->selectedDate
            ? \Carbon\Carbon::parse($this->selectedDate)->format('l, F j, Y')
            : '';
    }

    public function render()
    {
        return view('livewire.events-modal');
    }
}
