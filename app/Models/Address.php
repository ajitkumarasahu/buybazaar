<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The Address model represents a user's address in the application.
 * It defines the attributes and relationships associated with an address.
 */
//THIS CALLS MANAGE DATABASE TABLES AND RELATIONSHIPS AND OPERATIONS ON THE TABLES
class Address extends Model
{
    /**
     * 'user_id' and 'is_default' are NOT fillable: the owner comes from the
     * logged-in user, and the default flag is controlled by our service.
     */
    protected $fillable = [
        'label',
        'recipient_name',
        'phone',
        'line1',
        'line2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    /**
     * Casts the 'is_default' attribute to a boolean when accessed.
     * This ensures that the 'is_default' attribute is always treated as a boolean value.
     *
     * @return array The attribute casts for the model.
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Defines the relationship between the Address model and the User model.
     * An address belongs to a user, and this method establishes that relationship.
     *
     * @return BelongsTo The relationship instance representing the user associated with the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}