<?php

namespace App\Filament\Resources\PerawatanResource\Pages;

use App\Filament\Resources\PerawatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerawatan extends EditRecord
{
    protected static string $resource = PerawatanResource::class;

    protected static ?string $title = 'Edit Perawatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
