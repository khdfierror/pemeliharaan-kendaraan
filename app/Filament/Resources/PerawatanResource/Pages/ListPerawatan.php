<?php

namespace App\Filament\Resources\PerawatanResource\Pages;

use App\Filament\Resources\PerawatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerawatan extends ListRecords
{
    protected static string $resource = PerawatanResource::class;

    protected static ?string $title = 'Perawatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah'),
        ];
    }
}
