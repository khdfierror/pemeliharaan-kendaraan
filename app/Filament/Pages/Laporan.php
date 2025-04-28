<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Livewire\Component;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms;
use App\Models\JenisPerawatan;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.laporan';

    public $data = [];


    protected function getViewData(): array
    {
        return [
            'form' => $this->form,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('laporan')
                    ->options([
                        'jumlah_kendaraan'    => 'Jumlah Kendaraan',
                        'jumlah_perawatan'    => 'Jumlah Perawatan Kendaraan',
                        'perawatan_bulan_ini' => 'Perawatan Bulan Ini',
                        'jumlah_pengeluaran'  => 'Jumlah Pengeluaran',
                    ])
                    ->placeholder('Pilih Laporan')
                    ->label('Laporan')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->options([
                        2 => 'Roda 2',
                        4 => 'Roda 4',
                    ])
                    ->placeholder('Pilih Jumlah Roda')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('jenis_perawatan')
                    ->label('Jenis Perawatan')
                    ->options(JenisPerawatan::pluck('nama', 'id'))
                    ->searchable()
                    ->placeholder('Pilih Jenis Perawatan'),
            ])
            ->statePath('data'); // <<- tambahkan ini untuk BIND ke $data
    }

    public function submit()
    {
        $laporan = $this->data['laporan'] ?? null;
        $jumlahRoda = $this->data['jumlah_roda'] ?? null;
        $jenisPerawatan = $this->data['jenis_perawatan'] ?? null;
        $tahun = session('tahun') ?? now()->year;

        // if (!$laporan || !$jumlahRoda) {
        //     Notification::make()
        //         ->title('Gagal')
        //         ->body('Laporan dan Jumlah Roda wajib diisi.')
        //         ->danger()
        //         ->send();
        //     return;
        // }

        $export = new LaporanExport($laporan, $jumlahRoda, $jenisPerawatan, $tahun);

        return Excel::download($export, 'laporan_' . now()->format('Ymd_His') . '.xlsx');
    }
}
