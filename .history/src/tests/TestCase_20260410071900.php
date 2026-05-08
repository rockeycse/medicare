<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Client as PassportClient;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPassport();
    }

    protected function setUpPassport(): void
    {
        $client = PassportClient::forceCreate([
            'name'                   => 'Test Personal Access Client',
            'secret'                 => \Illuminate\Support\Str::random(40),
            'provider'               => 'users',
            'personal_access_client' => true,
            'password_client'        => false,
            'revoked'                => false,
        ]);

        \Laravel\Passport\PersonalAccessClient::forceCreate([
            'client_id' => $client->id,
        ]);
    }
}
