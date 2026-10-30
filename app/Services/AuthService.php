<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Service class for handling user authentication operations,
 * including registration, authentication, and token issuance.
 */
class AuthService
{
    /**
     * Registers a new user with the provided data.
     * Returns the created User instance.
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed automatically by the model cast
        ]);
    }

    /**
     * Returns the user if email and password are correct, otherwise null.
     */
    public function authenticate(string $email, string $password): ?User
    {
        //check if user exists and password is correct
        $user = User::where('email', $email)->first();

        // If the user doesn't exist or the password doesn't match, return null
        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        // If the user exists and the password matches, return the user details
        return $user;
    }

    /**
     * Creates a token that expires in 7 days.
     * Returns the plain token (shown once) and its expiry time.
     */
    public function issueToken(User $user): array
    {
        // Set the token to expire in 7 days
        $expiresAt = now()->addDays(7);

        // Create a new token for the user with the specified expiration time
        $token = $user->createToken('api-token', ['*'], $expiresAt);

        // Return the token details, including the plain text token, type, and expiration time
        return [
            'token' => $token->plainTextToken, //token is shown only once when created
            'token_type' => 'Bearer',          //standard token type for API authentication
            'expires_at' => $expiresAt->utc()->toIso8601String(), //expires_at is in UTC format for consistency across time zones
        ];
    }
}