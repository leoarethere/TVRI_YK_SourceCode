<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsDashboardUser;
use App\Http\Middleware\IsPegawai;
use App\Http\Middleware\TrackVisitor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => IsAdmin::class,
            'dashboardUser' => IsDashboardUser::class,
            'isPegawai' => IsPegawai::class,
        ]);

        // Daftarkan TrackVisitor dan AuthenticateSession sebagai middleware web global
        $middleware->web(append: [
            AddSecurityHeaders::class,
            TrackVisitor::class,
            AuthenticateSession::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
