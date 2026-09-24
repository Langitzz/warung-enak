<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Pesanan extends Model
{
    // Status yang dipakai (kunci = nilai di database, isi = tulisan di tampilan)
    public const STATUS = [
        'menunggu'   => 'Menunggu',
        'diproses'   => 'Diproses',
        'siap'       => 'Siap',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    // Perpindahan status yang diizinkan. Selesai dan Dibatalkan tidak bisa berubah lagi.
    private const ALUR = [
        'menunggu'   => ['diproses', 'dibatalkan'],
        'diproses'   => ['siap', 'dibatalkan'],
        'siap'       => ['selesai', 'dibatalkan'],
        'selesai'    => [],
        'dibatalkan' => [],
    ];

    // Catatan keamanan: kolom uang dan status ada di sini karena pesanan dibuat dari kode
    // (lihat buat() di bawah). Jangan pernah memanggil Pesanan::create($request->all()).
    protected $fillable = [
        'user_id',
        'nama_pelanggan',
        'no_whatsapp',
        'catatan',
        'status',
        'subtotal',
        'pajak',
        'biaya_layanan',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'      => 'integer',
            'pajak'         => 'integer',
            'biaya_layanan' => 'integer',
            'total'         => 'integer',
        ];
    }

    // ---------------------------------------------------------------
    // Activity log: dicatat otomatis di sini, bukan di tiap controller,
    // supaya semua sumber pesanan (admin, kasir, pelanggan) otomatis
    // tercatat tanpa perlu mengubah kode yang sudah ada.
    // ---------------------------------------------------------------
    protected static function booted(): void
    {
        static::created(function (Pesanan $pesanan) {
            Aktivitas::catat("Pesanan {$pesanan->kode} dibuat atas nama {$pesanan->nama_pelanggan}.");
        });

        static::updated(function (Pesanan $pesanan) {
            if ($pesanan->wasChanged('status')) {
                Aktivitas::catat("Status pesanan {$pesanan->kode} diubah menjadi {$pesanan->label_status}.");
            }
        });
    }

    // ---------------------------------------------------------------
    // Relasi
    // ---------------------------------------------------------------

    // Akun pemesan (kosong kalau pemesannya tamu tanpa akun)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Isi pesanan
    public function items(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }

    // ---------------------------------------------------------------
    // Atribut bantu
    // ---------------------------------------------------------------

    // Kode pesanan, dibuat dari id: 8 menjadi "PSN-0008". Tidak disimpan di database.
    protected function kode(): Attribute
    {
        return Attribute::get(fn () => 'PSN-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

    // Tulisan status untuk tampilan: "diproses" menjadi "Diproses"
    protected function labelStatus(): Attribute
    {
        return Attribute::get(fn () => self::STATUS[$this->status] ?? ucfirst($this->status));
    }

    // Jumlah seluruh porsi di pesanan ini
    public function jumlahPorsi(): int
    {
        return (int) $this->items->sum('jumlah');
    }

    // ---------------------------------------------------------------
    // Aturan status
    // ---------------------------------------------------------------

    // Status apa saja yang boleh dituju dari status sekarang
    public function statusBerikutnya(): array
    {
        return self::ALUR[$this->status] ?? [];
    }

    public function bolehKe(string $statusBaru): bool
    {
        return in_array($statusBaru, $this->statusBerikutnya(), true);
    }

    // Hanya pesanan yang dibatalkan yang boleh dihapus
    public function bolehDihapus(): bool
    {
        return $this->status === 'dibatalkan';
    }

    // ---------------------------------------------------------------
    // Membuat pesanan (dipakai bersama oleh admin, kasir, dan pelanggan)
    // ---------------------------------------------------------------

    /**
     * Membuat pesanan lengkap dengan isinya.
     *
     * @param array    $pemesan   ['nama_pelanggan' => ..., 'no_whatsapp' => ..., 'catatan' => ...]
     * @param array    $barisItem daftar ['menu_id' => ..., 'jumlah' => ...]
     * @param int|null $userId    id akun pemesan, kosong kalau tamu
     *
     * Harga TIDAK PERNAH diambil dari input: selalu dibaca dari database di sini.
     */
    public static function buat(array $pemesan, array $barisItem, ?int $userId = null, string $status = 'menunggu'): self
    {
        return DB::transaction(function () use ($pemesan, $barisItem, $userId, $status) {

            // Gabungkan baris dengan menu yang sama supaya tidak dobel
            $jumlahPerMenu = [];
            foreach ($barisItem as $baris) {
                $menuId = (int) $baris['menu_id'];
                $jumlahPerMenu[$menuId] = ($jumlahPerMenu[$menuId] ?? 0) + (int) $baris['jumlah'];
            }

            if ($jumlahPerMenu === []) {
                throw ValidationException::withMessages([
                    'items' => 'Pilih minimal satu menu.',
                ]);
            }

            // Ambil menu dari database: hanya yang tersedia dan kategorinya aktif
            $menus = Menu::whereIn('id', array_keys($jumlahPerMenu))
                ->where('tersedia', true)
                ->whereHas('kategori', fn ($q) => $q->where('aktif', true))
                ->get()
                ->keyBy('id');

            if ($menus->count() !== count($jumlahPerMenu)) {
                throw ValidationException::withMessages([
                    'items' => 'Ada menu yang sudah tidak tersedia. Silakan periksa kembali pesanan.',
                ]);
            }

            // Hitung isi pesanan dan subtotal dari harga di database
            $subtotal = 0;
            $items = [];

            foreach ($jumlahPerMenu as $menuId => $jumlah) {
                $menu = $menus[$menuId];
                $subtotalBaris = $menu->harga * $jumlah;
                $subtotal += $subtotalBaris;

                $items[] = [
                    'menu_id'   => $menu->id,
                    'nama_menu' => $menu->nama,   // salinan, supaya riwayat tidak berubah
                    'harga'     => $menu->harga,  // salinan
                    'jumlah'    => $jumlah,
                    'subtotal'  => $subtotalBaris,
                ];
            }

            // Pajak dan biaya layanan dari persen di Pengaturan Sistem, disimpan sebagai nominal
            $pengaturan = Pengaturan::ambil();
            $pajak = (int) round($subtotal * $pengaturan->pajak / 100);
            $biayaLayanan = (int) round($subtotal * $pengaturan->biaya_layanan / 100);

            $pesanan = static::create([
                'user_id'        => $userId,
                'nama_pelanggan' => $pemesan['nama_pelanggan'],
                'no_whatsapp'    => $pemesan['no_whatsapp'],
                'catatan'        => $pemesan['catatan'] ?? null,
                'status'         => $status,
                'subtotal'       => $subtotal,
                'pajak'          => $pajak,
                'biaya_layanan'  => $biayaLayanan,
                'total'          => $subtotal + $pajak + $biayaLayanan,
            ]);

            $pesanan->items()->createMany($items);

            return $pesanan;
        });
    }
}