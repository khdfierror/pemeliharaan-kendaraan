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
        Schema::create('perawatan', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedSmallInteger('tahun')->nullable()->index();
            $table->foreignUlid('kendaraan_id')->nullable()->index();
            $table->string('nomor_nota')->nullable();
            $table->date('tanggal_nota')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perawatan');
    }
};
