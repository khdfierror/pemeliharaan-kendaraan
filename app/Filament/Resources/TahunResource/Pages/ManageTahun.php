<?php

namespace App\Filament\Resources\TahunResource\Pages;

use App\Filament\Resources\TahunResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTahun extends ManageRecords
{
    protected static string $resource = TahunResource::class;

    protected static ?string $title = 'Tahun';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->modalWidth('xl'),
        ];
    }
}
