<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\UseCookieToken;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
        $middleware->api(prepend: [
            UseCookieToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
            $exceptions->render(function (UnauthorizedException $e,Request $request) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to access this resource.'
                ], 403);
            });
    })->create();
