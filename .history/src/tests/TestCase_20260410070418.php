<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:client', [
            '--personal' => true,
            '--name'     => 'Test Personal Access Client',
            '--no-interaction' => true,
        ]);
    }
}
