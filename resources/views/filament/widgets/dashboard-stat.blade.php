<x-filament::widget>
    <x-filament::card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="border p-4 rounded">
                <h2 class="text-lg font-bold mb-2">Data Kendaraan</h2>
                <p>Roda 2: {{ $jumlah_kendaraan_roda_2 }} Unit</p>
                <p>Roda 4: {{ $jumlah_kendaraan_roda_4 }} Unit</p>
            </div>

            <div class="border p-4 rounded">
                <h2 class="text-lg font-bold mb-2">Data Perawatan Kendaraan</h2>
                <p>Roda 2: {{ $jumlah_perawatan_roda_2 }} Unit</p>
                <p>Roda 4: {{ $jumlah_perawatan_roda_4 }} Unit</p>
            </div>

            <div class="border p-4 rounded">
                <h2 class="text-lg font-bold mb-2">Perawatan Bulan Ini</h2>
                <p>Roda 2: {{ $perlu_perawatan_roda_2 }} Unit</p>
                <p>Roda 4: {{ $perlu_perawatan_roda_4 }} Unit</p>
            </div>

            <div class="border p-4 rounded">
                <h2 class="text-lg font-bold mb-2">Total Biaya Perawatan</h2>
                <p>Roda 2: Rp {{ number_format($pengeluaran_roda_2, 0, ',', '.') }}</p>
                <p>Roda 4: Rp {{ number_format($pengeluaran_roda_4, 0, ',', '.') }}</p>
                <p class="font-bold">Total: Rp {{ number_format($pengeluaran_roda_2 + $pengeluaran_roda_4, 0, ',', '.') }}</p>
            </div>

        </div>
    </x-filament::card>
</x-filament::widget>
