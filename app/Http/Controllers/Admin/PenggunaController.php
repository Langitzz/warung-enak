<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    // Role yang tersedia (kunci = nilai yang disimpan, isi = tulisan di dropdown)
    private const ROLE = [
        'admin' => 'Admin',
        'kasir' => 'Kasir',
        'chef' => 'Chef',
        'supervisor' => 'Supervisor',
        'owner' => 'Owner',
        'pelanggan' => 'Pelanggan',
    ];

    // Halaman daftar pengguna
    public function index()
    {
        $penggunas = User::orderBy('name')->get();

        return view('admin.pengguna.index', compact('penggunas'));
    }

    // Halaman form tambah pengguna
    public function create()
    {
        $roles = self::ROLE;

        return view('admin.pengguna.create', compact('roles'));
    }

    // Simpan pengguna baru
    public function store(Request $request)
    {
        // Hanya kolom yang divalidasi yang disimpan, tidak pernah $request->all().
        $data = $request->validate($this->aturan(), $this->pesan());

        // Switch yang dimatikan tetap terkirim sebagai "0" (input tersembunyi di form)
        $data['aktif'] = $request->boolean('aktif');

        // Password otomatis di-hash oleh cast 'hashed' di model User
        User::create($data);

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // Halaman form edit pengguna
    public function edit(User $pengguna)
    {
        // Akun Zii tidak boleh diedit.
        if ($pengguna->name === 'Zii') {
            return redirect()
                ->route('admin.pengguna.index')
                ->with('error', 'Akun Zii tidak dapat diedit.');
        }

        $roles = self::ROLE;

        return view('admin.pengguna.edit', compact('pengguna', 'roles'));
    }

    // Simpan perubahan pengguna
    public function update(Request $request, User $pengguna)
    {
        // Akun Zii tidak boleh diedit.
        if ($pengguna->name === 'Zii') {
            return redirect()
                ->route('admin.pengguna.index')
                ->with('error', 'Akun Zii tidak dapat diedit.');
        }

        // Pengaman: admin tidak boleh mengubah role atau menonaktifkan akunnya sendiri.
        // Nilainya dipaksa tetap di sisi server, walau form dimanipulasi.
        if ($pengguna->is($request->user())) {
            $request->merge([
                'role' => $pengguna->role,
                'aktif' => true,
            ]);
        }

        $data = $request->validate($this->aturan($pengguna), $this->pesan());

        $data['aktif'] = $request->boolean('aktif');

        // Password dikosongkan saat edit = tidak diubah
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $pengguna->update($data);

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    // Hapus pengguna
    public function destroy(Request $request, User $pengguna)
    {
        // Akun Zii tidak boleh dihapus.
        if ($pengguna->name === 'Zii') {
            return redirect()
                ->route('admin.pengguna.index')
                ->with('error', 'Akun Zii tidak dapat dihapus.');
        }

        // Pengaman: admin tidak boleh menghapus akunnya sendiri
        if ($pengguna->is($request->user())) {
            return redirect()
                ->route('admin.pengguna.index')
                ->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if ($pengguna->foto) {
            Storage::disk('public')->delete($pengguna->foto);
        }

        $pengguna->delete();

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    // Aturan validasi, dipakai bareng oleh store() dan update()
    private function aturan(?User $pengguna = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pengguna)],
            'no_whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'role' => ['required', Rule::in(array_keys(self::ROLE))],

            // Saat menambah: wajib. Saat mengedit: boleh kosong (berarti tidak diubah).
            'password' => $pengguna
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    private function pesan(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'no_whatsapp.regex' => 'Nomor hanya boleh berisi angka, spasi, dan tanda + - ( ).',
            'no_whatsapp.max' => 'Nomor maksimal 20 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }
}
