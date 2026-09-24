<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ganti default kolom role dari 'kasir' jadi 'pelanggan'. Pakai raw SQL
        // (bukan ->change()) supaya tidak perlu tambah dependency doctrine/dbal.
        // Akun yang sudah ada tidak berubah, ini cuma mengganti nilai bawaan
        // untuk baris baru yang tidak menyebutkan role secara eksplisit.
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NOT NULL DEFAULT 'pelanggan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NOT NULL DEFAULT 'kasir'");
    }
};