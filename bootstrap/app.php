<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'active'     => \App\Http\Middleware\EnsureUserIsActive::class,
            'json.force' => \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Jangan report exception ini ke Monolog (cegah OOM loop)
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class,
            \Spatie\Permission\Exceptions\UnauthorizedException::class,
            \Symfony\Component\ErrorHandler\Error\FatalError::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        ]);

        // Semua exception → return JSON ringkas (TANPA stack trace)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {

                $status = 500;

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $status = 403;
                } elseif ($e instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                    $status = 403;
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal.',
                        'data'    => null,
                        'errors'  => $e->errors(),
                    ], 422);
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $status = 404;
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    $status = 405;
                }

                $messages = [
                    401 => 'Unauthenticated. Token tidak valid atau sudah expired.',
                    403 => 'Forbidden. Anda tidak memiliki akses.',
                    404 => 'Endpoint tidak ditemukan.',
                    405 => 'Method tidak diizinkan.',
                    500 => app()->isProduction() ? 'Terjadi kesalahan pada server.' : $e->getMessage(),
                ];

                return response()->json([
                    'success' => false,
                    'message' => $messages[$status] ?? 'Server Error.',
                    'data'    => null,
                    'errors'  => null,
                ], $status);
            }
        });

    })->create();
