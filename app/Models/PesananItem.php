<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananItem extends Model
{
    // Baris isi pesanan dibuat dari kode (lihat Pesanan::buat()), bukan langsung dari form.
    protected $fillable = [
        'pesanan_id',
        'menu_id',
        'nama_menu',
        'harga',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga'    => 'integer',
            'jumlah'   => 'integer',
            'subtotal' => 'integer',
        ];
    }

    // Pesanan pemilik baris ini
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    // Menu asal (bisa kosong kalau menunya sudah dihapus; nama dan harganya tetap tersimpan di baris ini)
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}