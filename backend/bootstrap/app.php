<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sanctum stateful domains for SPA
        $middleware->statefulApi();

        $middleware->redirectGuestsTo(fn () => null);

        // Prevent request forgery on web routes while keeping API routes stateless
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Alias middleware
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Throttle API requests
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Always return JSON for API routes
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson()
        );

        // Format validation errors
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Format auth errors (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthenticated. Please log in to continue.',
                    'data'    => null,
                ], 401);
            }
        });

        // Format not found errors
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => "{$model} not found.",
                    'data'    => null,
                ], 404);
            }
        });

        // Format Spatie permission/role authorization errors (403)
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'code'    => 403,
                    'message' => 'Access denied. This dashboard is only available for buyers and renters.',
                    'data'    => null,
                ], 403);
            }
        });

        // Format authorization errors (403)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'code'    => 403,
                    'message' => 'Access denied. You do not have permission to access this resource.',
                    'data'    => null,
                ], 403);
            }
        });
    })->create();
