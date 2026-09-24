<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom kategori_id (boleh kosong dulu, karena menu lama belum punya isinya)
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('kategori_id')
                ->nullable()
                ->after('nama')
                ->constrained('kategoris')
                ->restrictOnDelete();
        });

        // 2. Pindahkan kategori teks lama ke tabel kategoris, lalu hubungkan menunya
        $namaKategori = DB::table('menus')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori');

        foreach ($namaKategori as $nama) {
            $id = DB::table('kategoris')->where('nama', $nama)->value('id');

            if (! $id) {
                $id = DB::table('kategoris')->insertGetId([
                    'nama' => $nama,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('menus')->where('kategori', $nama)->update(['kategori_id' => $id]);
        }

        // 3. Buang kolom teks lama, karena sudah diganti kategori_id
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom teks lama dan isi dari nama kategori
        Schema::table('menus', function (Blueprint $table) {
            $table->string('kategori')->default('')->after('nama');
        });

        DB::table('menus')
            ->join('kategoris', 'kategoris.id', '=', 'menus.kategori_id')
            ->update(['menus.kategori' => DB::raw('kategoris.nama')]);

        Schema::table('menus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
        });
    }
};
