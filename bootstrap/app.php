<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\RefreshTokenMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification_handling',
        ]);
        //

        $middleware->alias([
            'jwt.auth' => Authenticate::class,
            'jwt.refresh' => RefreshToken::class,
            'refresh.token' => RefreshTokenMiddleware::class,
            'check.role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);
            }
        });
    })->create();
