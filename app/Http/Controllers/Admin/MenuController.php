<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    // Halaman daftar menu, bisa dicari & difilter kategori/status
    public function index(Request $request)
    {
        $menus = Menu::with('kategori')
            ->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%' . $request->cari . '%'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori_id', $request->integer('kategori')))
            ->when($request->filled('status'), fn ($q) => $q->where('tersedia', $request->status === 'tersedia'))
            ->latest()
            ->get();

        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.menu.index', compact('menus', 'kategoris'));
    }

    // Halaman form tambah menu
    public function create()
    {
        // Dropdown kategori: hanya kategori yang aktif, urut A-Z
        $kategoris = Kategori::where('aktif', true)->orderBy('nama')->get();

        return view('admin.menu.create', compact('kategoris'));
    }

    // Simpan menu baru ke database
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        // Checkbox yang tidak dicentang tidak ikut terkirim, jadi dicek manual
        $data['tersedia'] = $request->boolean('tersedia');

        Menu::create($data);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    // Halaman form edit menu
    public function edit(Menu $menu)
    {
        // Kategori aktif, ditambah kategori menu ini sendiri (kalau kebetulan sudah dinonaktifkan)
        $kategoris = Kategori::where('aktif', true)
            ->orWhere('id', $menu->kategori_id)
            ->orderBy('nama')
            ->get();

        return view('admin.menu.edit', compact('menu', 'kategoris'));
    }

    // Simpan perubahan menu
    public function update(Request $request, Menu $menu)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            // Ada foto baru: hapus foto lama kalau ada, lalu simpan yang baru
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            // Tidak ada foto baru, tapi kotak "Hapus foto" dicentang: hapus foto lama
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = null;
        } else {
            // Tidak ada perubahan pada foto: jangan sentuh foto yang sudah tersimpan
            unset($data['foto']);
        }

        $data['tersedia'] = $request->boolean('tersedia');

        $menu->update($data);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    // Hapus menu
    public function destroy(Menu $menu)
    {
        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    // Aturan validasi, dipakai bareng oleh store() dan update()
    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'harga' => ['required', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
