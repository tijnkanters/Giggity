<x-filament-widgets::widget>
    <div style="display: flex; gap: 0.75rem;">
        @foreach ($this->getStats() as $stat)
            <div
                style="flex: 1; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.75rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                <div style="font-size: 1.5rem; font-weight: 800; color: {{ $stat['color'] }}; line-height: 1;">
                    {{ $stat['value'] }}
                </div>
                <div style="font-size: 0.75rem; opacity: 0.5; line-height: 1.2;">
                    {{ $stat['label'] }}
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>