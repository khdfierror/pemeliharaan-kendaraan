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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('merk_id')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable()->index();
            $table->string('nomor_plat')->nullable()->index();
            $table->string('jumlah_roda')->nullable();
            $table->string('tahun_produksi')->nullable();
            $table->string('nama')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
