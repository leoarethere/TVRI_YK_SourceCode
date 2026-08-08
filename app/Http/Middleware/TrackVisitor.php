<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya lacak request GET dan hindari rute admin/dashboard serta rute asset
        if ($request->isMethod('GET') &&
            ! $request->is('dashboard*') &&
            ! $request->is('login*') &&
            ! $request->is('pegawai*') &&
            ! $request->is('forgot-password*') &&
            ! $request->is('reset-password*') &&
            ! $request->is('livewire*') &&
            ! $request->ajax()) {

            try {
                $ip = $request->ip();
                $date = now()->toDateString();

                // Anonimisasi IP menggunakan Hashing (SHA-256) + Salt dari App Key
                // Ini memastikan IP asli tidak bisa dibaca balik (irreversible) demi kepatuhan Privasi Data
                $anonymizedIp = hash('sha256', $ip.config('app.key'));

                Visitor::query()->insertOrIgnore([
                    'ip_address' => $anonymizedIp,
                    'date' => $date,
                    'hits' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Visitor::query()
                    ->where('ip_address', $anonymizedIp)
                    ->where('date', $date)
                    ->increment('hits');
            } catch (\Exception $e) {
                Log::error('Visitor tracking failed: '.$e->getMessage());
            }
        }

        return $next($request);
    }
}
