<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Swap the Auth0 guard for a standard Eloquent session guard in tests.
        // This allows actingAs($user) to work without a real Auth0 session.
        $this->app['config']->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
    }
}
