<?php

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
    ->withMiddleware(function (Middleware $middleware) {
        //midlewares globales y de rutas
        /* $middleware->append([
            \App\Http\Middleware\MyCustomMiddleware::class,
        ]); */

        //El paquete Spatie proporciona su middleware incorporado
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class
        ]);

        //midlewares de grupos
        $middleware->group('api', [
           \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // si usas cookies (no obligatorio para móviles)
           'throttle:api',
           \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //para manejos de errores exceptions personalizadas
        /* $exceptions->render(function (\Throwable $e, $request) {
            // lógica personalizada
        }); */
    })->create();
