<?php

namespace App\Livewire;

use App\Models\Event;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SimpleCalendar extends Component
{
    public int $currentMonth;
    public int $currentYear;

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function selectDate(string $date): void
    {
        $this->dispatch('date-clicked', date: $date);
    }

    #[Computed]
    public function monthName(): string
    {
        return Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->format('F Y');
    }

    #[Computed]
    public function calendarDays(): array
    {
        $firstOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstOfMonth->daysInMonth;

        // Get events for this month (without Booking/Travel global scopes)
        $events = Event::withoutGlobalScope('booking')
            ->withoutGlobalScope('travel')
            ->where(function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->orWhereNull('status');
            })
            ->inMonth($this->currentMonth, $this->currentYear)
            ->get();

        // Group events by date and type
        $eventsByDate = [];
        foreach ($events as $event) {
            $dateKey = $event->date->format('Y-m-d');
            if (!isset($eventsByDate[$dateKey])) {
                $eventsByDate[$dateKey] = ['hasBooking' => false, 'hasTravel' => false];
            }
            if ($event->type->value === 'booking') {
                $eventsByDate[$dateKey]['hasBooking'] = true;
            } else {
                $eventsByDate[$dateKey]['hasTravel'] = true;
            }
        }

        // Build calendar grid
        $days = [];

        // Day of week offset (Monday = 0, Sunday = 6)
        $startDayOfWeek = ($firstOfMonth->dayOfWeek + 6) % 7; // Convert Sunday=0 to Monday=0

        // Empty cells before first day
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $days[] = [
                'day' => null,
                'date' => null,
                'hasBooking' => false,
                'hasTravel' => false,
                'isToday' => false,
            ];
        }

        // Actual days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, $day);
            $dateKey = $date->format('Y-m-d');

            $days[] = [
                'day' => $day,
                'date' => $dateKey,
                'hasBooking' => $eventsByDate[$dateKey]['hasBooking'] ?? false,
                'hasTravel' => $eventsByDate[$dateKey]['hasTravel'] ?? false,
                'isToday' => $date->isToday(),
            ];
        }

        return $days;
    }

    public function render()
    {
        return view('livewire.simple-calendar');
    }
}
