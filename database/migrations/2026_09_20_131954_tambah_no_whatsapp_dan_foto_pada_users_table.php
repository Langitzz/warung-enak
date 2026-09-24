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
        Schema::table('users', function (Blueprint $table) {
            // Nomor WhatsApp: boleh kosong, karena tidak diminta saat daftar.
            // Pelanggan mengisinya di profil atau saat pertama kali memesan.
            $table->string('no_whatsapp', 20)->nullable()->after('email');

            // Foto profil: boleh kosong (yang disimpan hanya nama file di storage).
            $table->string('foto')->nullable()->after('no_whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_whatsapp', 'foto']);
        });
    }
};
