<?php

namespace App\Providers;

use App\Repositories\Auth0UserRepository;
use Auth0\Laravel\Events\LoginAttempting;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Override 'auth0.repository' after the SDK registers its type-checked singleton.
        // We cannot bind UserRepository::class (it's final and causes a TypeError in the
        // SDK's own closure). Overriding the named alias in boot() is the correct approach.
        $this->app->singleton('auth0.repository', fn () => $this->app->make(Auth0UserRepository::class));

        // Forcer le sélecteur de compte Auth0 à chaque connexion : l'utilisateur voit toujours
        // le formulaire / le choix de compte (au lieu d'une ré-authentification SSO silencieuse).
        // Indispensable après une suppression pour pouvoir se connecter à un AUTRE compte.
        Event::listen(LoginAttempting::class, static function (LoginAttempting $event): void {
            $event->parameters['prompt'] = 'select_account';
        });

        Builder::defaultStringLength(191);

        // Fix SSL certificate verification on Windows/WAMP
        $caBundle = 'C:\\wamp64\\bin\\php\\php8.4.15\\extras\\ssl\\cacert.pem';
        if (file_exists($caBundle)) {
            ini_set('curl.cainfo', $caBundle);
            ini_set('openssl.cafile', $caBundle);
        }
    }
}
