<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * If your original file had a `$middleware` property here, ensure it's included.
     * Example:
     * protected $middleware = [
     * // \App\Http\Middleware\TrustHosts::class,
     * \App\Http\Middleware\TrustProxies::class,
     * \Illuminate\Http\Middleware\HandleCors::class,
     * \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
     * \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
     * \App\Http\Middleware\TrimStrings::class,
     * \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
     * ];
     */
     // Add the $middleware property here if it existed in your original file

    /**
     * The application's route middleware groups.
     *
      * If your original file had a `$middlewareGroups` property here, ensure it's included.
     * Example:
     * protected $middlewareGroups = [
     * 'web' => [
     * \App\Http\Middleware\EncryptCookies::class,
     * \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
     * \Illuminate\Session\Middleware\StartSession::class,
     * // \Illuminate\Session\Middleware\AuthenticateSession::class, // Usually handled by auth middleware
     * \Illuminate\View\Middleware\ShareErrorsFromSession::class,
     * \App\Http\Middleware\VerifyCsrfToken::class,
     * \Illuminate\Routing\Middleware\SubstituteBindings::class,
     * ],
     *
     * 'api' => [
     * // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // If using Sanctum
     * \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
     * \Illuminate\Routing\Middleware\SubstituteBindings::class,
     * ],
     * ];
     */
     // Add the $middlewareGroups property here if it existed in your original file


    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\IsAdmin::class, // Existing admin middleware alias
        'permission' => \App\Http\Middleware\CheckPermission::class, // Permission middleware alias
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * If your original file had a `$middlewarePriority` property here, ensure it's included.
     * Example:
     * protected $middlewarePriority = [
     * \Illuminate\Session\Middleware\StartSession::class,
     * \Illuminate\View\Middleware\ShareErrorsFromSession::class,
     * \App\Http\Middleware\Authenticate::class,
     * \Illuminate\Session\Middleware\AuthenticateSession::class,
     * \Illuminate\Routing\Middleware\SubstituteBindings::class,
     * \Illuminate\Auth\Middleware\Authorize::class,
     * ];
     */
     // Add the $middlewarePriority property here if it existed in your original file

} // <-- This is the single, correct closing brace for the Kernel class