<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserProfileService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group User Profile
 *
 * APIs for managing the authenticated user's profile and addresses.
 */
class UserProfileController extends Controller
{
    /**
     * Create a new controller instance.
     * and call proffile service 
     */
    public function __construct(private UserProfileService $profileService)
    {
    }
    
    /**
     * Get the authenticated user's profile.
     * @return JsonResponse profile data of the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()));
    }

    /**
     * Update the authenticated user's profile.
     * Validates the request data and updates the user's profile.
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        // Update the authenticated user's profile using the validated data from the request.
        $user = $this->profileService->updateProfile($request->user(), $request->validated());

        return ApiResponse::success('Profile updated successfully.', 200);
    }

    /**
     * Change the authenticated user's password.
     * Validates the request data and changes the user's password.
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(ChangePasswordRequest $request): JsonResponse
    {
        // Change the authenticated user's password using the validated data from the request.
        $this->profileService->changePassword(
            $request->user(),
            $request->validated('current_password'),
            $request->validated('password'),
            $request->user()->currentAccessToken()?->id
        );

        return ApiResponse::success(null, 'Password changed successfully. Your other devices have been logged out.');
    }
}