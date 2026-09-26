<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = [
        // Profil warung
        'nama_warung',
        'telepon',
        'alamat',
        'jam_buka',
        'jam_tutup',
        'deskripsi',
        'logo',

        // Pengaturan sistem
        'zona_waktu',
        'mata_uang',
        'pajak',
        'biaya_layanan',
        'terima_pesanan',
        'notif_pesanan',
    ];

    protected function casts(): array
    {
        return [
            'pajak'          => 'integer',
            'biaya_layanan'  => 'integer',
            'terima_pesanan' => 'boolean',
            'notif_pesanan'  => 'boolean',
        ];
    }

    // Ambil satu-satunya baris pengaturan.
    // Kalau belum ada, dibuat otomatis dengan nilai bawaan dari tabel.
    public static function ambil(): self
    {
        return static::first() ?? static::create([])->fresh();
    }

    // Nama warung untuk ditampilkan, dengan kata cadangan kalau belum diisi.
    // Dipakai di semua tempat yang menampilkan nama warung, supaya tidak ada
    // teks "Warung Enak" yang tertinggal statis di suatu halaman.
    public static function namaWarung(): string
    {
        return static::ambil()->nama_warung ?: 'Warung Enak';
    }
}