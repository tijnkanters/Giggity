<div>
    @if ($showModal)
        {{-- Modal overlay --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeModal">
            <div
                style="background: rgb(24 24 27); border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,.5); width: 100%; max-width: 28rem; max-height: 80vh; overflow-y: auto; color: rgba(255,255,255,0.9);">
                {{-- Header --}}
                <div
                    style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">
                        {{ $this->formattedDate }}
                    </h3>
                    <button wire:click="closeModal"
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.375rem; border: none; background: transparent; color: rgba(255,255,255,0.4); cursor: pointer;"
                        onmouseover="this.style.color='rgba(255,255,255,0.8)'"
                        onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div style="padding: 0.75rem 1.25rem 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
                    @if ($this->events->isNotEmpty())
                        @foreach ($this->events as $event)
                            @php
                                $isBooking = $event->type->value === 'booking';
                                $accentColor = $isBooking ? '#ef4444' : '#3b82f6';
                                $timeFrom = $isBooking
                                    ? ($event->set_time_from ? \Carbon\Carbon::parse($event->set_time_from)->format('H:i') : null)
                                    : ($event->time_from ? \Carbon\Carbon::parse($event->time_from)->format('H:i') : null);
                                $timeTo = $isBooking
                                    ? ($event->set_time_to ? \Carbon\Carbon::parse($event->set_time_to)->format('H:i') : null)
                                    : ($event->time_to ? \Carbon\Carbon::parse($event->time_to)->format('H:i') : null);
                            @endphp

                            <a href="{{ $this->getEventUrl($event->id) }}"
                                style="display: block; padding: 0.75rem; border-radius: 0.5rem; border-left: 3px solid {{ $accentColor }}; background: rgba(255,255,255,0.03); text-decoration: none; color: inherit; transition: background 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.07)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                    <span style="font-weight: 500;">{{ $event->name }}</span>
                                    @if ($isBooking && $event->status)
                                                <span style="font-size: 0.7rem; font-weight: 500; padding: 0.125rem 0.5rem; border-radius: 9999px;
                                                                                            {{ match ($event->status->value) {
                                            'confirmed' => 'background: rgba(34,197,94,0.15); color: #4ade80;',
                                            'option' => 'background: rgba(234,179,8,0.15); color: #facc15;',
                                            default => 'background: rgba(156,163,175,0.15); color: #9ca3af;',
                                        } }}">
                                                    {{ $event->status->getLabel() }}
                                                </span>
                                    @endif
                                    @if (!$isBooking)
                                        <span
                                            style="font-size: 0.7rem; font-weight: 500; padding: 0.125rem 0.5rem; border-radius: 9999px; background: rgba(59,130,246,0.15); color: #60a5fa;">
                                            Travel
                                        </span>
                                    @endif
                                </div>

                                <div style="font-size: 0.8rem; opacity: 0.55; margin-top: 0.25rem;">
                                    @if ($isBooking && $event->venue_name)
                                        📍 {{ $event->venue_name }}
                                    @elseif (!$isBooking)
                                        ✈️ {{ $event->leave_from_name ?? '?' }} → {{ $event->arrival_at_name ?? '?' }}
                                        @if ($event->flight_number)
                                            · {{ $event->flight_number }}
                                        @endif
                                    @endif
                                </div>

                                @if ($timeFrom)
                                    <div style="font-size: 0.8rem; opacity: 0.55;">
                                        🕐 {{ $timeFrom }}@if ($timeTo) – {{ $timeTo }}@endif
                                        @if ($isBooking && $event->set_info)
                                            · {{ $event->set_info }}
                                        @endif
                                    </div>
                                @elseif ($isBooking && $event->set_info)
                                    <div style="font-size: 0.8rem; opacity: 0.55;">
                                        🎵 {{ $event->set_info }}
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <div style="text-align: center; padding: 2rem 0; opacity: 0.4;">
                            <p>No events on this date</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>