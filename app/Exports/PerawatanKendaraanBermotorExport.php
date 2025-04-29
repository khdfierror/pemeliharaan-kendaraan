<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerawatanKendaraanExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithDefaultStyles, WithColumnWidths
{
    private array $data = [];
    private string $title = 'Data Perawatan Kendaraan Bermotor';
    private string $lastColumn = 'G';

    public function array(): array
    {
        $this->data = [];

        $this->generateSection(2);
        $this->addEmptyRow();
        $this->generateSection(4);

        return $this->data;
    }

    protected function generateSection(int $jumlahRoda)
    {
        $kendaraans = Kendaraan::where('jumlah_roda', $jumlahRoda)->with('perawatan')->get();

        $this->data[] = ["Roda {$jumlahRoda}", '', '', '', '', '', ''];

        foreach ($kendaraans as $kendaraan) {
            foreach ($kendaraan->perawatan as $perawatan) {
                $this->data[] = [
                    "{$kendaraan->nama} ({$kendaraan->nomor_plat})", '', '', '', '', '', '',
                ];
                $this->data[] = [
                    "No Nota: {$perawatan->no_nota}", '', '', "Tgl Nota: " . ($perawatan->tanggal ? $perawatan->tanggal->format('d F Y') : '-'), '', '', '',
                ];

                $this->data[] = ['No.', 'Jenis Perawatan', 'Uralan', 'Biaya', 'Masa Pakai', 'KM Awal', 'KM Akhir'];

                $totalBiaya = 0;
                foreach ($perawatan->detail as $index => $detail) {
                    $this->data[] = [
                        $index + 1,
                        $detail->jenis_perawatan,
                        $detail->uralan ?? '-',
                        $detail->biaya ?? 0,
                        $detail->masa_pakai ?? '-',
                        $detail->km_awal ?? 0,
                        $detail->km_akhir ?? 0,
                    ];
                    $totalBiaya += $detail->biaya ?? 0;
                }

                $this->data[] = [
                    '', '', 'Total', $totalBiaya, '', '', '',
                ];

                $this->addEmptyRow();
            }
        }
    }

    protected function addEmptyRow()
    {
        $this->data[] = ['', '', '', '', '', '', ''];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastColumn = $this->lastColumn;

                $sheet->insertNewRowBefore(1, 2);

                $sheet->setCellValue('A1', $this->title);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->freezePane('A3');

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
        ];
    }
}
