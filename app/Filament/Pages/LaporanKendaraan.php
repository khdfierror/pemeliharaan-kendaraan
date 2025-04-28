<?php

namespace App\Filament\Pages;

use App\Exports\JumlahKendaraanExport;
use App\Models\Kendaraan;
use Filament\Pages\Page;
use Filament\Forms;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class LaporanKendaraan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Laporan Jumlah Kendaraan';
    protected static ?string $slug = 'laporan-kendaraan';
    protected static string $view = 'filament.pages.laporan-kendaraan';

    public $jumlah_roda;

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
                    ->native(false)
                    ->statePath('jumlah_roda'), // <<-- ini ubahannya!
            ]);
    }

    public $data = [];

    public function submit()
    {
        $jumlahRoda = $this->jumlah_roda ?? null;

        if (!$jumlahRoda) {
            Notification::make()
                ->title('Gagal')
                ->body('Silakan pilih jumlah roda terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }

        return Excel::download(new JumlahKendaraanExport($jumlahRoda), 'jumlah_kendaraan.xlsx');
    }
}
