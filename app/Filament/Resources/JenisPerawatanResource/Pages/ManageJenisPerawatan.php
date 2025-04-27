<?php

namespace App\Filament\Resources\JenisPerawatanResource\Pages;

use App\Filament\Resources\JenisPerawatanResource;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\ManageRecords;

class ManageJenisPerawatan extends ManageRecords
{
    protected static string $resource = JenisPerawatanResource::class;

    protected static ?string $title = 'Data Jenis Perawatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->modalWidth('xl')
                ->modalHeading('Tambah Jenis Kendaraan')
                ->modalSubmitActionLabel('Tambah')
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
