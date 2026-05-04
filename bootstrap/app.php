<?php

use App\Http\Middleware\ApiBearerTokenMiddleware;
use App\Http\Middleware\EnsureCompanyPermission;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureUserCompanyAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.bearer' => ApiBearerTokenMiddleware::class,
            'company.access' => EnsureUserCompanyAccess::class,
            'company.perm' => EnsureCompanyPermission::class,
            'super.admin' => EnsureSuperAdmin::class,
        ]);

        // Session-authenticated JSON under web; browsers send CSRF from Blade, but
        // tools like Postman only need the session cookie once logged in.
        $middleware->validateCsrfTokens(except: [
            'masters/api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
