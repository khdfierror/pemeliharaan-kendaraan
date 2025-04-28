<?php

namespace App\Filament\Resources\MerkResource\Pages;

use App\Filament\Resources\MerkResource;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMerk extends ManageRecords
{
    protected static string $resource = MerkResource::class;

    protected static ?string $title  = 'Master - Merk Kendaraan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->modalWidth('xl')
                ->modalHeading('Tambah')
                ->modalSubmitActionLabel('Tambah Merk')
                ->modalCancelActionLabel('Batal')
                ->extraModalFooterActions(function (StaticAction $action): array {
                    return $action->canCreateAnother() ? [
                        $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                            ->label('Tambah dan Buat Lagi'),
                    ] : [];
                }),
        ];
    }
}
