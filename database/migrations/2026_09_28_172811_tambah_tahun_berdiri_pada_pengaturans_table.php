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
        Schema::table('pengaturans', function (Blueprint $table) {
            // Tahun warung mulai berdiri (contoh: 2023). Boleh kosong.
            // Dipakai landing page untuk menghitung "Tahun Beroperasi".
            $table->unsignedSmallInteger('tahun_berdiri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->dropColumn('tahun_berdiri');
        });
    }
};