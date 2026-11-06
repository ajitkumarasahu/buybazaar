<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Represents a user in the application, including their authentication
 * credentials, role, and account status.
 */
class User extends Authenticatable
{
    //make sure to include the necessary traits for API tokens, 
    //factories, and notifications
    use HasApiTokens, HasFactory, Notifiable;

    // Define constants for user roles and account statuses to avoid magic strings in the code.
    //default role is customer and default status is active. Admins can be created manually in the database or through a separate admin interface.
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_ADMIN = 'admin';

    // Define constants for user account statuses to avoid magic strings in the code.
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Fields that can be mass-assigned from request data.
     * 'role' and 'status' are deliberately NOT here, so a client
     * can never make themselves an admin by sending "role": "admin".
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // plain passwords are hashed automatically when saved
        ];
    }

    /**
     * Defines a one-to-many relationship between User and Address.
     * A user can have multiple addresses, but each address belongs to one user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    
    /**
     * Checks if the user account is active.
     * Returns true if the status is 'active', false otherwise.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Checks if the user has an admin role.
     * Returns true if the role is 'admin', false otherwise.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}