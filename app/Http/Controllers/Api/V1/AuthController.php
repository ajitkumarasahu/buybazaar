<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for handling user authentication endpoints,
 * including registration, login, logout, and retrieving the authenticated user's details.
 */
class AuthController extends Controller
{
    /**
     * AuthController constructor.
     * @param AuthService $authService
     */
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * Registers a new user.
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Create a new user using the validated request data
        $user = $this->authService->register($request->validated());

        //if user is created successfully then issue token and return response
        return ApiResponse::success('Registration successful.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        // Attempt to authenticate the user with the provided email and password
        $user = $this->authService->authenticate(
            $request->validated('email'),
            $request->validated('password')
        );

        // Same message for "no such email" and "wrong password",
        // so attackers can't discover which emails are registered.
        if (! $user) {
            return ApiResponse::error('Invalid email or password.', 401);
        }

        // Check if the user's account is active before issuing a token
        //then if the user is not active, return an error response
        if (! $user->isActive()) {
            return ApiResponse::error('Your account is not active.', 403);
        }

        // If authentication is successful and the account is active, issue a token and return it in the response
        return Apiresponse::success([ ...$this->authService->issueToken($user), ], 'Login successful.', 200);
        
    }

    public function logout(Request $request): JsonResponse
    {
        // Deletes only the token used for this request.
        $request->user()->currentAccessToken()->delete();

        // Return a success response indicating that the user has been logged out successfully
        return ApiResponse::success(null, 'Logged out successfully.', 200);
    }

    public function me(Request $request): JsonResponse
    {
        // Return the authenticated user's details in the response
        return ApiResponse::success(new UserResource($request->user()));
    }
}