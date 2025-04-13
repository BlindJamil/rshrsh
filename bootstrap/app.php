<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware alias
        $middleware->alias([
            'admin' => IsAdmin::class,
        ]);

        $middleware->prependToGroup('web', \App\Http\Middleware\AutoLogout::class);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();
