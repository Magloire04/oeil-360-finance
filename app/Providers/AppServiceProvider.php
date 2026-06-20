<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

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
        Builder::defaultStringLength(191);

        // Fix SSL certificate verification on Windows/WAMP
        $caBundle = 'C:\\wamp64\\bin\\php\\php8.4.15\\extras\\ssl\\cacert.pem';
        if (file_exists($caBundle)) {
            ini_set('curl.cainfo', $caBundle);
            ini_set('openssl.cafile', $caBundle);
        }
    }
}
