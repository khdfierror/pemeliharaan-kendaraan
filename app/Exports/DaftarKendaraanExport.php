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

class DaftarKendaraanExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithDefaultStyles, WithColumnWidths
{
    private int $nomor = 1;
    private string $lastColumn = 'F';
    private string $title = 'Daftar Kendaraan Bermotor';
    private array $data = [];
    private array $sectionTitleRows = [];

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
        $kendaraans = Kendaraan::where('jumlah_roda', $jumlahRoda)->get();

        $this->data[] = ["Kendaraan Roda {$jumlahRoda}", '', '', '', '', ''];
        $this->sectionTitleRows[] = count($this->data); 

        $this->data[] = ['No.', 'Nomor Plat', 'Tahun', 'Merk', 'Nama', 'Keterangan'];

        if ($kendaraans->isNotEmpty()) {
            foreach ($kendaraans as $kendaraan) {
                $this->data[] = [
                    $this->nomor++,
                    $kendaraan->nomor_plat,
                    $kendaraan->tahun,
                    $kendaraan->merk->nama ?? '-',
                    $kendaraan->nama,
                    $kendaraan->keterangan ?? '-',
                ];
            }
        }
    }

    protected function addEmptyRow()
    {
        $this->data[] = ['', '', '', '', '', ''];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastColumn = $this->lastColumn;
                $sheet = $event->sheet;

                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', $this->title);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 22],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $tanggal = now()->translatedFormat('d F Y, H:i');
                $sheet->setCellValue('A3', "Tanggal Diunduh: {$tanggal}");
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getStyle('A3')->getFont()->getColor()->setARGB('FF888888');

                $sheet->freezePane('A4');

                foreach ($this->sectionTitleRows as $row) {
                    $row += 3; 
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF3F4F6'], // Warna abu-abu muda
                        ],
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
            'B' => 20,
            'C' => 12,
            'D' => 20,
            'E' => 25,
            'F' => 30,
        ];
    }
}
