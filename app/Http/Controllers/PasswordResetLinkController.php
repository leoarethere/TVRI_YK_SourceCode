<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Menampilkan tampilan permintaan tautan reset kata sandi.
     */
    public function create(): View
    {
        return view('auth.forgot-password', [
            'title' => 'Lupa Kata Sandi',
        ]);
    }

    /**
     * Menangani permintaan tautan reset kata sandi yang masuk.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $exception) {
            report($exception);
        }

        // Selalu berikan respons yang sama agar keberadaan akun tidak dapat ditebak.
        return back()
            ->withInput($request->only('email'))
            ->with('success', __('passwords.request_received'));
    }
}
