<x-filament::page>
    <div class="space-y-4">
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            <x-filament::button type="submit" class="mt-4">
                Tampilkan
            </x-filament::button>
        </form>
    </div>
</x-filament::page>
