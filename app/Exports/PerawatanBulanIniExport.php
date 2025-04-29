<?php

namespace App\Exports;

use App\Models\DetailPerawatan;
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

class PerawatanBulanIniExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithDefaultStyles, WithColumnWidths
{
    private ?int $jumlahRoda;
    private ?int $jenisPerawatanId;
    private array $data = [];
    private array $sectionTitleRows = [];
    private int $nomor = 1;
    private string $lastColumn = 'D';
    private string $title;

    public function __construct($jumlahRoda = null, $jenisPerawatanId = null)
    {
        $this->jumlahRoda = $jumlahRoda;
        $this->jenisPerawatanId = $jenisPerawatanId;
        $this->title = 'Data Kendaraan Bermotor Yang Perlu Perawatan Bulan ' . now()->translatedFormat('F');
    }

    public function array(): array
    {
        $this->data = [];

        if ($this->jumlahRoda) {
            $this->generateSection($this->jumlahRoda);
        } else {
            $this->generateSection(2);
            $this->addEmptyRow();
            $this->generateSection(4);
        }

        return $this->data;
    }

    protected function generateSection(int $jumlahRoda)
    {
        $this->nomor = 1;

        $query = DetailPerawatan::whereHas('perawatan.kendaraan', function ($q) use ($jumlahRoda) {
            $q->where('jumlah_roda', $jumlahRoda);
        });

        if ($this->jenisPerawatanId) {
            $query->where('jenis_perawatan_id', $this->jenisPerawatanId);
        }

        $query->whereMonth('habis_masa_pakai', now()->month)
            ->whereYear('habis_masa_pakai', now()->year);

        $details = $query->with('perawatan.kendaraan.merk', 'jenisPerawatan')->get();

        $this->data[] = ["Kendaraan Roda {$jumlahRoda}", '', '', ''];
        $this->sectionTitleRows[] = count($this->data);

        $this->data[] = ['No.', 'Kendaraan', 'Jenis Perawatan', 'Habis Masa Pakai'];

        if ($details->isNotEmpty()) {
            // Grouping berdasarkan kendaraan_id
            $grouped = $details->groupBy(function ($item) {
                return $item->perawatan->kendaraan_id;
            });

            foreach ($grouped as $kendaraanId => $detailsGroup) {
                $firstDetail = $detailsGroup->first();
                $kendaraan = $firstDetail->perawatan->kendaraan;
                $merkNamaPlat = ($kendaraan->merk->nama ?? '-') . ' ' . ($kendaraan->nama ?? '-') . ' (' . ($kendaraan->nomor_plat ?? '-') . ')';

                $first = true;

                foreach ($detailsGroup as $detail) {
                    $this->data[] = [
                        $first ? $this->nomor++ : '',
                        $first ? $merkNamaPlat : '',
                        $detail->jenisPerawatan->nama ?? '-',
                        $detail->habis_masa_pakai?->format('d-m-Y') ?? '-',
                    ];
                    $first = false;
                }
            }
        }
    }

    protected function addEmptyRow()
    {
        $this->data[] = ['', '', '', ''];
    }

    public function headings(): array
    {
        return [
            [$this->title],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastColumn = $this->lastColumn;

                $sheet->insertNewRowBefore(1, 3);
                $sheet->setCellValue('A1', $this->title);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->setCellValue('A3', "Tanggal Diunduh: " . now()->translatedFormat('d F Y, H:i'));
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getStyle('A3')->getFont()->getColor()->setARGB('FF888888');

                $sheet->freezePane('A4');

                // Merge untuk section title
                foreach ($this->sectionTitleRows as $row) {
                    $row += 3;
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF3F4F6']],
                    ]);
                }

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // 🔥 Merge Kendaraan & Nomor di kolom A & B berdasarkan blok yang sama
                $startRow = 5;
                while ($startRow <= $highestRow) {
                    $kendaraan = $sheet->getCell("B{$startRow}")->getValue();
                    $rowSpan = 1;

                    while (
                        $startRow + $rowSpan <= $highestRow &&
                        $sheet->getCell("B" . ($startRow + $rowSpan))->getValue() === ''
                    ) {
                        $rowSpan++;
                    }

                    if ($rowSpan > 1) {
                        $sheet->mergeCells("A{$startRow}:A" . ($startRow + $rowSpan - 1));
                        $sheet->mergeCells("B{$startRow}:B" . ($startRow + $rowSpan - 1));
                        $sheet->getStyle("A{$startRow}:B" . ($startRow + $rowSpan - 1))->applyFromArray([
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_TOP,
                            ],
                        ]);
                    }

                    $startRow += $rowSpan;
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
                'indent' => 1,
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
            'B' => 40,
            'C' => 25,
            'D' => 20,
        ];
    }
}
