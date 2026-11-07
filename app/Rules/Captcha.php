<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi jawaban captcha sederhana berbasis session.
 *
 * Jawaban disimpan di session oleh App\Http\Controllers\CaptchaController
 * saat gambar captcha dirender. Dapat dipakai sekali saja, lalu langsung
 * dibersihkan agar tidak bisa di-replay.
 */
class Captcha implements ValidationRule
{
    /**
     * Key session tempat jawaban captcha disimpan.
     */
    public const SESSION_KEY = 'captcha.answer';

    /**
     * Jalankan validasi.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $expected = session(self::SESSION_KEY);

        // Hapus jawaban agar hanya bisa dipakai satu kali.
        session()->forget(self::SESSION_KEY);

        if ($expected === null || ! is_numeric($value) || (int) $value !== (int) $expected) {
            $fail('Jawaban captcha tidak sesuai. Silakan coba lagi.');
        }
    }
}
