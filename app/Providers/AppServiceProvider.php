<?php

namespace App\Providers;

use App\Repositories\Auth0UserRepository;
use Auth0\Laravel\UserRepository;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Use our custom repository so auth()->user() returns the local User model
        // and auth()->id() returns the integer DB ID (not the Auth0 sub string).
        $this->app->bind(UserRepository::class, Auth0UserRepository::class);
    }

    public function boot(): void
    {
        Builder::defaultStringLength(191);

        // Fix SSL certificate verification on Windows/WAMP
        $caBundle = 'C:\\wamp64\\bin\\php\\php8.4.15\\extras\\ssl\\cacert.pem';
        if (file_exists($caBundle)) {
            ini_set('curl.cainfo', $caBundle);
            ini_set('openssl.cafile', $caBundle);
        }
    }
}
