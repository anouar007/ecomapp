<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Only disable CSRF middleware, keep auth and other middleware active
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }
}
