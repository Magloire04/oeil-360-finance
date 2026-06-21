<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\UserBootstrapService;
use Auth0\Laravel\UserRepositoryContract;
use Illuminate\Contracts\Auth\Authenticatable;

class Auth0UserRepository implements UserRepositoryContract
{
    public function __construct(private UserBootstrapService $bootstrap) {}

    public function fromSession(array $user): ?Authenticatable
    {
        $sub = $user['sub'] ?? null;
        if (! $sub) {
            return null;
        }

        $isNew = ! User::where('auth0_id', $sub)->exists();

        $localUser = User::updateOrCreate(
            ['auth0_id' => $sub],
            [
                'email' => $user['email'] ?? null,
                'name'  => $user['name'] ?? $user['nickname'] ?? $user['email'] ?? $sub,
            ]
        );

        if ($isNew) {
            $this->bootstrap->bootstrapNewUser($localUser);
        }

        return $localUser;
    }

    public function fromAccessToken(array $user): ?Authenticatable
    {
        return $this->fromSession($user);
    }
}
