<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}

        <x-slot name="footerActions">
            <x-filament::button
                wire:click="process"
                color="success"
                icon="heroicon-o-play"
            >
                Proses Monitoring
            </x-filament::button>
        </x-slot>
    </x-filament::section>
</x-filament-panels::page>
