<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $fillable = [
        'nama',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    // Satu kategori punya banyak menu.
    // Relasi ini baru bisa dipakai setelah tabel menus punya kolom kategori_id (langkah berikutnya).
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
