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
        PassportClient::forceCreate([
            'name'          => 'Test Personal Access Client',
            'secret'        => \Illuminate\Support\Str::random(40),
            'provider'      => 'users',
            'redirect_uris' => [],
            'grant_types'   => ['personal_access'],
            'revoked'       => false,
        ]);
    }
}
