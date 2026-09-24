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
        Schema::create('aktivitas', function (Blueprint $table) {
            $table->id();

            // Satu baris teks siap tampil, contoh: "Pesanan PSN-0042 dibuat oleh Budi Santoso"
            // atau "Pesanan PSN-0038 diubah menjadi Selesai". Sengaja teks polos (bukan relasi
            // ke pesanan atau tabel lain), supaya catatan tetap ada walau pesanannya nanti dihapus.
            $table->text('deskripsi');

            $table->timestamps();

            // Mempercepat "aktivitas terbaru dulu"
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas');
    }
};