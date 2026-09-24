<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    // Halaman daftar pengeluaran, terbaru dulu
    public function index()
    {
        $pengeluarans = Pengeluaran::latest('tanggal')->latest('id')->paginate(15);

        return view('admin.pengeluaran.index', compact('pengeluarans'));
    }

    // Halaman form tambah pengeluaran
    public function create()
    {
        return view('admin.pengeluaran.create');
    }

    // Simpan pengeluaran baru
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        Pengeluaran::create($data);

        return redirect()
            ->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    // Halaman form edit pengeluaran
    public function edit(Pengeluaran $pengeluaran)
    {
        return view('admin.pengeluaran.edit', compact('pengeluaran'));
    }

    // Simpan perubahan pengeluaran
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $data = $this->validasi($request);

        $pengeluaran->update($data);

        return redirect()
            ->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    // Hapus pengeluaran
    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();

        return redirect()
            ->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // Aturan validasi, dipakai bareng oleh store() dan update()
    private function validasi(Request $request): array
    {
        return $request->validate(
            [
                'tanggal' => ['required', 'date'],
                'keterangan' => ['required', 'string', 'max:150'],
                'jumlah' => ['required', 'integer', 'min:1'],
            ],
            [
                'tanggal.required' => 'Tanggal wajib diisi.',
                'keterangan.required' => 'Keterangan wajib diisi.',
                'keterangan.max' => 'Keterangan maksimal 150 karakter.',
                'jumlah.required' => 'Jumlah wajib diisi.',
                'jumlah.min' => 'Jumlah harus lebih dari 0.',
            ]
        );
    }
}