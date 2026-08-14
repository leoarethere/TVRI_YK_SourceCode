<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

use function Illuminate\Support\defer;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
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

            $date = now()->toDateString();
            $anonymizedIp = hash('sha256', $request->ip().config('app.key'));

            defer(function () use ($anonymizedIp, $date): void {
                try {
                    $timestamp = now();

                    DB::table('visitors')->upsert(
                        [[
                            'ip_address' => $anonymizedIp,
                            'date' => $date,
                            'hits' => 1,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]],
                        ['ip_address', 'date'],
                        [
                            'hits' => DB::raw('hits + 1'),
                            'updated_at' => $timestamp,
                        ],
                    );
                } catch (\Throwable $exception) {
                    Log::error('Visitor tracking failed: '.$exception->getMessage());
                }
            })->always();
        }

        return $next($request);
    }
}
