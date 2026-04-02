<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LogSlowRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'forcehttps' => \App\Http\Middleware\ForceHttpsFromProxy::class,
        ]);

        // kalau mau jadi global middleware (selalu jalan)
        $middleware->append(\App\Http\Middleware\ForceHttpsFromProxy::class);

        $middleware->append(LogSlowRequests::class);
    })
    ->withExceptions(function ($exceptions) {
        //
    })
    ->create();
