<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'nama',
        'kategori_id',
        'deskripsi',
        'harga',
        'foto',
        'tersedia',
    ];

    protected function casts(): array
    {
        return [
            'tersedia' => 'boolean',
        ];
    }

    // Satu menu milik satu kategori
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    // Satu menu bisa muncul di banyak baris pesanan
    public function pesananItems(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }
}
