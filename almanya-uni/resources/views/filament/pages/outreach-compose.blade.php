<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}

        <div class="flex justify-end mt-4">
            <x-filament::button type="submit" icon="heroicon-o-paper-airplane" color="success">
                Gönder
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
