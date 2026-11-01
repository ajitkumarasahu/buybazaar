<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiting for login attempts to prevent brute-force attacks.
        RateLimiter::for('login', function (Request $request) {
            
            // Limit the number of login attempts to 5 per minute based on the user's email and IP address.
            $email = $request->input('email');
            // Ensure the email is a string and convert it to lowercase for consistent rate limiting.
            $email = is_string($email) ? Str::lower($email) : '';
            // Combine the email and IP address to create a unique key for rate limiting.
            //if the email is not a string, it will be treated as an empty string,
            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });
    }
}
