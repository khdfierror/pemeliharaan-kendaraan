<?php

namespace App\Filament\Resources\PerawatanResource\Pages;

use App\Filament\Resources\PerawatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerawatan extends CreateRecord
{
    protected static string $resource = PerawatanResource::class;

    protected static ?string $title = 'Tambah Perawatan';
}
