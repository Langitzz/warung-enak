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
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();

            // ----- Profil warung -----
            $table->string('nama_warung', 100)->default('Warung Enak');
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('logo')->nullable();

            // ----- Pengaturan sistem -----
            $table->string('zona_waktu', 30)->default('Asia/Jakarta');
            $table->string('mata_uang', 30)->default('Rupiah (Rp)');
            $table->unsignedTinyInteger('pajak')->default(0);
            $table->unsignedTinyInteger('biaya_layanan')->default(0);
            $table->boolean('terima_pesanan')->default(true);
            $table->boolean('notif_pesanan')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
