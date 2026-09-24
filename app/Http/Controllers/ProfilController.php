<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    // Halaman profil akun (satu alamat untuk semua role, tampilannya menyesuaikan role)
    public function edit(Request $request)
    {
        $user = $request->user();

        return view($this->namaView($user), compact('user'));
    }

    // Simpan data profil (nama, email, nomor WhatsApp, foto)
    public function update(Request $request)
    {
        // Selalu akun yang sedang login. ID tidak pernah diambil dari form,
        // jadi tidak ada cara mengubah profil orang lain.
        $user = $request->user();

        // Hanya kolom yang divalidasi di bawah yang disimpan.
        // 'role' dan 'aktif' sengaja tidak ada, jadi tidak bisa diubah lewat form ini.
        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'no_whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
                'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'name.max' => 'Nama maksimal 100 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email ini sudah dipakai akun lain.',
                'no_whatsapp.regex' => 'Nomor hanya boleh berisi angka, spasi, dan tanda + - ( ).',
                'no_whatsapp.max' => 'Nomor maksimal 20 karakter.',
                'foto.image' => 'Foto harus berupa gambar.',
                'foto.mimes' => 'Foto harus berformat jpg, jpeg, png, atau webp.',
                'foto.max' => 'Ukuran foto maksimal 2 MB.',
            ]
        );

        if ($request->hasFile('foto')) {
            // Ada foto baru: hapus foto lama kalau ada, lalu simpan yang baru
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            // Tidak ada foto baru, tapi kotak "Hapus foto" dicentang: hapus foto lama
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = null;
        } else {
            // Tidak ada perubahan pada foto: jangan sentuh foto yang sudah tersimpan
            unset($data['foto']);
        }

        $user->update($data);

        return redirect()
            ->route('profil.edit')
            ->with('success', 'Profil berhasil disimpan.');
    }

    // Ganti password (wajib mengisi password lama)
    public function updatePassword(Request $request)
    {
        // Error password ditaruh di "bag" terpisah bernama 'password', supaya tidak
        // tercampur dengan error form profil di halaman yang sama.
        $validated = $request->validateWithBag(
            'password',
            [
                'password_lama' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8', 'different:password_lama'],
            ],
            [
                'password_lama.required' => 'Password lama wajib diisi.',
                'password_lama.current_password' => 'Password lama salah.',
                'password.required' => 'Password baru wajib diisi.',
                'password.confirmed' => 'Konfirmasi password baru tidak sama.',
                'password.min' => 'Password baru minimal 8 karakter.',
                'password.different' => 'Password baru harus berbeda dari password lama.',
            ]
        );

        // Password otomatis di-hash oleh cast 'hashed' di model User
        $request->user()->update(['password' => $validated['password']]);

        return redirect()
            ->route('profil.edit')
            ->with('success_password', 'Password berhasil diganti.');
    }

    // Pilih tampilan sesuai role
    private function namaView(User $user): string
    {
        return match ($user->role) {
            'admin' => 'admin.profil',
            'kasir' => 'kasir.profil',
            'pelanggan' => 'user.profil',
            default => abort(404),
        };
    }
}
