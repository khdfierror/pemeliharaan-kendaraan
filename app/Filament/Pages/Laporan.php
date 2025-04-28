<?php

namespace App\Filament\Pages;

use App\Exports\LaporanExport;
use App\Models\JenisPerawatan;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use PhpParser\Node\Stmt\Echo_;
use Vtiful\Kernel\Excel;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.laporan';

    protected static ?int $navigationSort = 5;

    public $laporan;
    public $jumlah_roda;
    public $jenis_perawatan;
    public array $data = [];

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\Fieldset::make()
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
                                ->columnSpanFull()
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
                        ->columns(1)
                ])
                // ->statePath('data'),
        ];
    }

    public function submit(): void
    {
        $laporan = $this->laporan;
        $jumlahRoda = $this->jumlah_roda;
        $jenisPerawatan = $this->jenis_perawatan;
        $tahun = session('tahun') ?? now()->year;

        if (!$laporan || !$jumlahRoda) {
            Notification::make()
                ->title('Gagal')
                ->body('Laporan dan Jumlah Roda wajib diisi.')
                ->danger()
                ->send();
            return;
        }

        $fileName = 'laporan_' . now()->format('Ymd_His') . '.xlsx';
        $export = new LaporanExport($laporan, $jumlahRoda, $jenisPerawatan, $tahun);
        $fileContent = FacadesExcel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

    }

    public function downloadFile()
{
    $fileContent = '...'; 
    $fileName = 'data.xlsx'; 

    return response()->stream(function () use ($fileContent) {
        echo $fileContent;
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
}

    
}
