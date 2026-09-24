<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Cek apakah role pengguna yang sedang login termasuk yang diizinkan.
     *
     * Cara pakai di route:
     *   'role:admin'          -> hanya admin
     *   'role:admin,kasir'    -> admin dan kasir
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Belum login: arahkan ke halaman login
        if (! $user) {
            return redirect()->route('login');
        }

        // Akun dinonaktifkan ketika sesinya masih berjalan: keluarkan sekarang juga
        if (! $user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda tidak aktif.']);
        }

        // Role tidak termasuk yang diizinkan: tolak dengan kode 403 (dilarang)
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
