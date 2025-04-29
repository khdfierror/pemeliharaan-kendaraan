<?php

namespace App\Exports;

use App\Models\DetailPerawatan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerawatanBulanIniExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $jumlahRoda;
    protected $jenisPerawatanId;
    protected $data = [];

    public function __construct($jumlahRoda, $jenisPerawatanId)
    {
        $this->jumlahRoda = $jumlahRoda;
        $this->jenisPerawatanId = $jenisPerawatanId;
    }

    public function array(): array
    {
        $detailPerawatans = DetailPerawatan::where('jenis_perawatan_id', $this->jenisPerawatanId)
            ->whereHas('perawatan.kendaraan', function ($q) {
                $q->where('jumlah_roda', $this->jumlahRoda);
            })
            ->whereMonth('habis_masa_pakai', now()->month)
            ->whereYear('habis_masa_pakai', now()->year)
            ->with(['perawatan.kendaraan', 'jenisPerawatan'])
            ->get();

        $this->data = [];

        foreach ($detailPerawatans as $index => $item) {
            $this->data[] = [
                $index + 1,
                $item->perawatan->kendaraan->nama ?? '-',
                $item->jenisPerawatan->nama ?? '-',
                $item->habis_masa_pakai?->format('d-m-Y') ?? '-',
            ];
        }

        return $this->data;
    }

    public function headings(): array
    {
        return [
            ['Data Kendaraan Bermotor Yang Perlu Perawatan Bulan Ini'],
            ['No.', 'Nama Kendaraan', 'Jenis Perawatan', 'Habis Masa Pakai'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge judul
                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Border
                $lastRow = count($this->data) + 2;
                $sheet->getStyle("A2:D{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Auto size kolom
                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
