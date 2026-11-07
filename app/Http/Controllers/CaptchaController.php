<?php

namespace App\Http\Controllers;

use App\Rules\Captcha;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Captcha sederhana buatan sendiri berbasis SVG + session.
 *
 * Tidak membutuhkan API key maupun library eksternal. Soal berupa
 * operasi aritmetika kecil ("7 + 6 = ?") yang jawabannya disimpan
 * di session, lalu diverifikasi oleh App\Rules\Captcha.
 */
class CaptchaController extends Controller
{
    /**
     * Ambil karakter acak yang aman.
     */
    private function rand(): int
    {
        return random_int(1, 9);
    }

    /**
     * Render gambar captcha sebagai SVG.
     */
    public function image(): Response
    {
        [$question, $answer, $tokens] = $this->generate();

        session([Captcha::SESSION_KEY => $answer]);

        return response()
            ->view('auth.captcha', [
                'tokens' => $tokens,
                'width' => 152,
                'height' => 48,
            ])
            ->header('Content-Type', 'image/svg+xml')
            // Jangan pernah dicache — setiap tampilan halaman harus ada
            // pasangan soal–jawaban baru di session.
            ->header('Cache-Control', 'no-store, no-cache, max-age=0, private');
    }

    /**
     * Buat soal acak ("a + b") dan token yang sudah diberi posisi/rotasi acak.
     *
     * @return array{0: string, 1: int, 2: array<int, array{char: string, x: int, y: int, rotate: int, size: int, opacity: float}>}
     */
    private function generate(): array
    {
        $a = $this->rand();
        $b = $this->rand();
        $answer = $a + $b;

        $tokens = [];
        foreach (mb_str_split($a.' + '.$b.' = ?') as $i => $char) {
            $tokens[] = [
                'char' => $char === ' ' ? ' ' : $char,
                'x' => 8 + ($i * 16) + random_int(-3, 3),
                'y' => 30 + random_int(-3, 3),
                'rotate' => random_int(-18, 18),
                'size' => random_int(17, 23),
                'opacity' => round(random_int(70, 100) / 100, 2),
            ];
        }

        return [$a.' + '.$b.' = ?', $answer, $tokens];
    }
}
