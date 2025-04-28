<?php

namespace App\Filament\Pages;

use App\Models\JenisPerawatan;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.laporan';

    protected static ?int $navigationSort = 5;

    public $laporan;
    public $jumlah_roda;
    public $jenis_perawatan;

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\Fieldset::make()
                        ->schema([
                            Forms\Components\Select::make('laporan')
                                ->options([
                                    'perawatan' => 'Data Perawatan Kendaraan',
                                    'pengeluaran' => 'Rekapitulasi Pengeluaran',
                                ])
                                ->placeholder('Pilih Laporan')
                                ->label('Laporan')
                                ->columnSpanFull()
                                ->required()
                                ->native(false),
                            Forms\Components\Select::make('jumlah_roda')
                                ->label('Jumlah Roda')
                                ->options([
                                    2 => 'Roda 2',
                                    4 => 'Roda 4',
                                ])
                                ->placeholder('Roda 2')
                                ->required()
                                ->native(false),
                            Forms\Components\Select::make('jenis_perawatan')
                                ->options(JenisPerawatan::pluck('nama', 'id'))
                                ->label('Jenis Perawatan')
                                ->searchable()
                                ->placeholder('Pilih Jenis Perawatan')
                                ->required(),
                        ])->columns(1)
                ])
                ->statePath('data'),
        ];
    }

    public function submit()
    {
        $this->fill($this->form->getState());
    }
}
