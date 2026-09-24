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
            // Role: 'admin' atau 'kasir'. Bawaannya 'kasir' (hak akses paling kecil),
            // jadi akun yang lupa diatur tidak otomatis jadi admin.
            $table->string('role', 20)->default('kasir')->after('password');

            // Akun yang dinonaktifkan tidak bisa login, tapi datanya tetap tersimpan.
            $table->boolean('aktif')->default(true)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'aktif']);
        });
    }
};
