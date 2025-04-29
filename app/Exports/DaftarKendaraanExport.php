<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DaftarKendaraanExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $jumlahRoda;
    protected array $data = [];
    protected array $mergeTitleRows = [];
    protected array $tableRanges = [];

    public function __construct($jumlahRoda = null)
    {
        $this->jumlahRoda = $jumlahRoda;
    }

    public function array(): array
    {
        $this->data = [];

        if ($this->jumlahRoda) {
            $this->generateTable($this->jumlahRoda);
        } else {
            $this->generateTable(2);

            // Baris kosong antar tabel
            $this->data[] = ['', '', '', '', '', ''];

            $this->generateTable(4);
        }

        return $this->data;
    }

    protected function generateTable($jumlahRoda)
    {
        $kendaraans = Kendaraan::where('jumlah_roda', $jumlahRoda)->get();

        // Tambahkan teks "Roda X"
        $this->data[] = ["Roda {$jumlahRoda}", '', '', '', '', ''];
        $this->mergeTitleRows[] = count($this->data); // Baris yang akan di-merge

        // Tambahkan header tabel
        $this->data[] = ['No.', 'Nomor Plat', 'Tahun', 'Merk', 'Nama', 'Keterangan'];
        $headerRow = count($this->data); // Posisi header tabel

        if ($kendaraans->isNotEmpty()) {
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
        }

        $lastDataRow = count($this->data); // Baris terakhir tabel

        $this->tableRanges[] = [$headerRow, $lastDataRow];
    }

    public function headings(): array
    {
        return [
            ['Daftar Kendaraan Bermotor'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
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

                // Merge judul utama
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Merge tulisan "Roda 2" dan "Roda 4"
                foreach ($this->mergeTitleRows as $row) {
                    $sheet->mergeCells("A{$row}:F{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    ]);
                }

                // Auto-size kolom
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Border untuk tabel Roda 2 dan Roda 4
                foreach ($this->tableRanges as [$start, $end]) {
                    $sheet->getStyle("A{$start}:F{$end}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center',
                        ],
                    ]);

                    // Bold header tabel
                    $sheet->getStyle("A{$start}:F{$start}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                    ]);
                }
            }
        ];
    }
}
