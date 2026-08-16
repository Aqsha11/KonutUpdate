<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminSessionTimeout;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
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
            'admin' => AdminMiddleware::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'admin.session-timeout' => AdminSessionTimeout::class,
        ]);

        $middleware->web(append: [SecurityHeaders::class]);

        if (filter_var(env('TRUST_PROXIES'), FILTER_VALIDATE_BOOLEAN)) {
            $middleware->trustProxies(at: '*');
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            if (app()->isProduction()) {
                logger()->error('Uncaught exception', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    })->create();
