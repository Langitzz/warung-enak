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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();

            // Pemesan yang punya akun. Kosong (null) kalau pemesannya tamu tanpa akun.
            // Kalau akunnya dihapus, pesanannya tetap ada (user_id jadi kosong).
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Data pemesan disimpan di tiap pesanan (tamu maupun yang login),
            // supaya tampilan pesanan di admin dan kasir selalu sama.
            $table->string('nama_pelanggan', 100);
            $table->string('no_whatsapp', 20);
            $table->text('catatan')->nullable();

            // menunggu, diproses, selesai, atau dibatalkan
            $table->string('status', 20)->default('menunggu');

            // Semua angka dalam rupiah bulat (tanpa desimal).
            // Pajak dan biaya layanan disimpan sebagai nominal saat pesanan dibuat,
            // jadi tidak berubah walau persentase di Pengaturan Sistem diubah nanti.
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('pajak')->default(0);
            $table->unsignedInteger('biaya_layanan')->default(0);
            $table->unsignedInteger('total')->default(0);

            $table->timestamps();

            // Mempercepat pencarian per status dan laporan per tanggal
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
