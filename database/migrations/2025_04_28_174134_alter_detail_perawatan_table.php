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
        Schema::table('detail_perawatan', function (Blueprint $table) {
            $table->date('habis_masa_pakai')->nullable()->after('masa_pakai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_perawatan', function (Blueprint $table) {
            $table->dropColumn('habis_masa_pakai');
        });
    }
};
