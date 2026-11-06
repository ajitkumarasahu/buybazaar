<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

/**
 * Policy class for managing access control to Address resources.
 * Ensures that users can only view, update, or delete their own addresses.
 */
class AddressPolicy
{
    /**
     * Determines if the given user can view the specified address.
     * A user can only view their own addresses.
     *
     * @param User $user The authenticated user attempting to view the address.
     * @param Address $address The address being accessed.
     * @return bool True if the user owns the address, false otherwise.
     */
    public function view(User $user, Address $address): bool
    {
        return (int) $user->id === (int) $address->user_id;
    }

    /**
     * Determines if the given user can update the specified address.
     * A user can only update their own addresses.
     *
     * @param User $user The authenticated user attempting to update the address.
     * @param Address $address The address being accessed.
     * @return bool True if the user owns the address, false otherwise.
     */
    public function update(User $user, Address $address): bool
    {
        return (int) $user->id === (int) $address->user_id;
    }

    /**
     * Determines if the given user can delete the specified address.
     * A user can only delete their own addresses.
     *
     * @param User $user The authenticated user attempting to delete the address.
     * @param Address $address The address being accessed.
     * @return bool True if the user owns the address, false otherwise.
     */
    public function delete(User $user, Address $address): bool
    {
        return (int) $user->id === (int) $address->user_id;
    }
}