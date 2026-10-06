<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // 'tunai', 'debit', atau 'qris'. Kosong untuk pesanan online (belum dibayar lewat kasir)
            // dan pesanan lama sebelum kolom ini ada.
            $table->string('metode_bayar', 20)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('metode_bayar');
        });
    }
};