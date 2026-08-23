<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            return $request->is('keranjang*', 'checkout*', 'pesanan-saya*', 'keluar*')
                ? route('customer.login')
                : route('login');
        });

        $middleware->redirectUsersTo(function ($request) {
            return $request->is('masuk*', 'register*')
                ? route('customer.catalog')
                : route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
