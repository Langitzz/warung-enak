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
}