<?php

namespace App\Http\Controllers;

use App\Models\AksesInformasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PegawaiLoginController extends Controller
{
    public function index(): View
    {
        return view('auth.pegawai-login', [
            'title' => 'Portal Pegawai',
            'active' => 'login',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        try {
            $authenticated = Auth::attempt($credentials);
        } catch (\RuntimeException $e) {
            \Illuminate\Support\Facades\Log::warning('Pegawai login authentication hash failure: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'email' => 'Format kredensial tidak valid atau akun memerlukan pembaruan kata sandi.',
            ])->onlyInput('email');
        }

        if ($authenticated) {
            $user = Auth::user();

            // Proteksi: Hanya user dengan role 'pegawai' yang boleh login di sini
            if (! $user->isPegawai()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun ini bukan akun pegawai. Silakan gunakan halaman login yang sesuai.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            $internal = AksesInformasi::internal();
            $redirectUrl = $internal->url && $internal->url !== '#'
                ? $internal->url
                : url('/');

            return redirect($redirectUrl)
                ->with('success', 'Selamat datang, '.$user->name.'!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $userName = Auth::user()->name;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pegawai.login')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa, '.$userName.'!');
    }
}
