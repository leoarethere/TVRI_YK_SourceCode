<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function index(): View
    {
        return view('auth.login', [
            'title' => 'Masuk',
            'active' => 'login',
        ]);
    }

    /**
     * Menangani proses login pengguna.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input dari form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // 2. Coba untuk mengautentikasi pengguna dengan remember me
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Deteksi akun pegawai yang salah portal — kembalikan ke sini + tampilkan link portal pegawai
            if ($user->isPegawai()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Akun pegawai tidak dapat login dari halaman ini.'])
                    ->with('show_pegawai_link', true)
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        // 3. Jika autentikasi gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menangani proses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        $userName = Auth::user()->name;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa, '.$userName.'!');
    }
}
