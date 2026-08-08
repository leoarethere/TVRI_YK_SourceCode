<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsDashboardUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isDashboardUser()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
