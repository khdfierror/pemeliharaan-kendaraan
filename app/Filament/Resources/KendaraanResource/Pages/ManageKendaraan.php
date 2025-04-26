<?php

namespace App\Filament\Resources\KendaraanResource\Pages;

use App\Filament\Resources\KendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKendaraan extends ManageRecords
{
    protected static string $resource = KendaraanResource::class;

    protected static ?string $title = 'Kendaraan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->modalWidth('xl'),
        ];
    }
}
