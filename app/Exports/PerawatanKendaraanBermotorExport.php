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

class PerawatanKendaraanBermotorExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithDefaultStyles, WithColumnWidths
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
        $kendaraans = Kendaraan::query()
            ->where('jumlah_roda', $jumlahRoda)
            ->with(['perawatan.detailPerawatan']) // <- ini fix relasinya
            ->get();

        $this->data[] = ["Roda {$jumlahRoda}", '', '', '', '', '', ''];

        foreach ($kendaraans as $kendaraan) {
            foreach ($kendaraan->perawatan as $perawatan) {
                $this->data[] = ["{$kendaraan->nama} ({$kendaraan->nomor_plat})", '', '', '', '', '', ''];
                $this->data[] = [
                    "No Nota: {$perawatan->nomor_nota}\nTgl Nota: " . ($perawatan->tanggal_nota ? $perawatan->tanggal_nota->format('d F Y') : '-'),
                    '', '', '', '', '', ''
                ];
                
                $this->data[] = ['No.', 'Jenis Perawatan', 'Uraian', 'Biaya', 'Masa Pakai', 'KM Awal', 'KM Akhir'];

                $totalBiaya = 0;

                if (!empty($perawatan->detailPerawatan)) {
                    foreach ($perawatan->detailPerawatan as $index => $detail) {
                        $this->data[] = [
                            $index + 1,
                            $detail->jenisPerawatan->nama ?? '-',
                            $detail->uraian ?? '-',
                            number_format($detail->total ?? 0, 0, ',', '.'),
                            $detail->masa_pakai ?? '-',
                            $detail->km_awal ?? 0,
                            $detail->km_akhir ?? 0,
                        ];
                        $totalBiaya += $detail->total ?? 0;
                    }
                }

                $this->data[] = ['', '', 'Total', number_format($totalBiaya, 0, ',', '.'), '', '', ''];
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
                    'font' => ['bold' => true, 'size' => 20],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->freezePane('A3');
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A6:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                for ($row = 3; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell("A{$row}")->getValue();
                    if (str_contains($cellValue, 'Roda 2') || str_contains($cellValue, 'Roda 4')) {
                        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 14],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFE5E7EB'],
                            ],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        ]);
                    }
                }
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
            'A' => 25,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
        ];
    }
}
