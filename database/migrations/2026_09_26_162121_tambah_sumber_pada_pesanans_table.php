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
        Schema::table('pesanans', function (Blueprint $table) {
            // 'kasir' atau 'online'. Dipakai untuk membedakan pesanan yang ditahan
            // kasir dari pesanan pelanggan lewat landing page, di halaman
            // "Pesanan Tertahan". Bawaannya 'online' karena itu jalur yang paling lama
            // ada; pesanan lama (sebelum kolom ini ditambah) otomatis dianggap begitu.
            $table->string('sumber', 20)->default('online')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('sumber');
        });
    }
};