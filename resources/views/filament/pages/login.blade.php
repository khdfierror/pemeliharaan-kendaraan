{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
    scopes: $this->getRenderHookScopes()) }}
        <x-filament-panels::form id="form" wire:submit="authenticate">
            <div class="text-center font-semibold text-lg">Badan Pengelolaan Keuangan dan Aset Daerah
                <p>Provinsi Kalimantan Timur</p>
            </div>
            <div class="text-center font-semibold text-2xl">Sistem Informasi Perawatan Kendaraan Bermotor</div>
            {{ $this->form }}

            <x-filament-panels::form.actions :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()" />
        </x-filament-panels::form>
{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
    scopes: $this->getRenderHookScopes()) }}