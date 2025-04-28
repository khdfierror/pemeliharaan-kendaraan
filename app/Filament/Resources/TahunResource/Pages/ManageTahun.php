<?php

namespace App\Filament\Resources\TahunResource\Pages;

use App\Filament\Resources\TahunResource;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTahun extends ManageRecords
{
    protected static string $resource = TahunResource::class;

    protected static ?string $title = 'Master - Tahun';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->modalWidth('xl')
                ->modalHeading('Tambah Tahun')
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
