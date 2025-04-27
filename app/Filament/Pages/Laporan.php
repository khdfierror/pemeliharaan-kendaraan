<?php

namespace App\Filament\Pages;

use App\Models\JenisPerawatan;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class Laporan extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.laporan';

    public $laporan;
    public $jumlah_roda;
    public $jenis_perawatan;

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\Select::make('laporan')
                        ->options([
                            'perawatan' => 'Data Perawatan Kendaraan',
                            'pengeluaran' => 'Rekapitulasi Pengeluaran',
                        ])
                        ->label('Laporan')
                        ->required(),

                    Forms\Components\Select::make('jumlah_roda')
                        ->options([
                            2 => 'Roda 2',
                            4 => 'Roda 4',
                        ])
                        ->label('Jumlah Roda')
                        ->required(),

                    Forms\Components\Select::make('jenis_perawatan')
                        ->options(JenisPerawatan::pluck('nama', 'id'))
                        ->label('Jenis Perawatan')
                        ->searchable()
                        ->required(),
                ])
                ->statePath('data'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return \App\Models\Perawatan::query()
            ->when($this->jumlah_roda, function ($query) {
                $query->whereHas('kendaraan', fn ($q) => $q->where('jumlah_roda', $this->jumlah_roda));
            })
            ->when($this->jenis_perawatan, function ($query) {
                $query->where('jenis_perawatan_id', $this->jenis_perawatan);
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('kendaraan.nama')
                ->label('Nama Kendaraan'),
            Tables\Columns\TextColumn::make('tanggal_nota')
                ->label('Tanggal Perawatan'),
            Tables\Columns\TextColumn::make('keterangan')
                ->label('Keterangan'),
        ];
    }

    public function submit()
{
    $this->fill($this->form->getState());
}


    // protected function getHeaderActions(): array
    // {
    //     return [
    //     ];
    // }
}
