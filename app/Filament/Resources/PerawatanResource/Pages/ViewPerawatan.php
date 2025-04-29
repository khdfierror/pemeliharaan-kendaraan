<?php

namespace App\Filament\Resources\PerawatanResource\Pages;

use App\Filament\Resources\PerawatanResource;
use App\Filament\Resources\PerawatanResource\RelationManagers\DetailPerawatanRelationManager;
use App\Models\Perawatan;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPerawatan extends ViewRecord
{
    protected static string $resource = PerawatanResource::class;

    public function getTitle(): string | Htmlable
    {
        return "Detail Perawatan";
    }

    public function getRelationManagers(): array
    {
        return [
            DetailPerawatanRelationManager::class,
        ];
    }

    public function getBreadcrumb(): string
    {
        return "Detail Perawatan";
    }

    // protected function getActions(): array
    // {
    //     return [];
    // }
}
