<?php

// By default, Laravel tries to redirect to a Blade route.
// Since we're using Vue, we need to tell Laravel where our reset page is located.

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register the policies for the application.
        $this->registerPolicies();

        // Custom URL that is sent in the email for password reset
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return 'http://localhost:8000/reset-password/' . $token . '?email=' . $user->email;
        });
        // Custom URL that is sent in the email for email verification
        VerifyEmail::createUrlUsing(function ($notifiable) {
            // Create signed route
            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            return str_replace('http://localhost', 'http://localhost:8000', $signedUrl);
        });
    }
}
