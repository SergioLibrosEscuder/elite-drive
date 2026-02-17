<?php

// By default, Laravel tries to redirect to a Blade route.
// Since we're using Vue, we need to tell Laravel where our reset page is located.

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Custom URL that is sent in the email
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return 'http://localhost:8000/reset-password/'.$token.'?email='.$user->email;
        });
    }
}