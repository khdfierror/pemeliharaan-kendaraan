<?php

namespace App\Filament\Pages;

use App\Exports\PerawatanBulanIniExport;
use App\Models\JenisPerawatan;
use Filament\Pages\Page;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class PerawatanBulanIni extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Laporan Perawatan Bulan Ini';
    protected static ?string $slug = 'perawatan-bulan-ini';
    protected static string $view = 'filament.pages.perawatan-bulan-ini';

    public $jumlah_roda;
    public $jenis_perawatan_id;

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->options([
                        2 => 'Roda 2',
                        4 => 'Roda 4',
                    ])
                    ->placeholder('Pilih Jumlah Roda')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('jenis_perawatan_id')
                    ->label('Jenis Perawatan')
                    ->options(JenisPerawatan::pluck('nama', 'id'))
                    ->placeholder('Pilih Jenis Perawatan')
                    ->required()
                    ->searchable(),
            ]);
    }

    public function submit()
    {
        if (!$this->jumlah_roda || !$this->jenis_perawatan_id) {
            Notification::make()
                ->title('Gagal')
                ->body('Pilih Jumlah Roda dan Jenis Perawatan.')
                ->danger()
                ->send();
            return;
        }

        return Excel::download(
            new PerawatanBulanIniExport($this->jumlah_roda, $this->jenis_perawatan_id),
            'perawatan_bulan_ini.xlsx'
        );
    }
}
