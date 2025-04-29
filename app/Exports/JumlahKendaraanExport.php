<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JumlahKendaraanExport implements FromArray, WithHeadings, WithEvents, WithStyles
{
    protected $jumlahRoda;

    protected array $data = [];

    public function __construct($jumlahRoda)
    {
        $this->jumlahRoda = $jumlahRoda;
    }

    public function array(): array
    {
        $kendaraans = Kendaraan::where('jumlah_roda', $this->jumlahRoda)->get();

        $this->data = [];

        foreach ($kendaraans as $index => $kendaraan) {
            $this->data[] = [
                $index + 1,
                $kendaraan->nomor_plat,
                $kendaraan->tahun,
                $kendaraan->merk->nama ?? '-',
                $kendaraan->nama,
                $kendaraan->keterangan ?? '-',
            ];
        }

        return $this->data;
    }

    public function headings(): array
    {
        return [
            ['Daftar Kendaraan Bermotor'],
            ['No.', 'Nomor Plat', 'Tahun', 'Merk', 'Nama', 'Keterangan'],
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

                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $lastRow = count($this->data) + 2;
                $sheet->getStyle("A2:F{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Auto width semua kolom
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
