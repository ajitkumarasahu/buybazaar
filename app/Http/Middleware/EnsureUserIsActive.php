<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure that the authenticated user is active.
 * If the user is not active, it returns a JSON error response with a 403 status code.
 */
class EnsureUserIsActive
{
    //handle method checks if the authenticated user is active. If not, it returns a 403 error response.
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the authenticated user from the request
        $user = $request->user();

        //if the user exists and is not active, return a JSON error response with a 403 status code
        if ($user && ! $user->isActive()) {
            return ApiResponse::error('Your account is not active.', 403);
        }
        // If the user is active or not authenticated, proceed with the request
        return $next($request);
    }
}