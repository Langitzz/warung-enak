<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    protected $fillable = [
        'deskripsi',
    ];

    // Jalan pintas untuk mencatat satu aktivitas, dipanggil dari model event Pesanan.
    public static function catat(string $deskripsi): void
    {
        static::create(['deskripsi' => $deskripsi]);
    }
}