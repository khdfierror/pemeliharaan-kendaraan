<?php

namespace App\Filament\Resources\JenisPerawatanResource\Pages;

use App\Filament\Resources\JenisPerawatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJenisPerawatan extends ManageRecords
{
    protected static string $resource = JenisPerawatanResource::class;

    protected static ?string $title = 'Jenis Perawatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah'),
        ];
    }
}
