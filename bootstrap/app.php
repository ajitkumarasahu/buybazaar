<?php

use App\Http\Middleware\AssignRequestId;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        // Runs first on every API request.
        $middleware->api(prepend: [
            AssignRequestId::class,
        ]);

        // Short names we can use in routes.
        $middleware->alias([    
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        /**
         * Configure API requests to receive JSON error responses
         * instead of HTML error pages.
         *
         * JSON is returned for API routes or requests that expect JSON.
         */
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) => $request->is('api/*') || $request->expectsJson()
        );

        /**
         * Handle validation exceptions for API requests.
         *
         * Returns HTTP 422 with validation error details.
         */
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Validation failed.', 422, $e->errors());
            }
        });

        /**
         * Handle authentication exceptions for API requests.
         *
         * Returns HTTP 401 when the user is unauthenticated
         * or the authentication credentials are invalid.
         */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Unauthenticated.', 401);
            }
        });

        /**
         * Handle authorization exceptions for API requests.
         *
         * Returns HTTP 403 when the user is authenticated
         * but does not have permission to perform the action.
         */
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('You are not allowed to perform this action.', 403);
            }
        });

        /**
         * Handle resource-not-found exceptions for API requests.
         *
         * Returns HTTP 404 when the requested route or resource
         * cannot be found.
         */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Resource not found.', 404);
            }
        });

        /**
         * Handle other HTTP exceptions for API requests.
         *
         * Returns the exception message and corresponding HTTP status code.
         * Examples include HTTP 405 (Method Not Allowed) and
         * HTTP 429 (Too Many Requests).
         */
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error($e->getMessage() ?: 'Request failed.', $e->getStatusCode());
            }
        });

        /**
         * Handle unexpected exceptions for API requests.
         *
         * In production, returns a generic HTTP 500 response
         * without exposing internal error details when debug mode is disabled.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') && ! config('app.debug')) {
                return ApiResponse::error('Internal server error.', 500);
            }
        });

    })->create();