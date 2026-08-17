<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPegawai
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->user() || ! auth()->user()->isPegawai()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
