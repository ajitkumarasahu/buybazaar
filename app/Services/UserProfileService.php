<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    /**
     * Changes the password and logs the user out of every OTHER device.
     * If someone stole an old token, this makes it useless.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword, ?int $keepTokenId): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update(['password' => $newPassword]); // hashed by the model cast

        $tokens = $user->tokens();

        if ($keepTokenId) {
            $tokens->where('id', '!=', $keepTokenId);
        }

        $tokens->delete();
    }
}