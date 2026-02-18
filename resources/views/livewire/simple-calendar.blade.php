<div>
    {{-- Calendar Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <button wire:click="previousMonth"
            style="display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer; color: inherit; transition: background 0.15s;"
            onmouseover="this.style.background='rgba(128,128,128,0.15)'"
            onmouseout="this.style.background='transparent'">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0;">
            {{ $this->monthName }}
        </h2>

        <button wire:click="nextMonth"
            style="display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer; color: inherit; transition: background 0.15s;"
            onmouseover="this.style.background='rgba(128,128,128,0.15)'"
            onmouseout="this.style.background='transparent'">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    {{-- Day of week headers --}}
    <div style="display: grid; grid-template-columns: repeat(7, 1fr); margin-bottom: 0.25rem;">
        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
            <div style="text-align: center; font-size: 0.75rem; font-weight: 600; padding: 0.5rem 0; opacity: 0.5;">
                {{ $dayName }}
            </div>
        @endforeach
    </div>

    {{-- Calendar grid --}}
    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px;">
        @foreach ($this->calendarDays as $day)
            @if ($day['day'] === null)
                <div style="height: 48px;"></div>
            @else
                @php
                    if ($day['hasBooking'] && $day['hasTravel']) {
                        $ringStyle = 'box-shadow: 0 0 0 2.5px #ef4444; outline: 2.5px solid #3b82f6; outline-offset: 4px;';
                    } elseif ($day['hasBooking']) {
                        $ringStyle = 'box-shadow: 0 0 0 2px #ef4444;';
                    } elseif ($day['hasTravel']) {
                        $ringStyle = 'box-shadow: 0 0 0 2px #3b82f6;';
                    } else {
                        $ringStyle = '';
                    }

                    $todayBg = $day['isToday']
                        ? 'background: rgba(245, 158, 11, 0.2); font-weight: 700;'
                        : '';

                    $cursor = ($day['hasBooking'] || $day['hasTravel']) ? 'pointer' : 'default';
                @endphp

                <div style="display: flex; align-items: center; justify-content: center; height: 56px; overflow: visible;">
                    <button wire:click="selectDate('{{ $day['date'] }}')"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: none; background: transparent; cursor: {{ $cursor }}; font-size: 0.875rem; color: inherit; transition: background 0.15s; {{ $ringStyle }} {{ $todayBg }}"
                        @if(!$day['hasBooking'] && !$day['hasTravel'] && !$day['isToday'])
                            onmouseover="this.style.background='rgba(128,128,128,0.15)'"
                        onmouseout="this.style.background='transparent'" @endif>
                        {{ $day['day'] }}
                    </button>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Legend --}}
    <div style="display: flex; align-items: center; gap: 1.5rem; margin-top: 1rem; font-size: 0.75rem; opacity: 0.5;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span
                style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; box-shadow: 0 0 0 2px #ef4444;"></span>
            <span>Booking</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span
                style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; box-shadow: 0 0 0 2px #3b82f6;"></span>
            <span>Travel</span>
        </div>
    </div>
</div>