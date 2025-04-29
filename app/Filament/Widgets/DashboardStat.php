<?php

namespace App\Filament\Widgets;

use App\Models\DetailPerawatan;
use App\Models\Kendaraan;
use App\Models\Perawatan;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class DashboardStat extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-stat';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            // Jumlah Kendaraan Bermotor
            'jumlah_kendaraan_roda_2' => Kendaraan::where('jumlah_roda', 2)->count(),
            'jumlah_kendaraan_roda_4' => Kendaraan::where('jumlah_roda', 4)->count(),

            // Jumlah Perawatan Kendaraan (di-group berdasarkan kendaraan, bukan berdasarkan perawatan)
            'jumlah_perawatan_roda_2' => Perawatan::whereHas('kendaraan', function ($q) {
                $q->where('jumlah_roda', 2);
            })
                ->select('kendaraan_id')
                ->distinct()
                ->count('kendaraan_id'),

            'jumlah_perawatan_roda_4' => Perawatan::whereHas('kendaraan', function ($q) {
                $q->where('jumlah_roda', 4);
            })
                ->select('kendaraan_id')
                ->distinct()
                ->count('kendaraan_id'),

            // Kendaraan Perlu Perawatan Bulan Ini
            // Perlu Perawatan Kendaraan (grouping berdasarkan kendaraan)
            'perlu_perawatan_roda_2' => DetailPerawatan::whereMonth('habis_masa_pakai', now()->month)
                ->whereYear('habis_masa_pakai', now()->year)
                ->whereHas('perawatan.kendaraan', function ($q) {
                    $q->where('jumlah_roda', 2);
                })
                ->select('perawatan_id') // ambil perawatan_id
                ->distinct()
                ->count('perawatan_id'),

            'perlu_perawatan_roda_4' => DetailPerawatan::whereMonth('habis_masa_pakai', now()->month)
                ->whereYear('habis_masa_pakai', now()->year)
                ->whereHas('perawatan.kendaraan', function ($q) {
                    $q->where('jumlah_roda', 4);
                })
                ->select('perawatan_id') // ambil perawatan_id
                ->distinct()
                ->count('perawatan_id'),

            // Jumlah Pengeluaran
            'pengeluaran_roda_2' => DetailPerawatan::whereHas('perawatan.kendaraan', function ($q) {
                $q->where('jumlah_roda', 2);
            })->sum('total'),

            'pengeluaran_roda_4' => DetailPerawatan::whereHas('perawatan.kendaraan', function ($q) {
                $q->where('jumlah_roda', 4);
            })->sum('total'),

        ];
    }
}
