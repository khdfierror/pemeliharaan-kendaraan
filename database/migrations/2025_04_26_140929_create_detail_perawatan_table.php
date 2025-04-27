<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_perawatan', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedSmallInteger('tahun')->nullable()->index();
            $table->foreignUlid('perawatan_id')->nullable()->index();
            $table->foreignUlid('jenis_perawatan_id')->nullable()->index();
            $table->text('uraian')->nullable();
            $table->string('volume')->nullable();
            $table->string('harga_satuan')->nullable();
            $table->string('total')->nullable();
            $table->string('masa_pakai')->nullable();
            $table->string('km_awal')->nullable();
            $table->string('km_akhir')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_perawatan');
    }
};
