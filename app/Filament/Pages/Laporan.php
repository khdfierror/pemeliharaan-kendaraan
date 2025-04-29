<?php

namespace App\Filament\Pages;

use App\Exports\DaftarKendaraanExport;
use App\Exports\PerawatanBulanIniExport;
use App\Exports\PerawatanKendaraanBermotorExport;
use App\Models\JenisPerawatan;
use Filament\Pages\Page;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Laporan';
    protected static ?string $slug = 'laporan';
    protected static string $view = 'filament.pages.laporan';

    public $laporan;
    public $jumlah_roda;
    public $jenis_perawatan_id;

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('laporan')
                    ->label('Pilih Laporan')
                    ->options([
                        'daftar_kendaraan' => 'Daftar Kendaraan Bermotor',
                        'perawatan_bulan_ini' => 'Data Kendaraan Bermotor Yang Perlu Perawatan Bulan Ini',
                        'perawatan_kendaraan_bermotor' => 'Data Perawatan Kendaraan Bermotor',
                    ])
                    ->placeholder('Pilih Laporan')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->options([
                        2 => 'Roda 2',
                        4 => 'Roda 4',
                    ])
                    ->placeholder('Semua Roda')
                    ->native(false),

                Forms\Components\Select::make('jenis_perawatan_id')
                    ->label('Jenis Perawatan')
                    ->options(JenisPerawatan::pluck('nama', 'id'))
                    ->searchable()
                    ->native(false)
                    ->placeholder('Pilih Jenis Perawatan'),
            ]);
    }

    public function submit()
    {
        if (!$this->laporan) {
            Notification::make()
                ->title('Gagal')
                ->body('Silakan pilih jenis laporan terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }

        if ($this->laporan == 'daftar_kendaraan') {
            return Excel::download(
                new DaftarKendaraanExport($this->jumlah_roda),
                'daftar_kendaraan_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        if ($this->laporan == 'perawatan_bulan_ini') {
            return Excel::download(
                new PerawatanBulanIniExport($this->jumlah_roda, $this->jenis_perawatan_id),
                'perawatan_bulan_ini_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        if ($this->laporan == 'perawatan_kendaraan_bermotor') {
            return Excel::download(
                new PerawatanKendaraanBermotorExport($this->jumlah_roda, $this->jenis_perawatan_id),
                'perawatan_kendaraan_bermotor_' . now()->format('Ymd_His') . '.xlsx'
            );
        }
    }
}
