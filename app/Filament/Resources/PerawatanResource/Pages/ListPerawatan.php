<?php

namespace App\Filament\Resources\PerawatanResource\Pages;

use App\Filament\Resources\PerawatanResource;
use App\Filament\Resources\PerawatanResource\RelationManagers\DetailPerawatanRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPerawatan extends ListRecords
{
    protected static string $resource = PerawatanResource::class;

    protected static ?string $title = 'Data Perawatan Kendaraan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah'),
        ];
    }

    public function getBreadcrumb(): string
    {
        return "Data Perawatan";
    }
}
