<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddressService
{
    //users can only have 10 addresses, so we define a constant for that limit
    public const MAX_ADDRESSES = 10;

    // List all addresses for a given user, ordered by default status and creation date.
    public function listFor(User $user): Collection
    {
        // Return the user's addresses, ordered by desc default status (default addresses first) and then by creation date (newest first).
        return $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->get();
    }

    // Create a new address for a given user with the provided data.
    public function create(User $user, array $data): Address
    {
        // Use a database transaction to ensure that the address creation and any related operations are atomic.
        return DB::transaction(function () use ($user, $data) {
            $this->lockUser($user->id);

            // Check if the user has reached the maximum number of addresses allowed.
            if ($user->addresses()->count() >= self::MAX_ADDRESSES) {
                throw ValidationException::withMessages([
                    'address' => ['You can save at most '.self::MAX_ADDRESSES.' addresses.'],
                ]);
            }

            // Determine if the new address should be set as the default. If the user has no addresses, 
            // the first address is automatically set as default.
            $wantsDefault = (bool) ($data['is_default'] ?? false);
            unset($data['is_default']);

            // Check if this is the user's first address.
            $isFirstAddress = ! $user->addresses()->exists();

            // Create the new address for the user with the provided data.
            $address = $user->addresses()->create($data);

            // The first address is always the default.
            if ($wantsDefault || $isFirstAddress) {
                $this->markAsDefault($address);
            }

            // Return the newly created address, refreshed from the database to ensure it has the latest data.
            return $address->refresh();
        });
    }

    // Update an existing address with the provided data.
    public function update(Address $address, array $data): Address
    {
        $address->update($data);

        return $address->refresh();
    }

    // Set a specific address as the default for the user. This method ensures that only one address is marked as default at any given time.
    public function setDefault(Address $address): Address
    {
        return DB::transaction(function () use ($address) {
            $this->lockUser($address->user_id);
            $this->markAsDefault($address);

            return $address->refresh();
        });
    }

    // Delete a specific address for the user. If the deleted address was the default, the newest remaining address is promoted to default.
    public function delete(Address $address): void
    {
        DB::transaction(function () use ($address) {
            $userId = $address->user_id;
            $this->lockUser($userId);

            $address->refresh(); // re-read inside the lock
            $wasDefault = $address->is_default;

            $address->delete();

            // If we removed the default, promote the newest remaining address.
            if ($wasDefault) {
                $next = Address::where('user_id', $userId)->orderByDesc('id')->first();

                if ($next) {
                    $this->markAsDefault($next);
                }
            }
        });
    }

    /**
     * Unset every other default of this user, then set this one.
     */
    private function markAsDefault(Address $address): void
    {
        Address::where('user_id', $address->user_id)
            ->where('id', '!=', $address->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $address->is_default = true;
        $address->save();
    }

    /**
     * Locks the user's row until the transaction ends. If two requests for the
     * same user arrive at the same moment, the second waits for the first.
     * This prevents "two default addresses" and going over the 10-address limit.
     */
    private function lockUser(int $userId): void
    {
        User::whereKey($userId)->lockForUpdate()->first();
    }
}