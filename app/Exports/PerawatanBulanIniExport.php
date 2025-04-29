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
        $this->nomor = 1; // ✅ Reset nomor setiap section baru

        $query = DetailPerawatan::whereHas('perawatan.kendaraan', function ($q) use ($jumlahRoda) {
            $q->where('jumlah_roda', $jumlahRoda);
        });

        if ($this->jenisPerawatanId) {
            $query->where('jenis_perawatan_id', $this->jenisPerawatanId);
        }

        $query->whereMonth('habis_masa_pakai', now()->month)
            ->whereYear('habis_masa_pakai', now()->year);

        $details = $query->with('perawatan.kendaraan', 'jenisPerawatan')->get();

        $this->data[] = ["Kendaraan Roda {$jumlahRoda}", '', '', ''];
        $this->sectionTitleRows[] = count($this->data);

        $this->data[] = ['No.', 'Nama Kendaraan', 'Jenis Perawatan', 'Habis Masa Pakai'];

        if ($details->isNotEmpty()) {
            foreach ($details as $detail) {
                $this->data[] = [
                    $this->nomor++,
                    $detail->perawatan->kendaraan->nama ?? '-',
                    $detail->jenisPerawatan->nama ?? '-',
                    $detail->habis_masa_pakai?->format('d-m-Y') ?? '-',
                ];
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

                // Insert 3 baris kosong di atas
                $sheet->insertNewRowBefore(1, 3);

                // Set judul besar
                $sheet->setCellValue('A1', $this->title);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16], // ✅ Kecilkan size dari 22 ke 16
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Set tanggal diunduh
                $tanggal = now()->translatedFormat('d F Y, H:i');
                $sheet->setCellValue('A3', "Tanggal Diunduh: {$tanggal}");
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getStyle('A3')->getFont()->getColor()->setARGB('FF888888');

                // Freeze pane di header
                $sheet->freezePane('A4');

                // Format Section Title (Kendaraan Roda 2 / 4)
                foreach ($this->sectionTitleRows as $row) {
                    $row += 3; // Tambah 3 karena insertNewRowBefore(1,3)
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF3F4F6'],
                        ],
                    ]);
                }

                // Apply border ke seluruh data
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
            'B' => 30,
            'C' => 25,
            'D' => 20,
        ];
    }
}
