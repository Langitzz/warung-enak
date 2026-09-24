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
        Schema::create('pesanan_items', function (Blueprint $table) {
            $table->id();

            // Kalau pesanannya dihapus, semua isinya ikut terhapus.
            $table->foreignId('pesanan_id')
                ->constrained('pesanans')
                ->cascadeOnDelete();

            // Menu asal. Kalau menunya dihapus nanti, isi pesanan lama tetap ada
            // (menu_id jadi kosong), karena nama dan harganya sudah disalin di bawah.
            $table->foreignId('menu_id')
                ->nullable()
                ->constrained('menus')
                ->nullOnDelete();

            // SALINAN nama dan harga menu saat pesanan dibuat.
            // Jadi riwayat tidak berubah walau menu diedit atau dihapus.
            $table->string('nama_menu', 100);
            $table->unsignedInteger('harga');

            $table->unsignedSmallInteger('jumlah');
            $table->unsignedInteger('subtotal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_items');
    }
};
