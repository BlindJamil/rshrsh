<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\CheckPermission; // <-- Add this use statement
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'admin' => IsAdmin::class,
            'permission' => CheckPermission::class, // <-- ADD THIS LINE
        ]);

        $middleware->prependToGroup('web', \App\Http\Middleware\AutoLogout::class);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();