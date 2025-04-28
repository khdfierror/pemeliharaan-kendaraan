<?php

namespace App\Exports;

use App\Models\Kendaraan;
use App\Models\Perawatan;
use App\Models\DetailPerawatan;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;

class LaporanExport implements FromArray, WithHeadings, WithEvents, WithStyles
{
    protected $laporan;
    protected $jumlahRoda;
    protected $tahun;
    protected $jenisPerawatan;
    protected $data = [];

    public function __construct($laporan, $jumlahRoda, $jenisPerawatan, $tahun)
    {
        $this->laporan = $laporan;
        $this->jumlahRoda = $jumlahRoda;
        $this->jenisPerawatan = $jenisPerawatan;
        $this->tahun = $tahun;
    }

    public function array(): array
    {
        $this->data = [];

        match ($this->laporan) {
            'jumlah_kendaraan' => $this->generateJumlahKendaraan(),
            'jumlah_perawatan' => $this->generateJumlahPerawatan(),
            'perawatan_bulan_ini' => $this->generatePerawatanBulanIni(),
            'jumlah_pengeluaran' => $this->generateJumlahPengeluaran(),
        };

        return $this->data;
    }

    public function headings(): array
    {
        return [];
    }

    protected function generateJumlahKendaraan()
    {
        $this->data[] = ['Data Kendaraan Bermotor Yang Perlu Perawatan Bulan ' . now()->translatedFormat('F') . ' ' . $this->tahun];
        $this->data[] = ['Daftar Kendaraan Bermotor'];
        $this->data[] = ['Roda ' . $this->jumlahRoda];
        $this->data[] = ['No.', 'Nomor Plat', 'Tahun', 'Merk', 'Nama', 'Keterangan'];

        $kendaraan = Kendaraan::where('jumlah_roda', $this->jumlahRoda)->get();
        foreach ($kendaraan as $index => $item) {
            $this->data[] = [
                $index + 1,
                $item->nomor_plat,
                $item->tahun,
                $item->merk,
                $item->nama,
                $item->keterangan ?? '-',
            ];
        }
    }

    protected function generateJumlahPerawatan()
    {
        $this->data[] = ['Jumlah Perawatan Kendaraan'];
        $this->data[] = ['Roda ' . $this->jumlahRoda];
        $this->data[] = ['No.', 'Kendaraan', 'Jumlah Perawatan'];

        $kendaraan = Kendaraan::where('jumlah_roda', $this->jumlahRoda)->get();
        foreach ($kendaraan as $index => $item) {
            $jumlahPerawatan = $item->perawatan()->count();
            $this->data[] = [
                $index + 1,
                $item->nomor_plat,
                $jumlahPerawatan,
            ];
        }
    }

    protected function generatePerawatanBulanIni()
    {
        $this->data[] = ['Data Kendaraan Bermotor Yang Perlu Perawatan Bulan ' . now()->translatedFormat('F Y')];
        $this->data[] = ['Roda ' . $this->jumlahRoda];
        $this->data[] = ['No.', 'Kendaraan', 'Jenis Perawatan', 'Habis Masa Pakai'];

        $perawatan = Perawatan::whereHas('kendaraan', function ($q) {
            $q->where('jumlah_roda', $this->jumlahRoda);
        })
            ->whereYear('tanggal_nota', $this->tahun)
            ->whereMonth('tanggal_nota', now()->month) // Bulan tetap bulan sekarang
            ->get();
        foreach ($perawatan as $index => $item) {
            $this->data[] = [
                $index + 1,
                $item->kendaraan->nomor_plat ?? '-',
                $item->jenis_perawatan,
                $item->tanggal_nota?->format('d-m-Y'),
            ];
        }
    }

    protected function generateJumlahPengeluaran()
    {
        $this->data[] = ['Data Perawatan Kendaraan Bermotor'];
        $this->data[] = ['Roda ' . $this->jumlahRoda];
        $this->data[] = ['No.', 'Jenis Perawatan', 'Uraian', 'Biaya', 'Masa Pakai', 'KM Awal', 'KM Akhir'];

        $details = DetailPerawatan::whereHas('perawatan.kendaraan', function ($q) {
            $q->where('jumlah_roda', $this->jumlahRoda);
        })->get();

        $total = 0;

        foreach ($details as $index => $item) {
            $this->data[] = [
                $index + 1,
                $item->jenis_perawatan,
                $item->uraian,
                $item->biaya,
                $item->masa_pakai,
                $item->km_awal,
                $item->km_akhir,
            ];
            $total += $item->biaya;
        }

        if (count($details) > 0) {
            $this->data[] = ['', '', 'Total', $total, '', '', ''];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Bold untuk judul
                $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                $sheet->getStyle('A2:F2')->getFont()->setBold(true);

                // Merge judul
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');

                // Bold header tabel
                $sheet->getStyle('A3:F3')->getFont()->setBold(true);

                // Bold baris total
                $highestRow = $sheet->getHighestRow();
                foreach (range('A', 'Z') as $col) {
                    if (strtolower(trim($sheet->getCell($col . $highestRow)->getValue())) == 'total') {
                        $sheet->getStyle('A' . $highestRow . ':F' . $highestRow)->getFont()->setBold(true);
                    }
                }
            }
        ];
    }
}
