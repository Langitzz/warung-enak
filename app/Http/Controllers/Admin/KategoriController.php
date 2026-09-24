<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    // Halaman daftar kategori (sekalian hitung jumlah menu tiap kategori), bisa dicari & difilter status
    public function index(Request $request)
    {
        $kategoris = Kategori::withCount('menus')
            ->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%' . $request->cari . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('aktif', $request->status === 'aktif'))
            ->orderBy('nama')
            ->get();

        return view('admin.kategori.index', compact('kategoris'));
    }

    // Halaman form tambah kategori
    public function create()
    {
        return view('admin.kategori.create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        // Checkbox yang tidak dicentang tidak ikut terkirim, jadi dicek manual
        $data['aktif'] = $request->boolean('aktif');

        Kategori::create($data);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Halaman form edit kategori
    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    // Simpan perubahan kategori
    public function update(Request $request, Kategori $kategori)
    {
        $data = $this->validasi($request, $kategori);

        $data['aktif'] = $request->boolean('aktif');

        $kategori->update($data);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // Hapus kategori
    public function destroy(Kategori $kategori)
    {
        // Kategori yang masih dipakai menu tidak boleh dihapus
        if ($kategori->menus()->exists()) {
            return redirect()
                ->route('admin.kategori.index')
                ->with('error', 'Kategori "'.$kategori->nama.'" tidak bisa dihapus karena masih dipakai oleh menu.');
        }

        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    // Aturan validasi, dipakai bareng oleh store() dan update()
    private function validasi(Request $request, ?Kategori $kategori = null): array
    {
        return $request->validate(
            [
                // unique: nama tidak boleh sama dengan kategori lain.
                // Saat edit, kategori itu sendiri dikecualikan lewat ignore().
                'nama' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('kategoris', 'nama')->ignore($kategori),
                ],
            ],
            [
                'nama.required' => 'Nama kategori wajib diisi.',
                'nama.max' => 'Nama kategori maksimal 50 karakter.',
                'nama.unique' => 'Nama kategori ini sudah ada.',
            ]
        );
    }
}
