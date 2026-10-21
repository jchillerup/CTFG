<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        // The test suite runs against a throwaway SQLite file (phpunit.xml);
        // make sure it exists before the framework tries to open it.
        $db = __DIR__ . '/../database/testing.sqlite';
        if (! file_exists($db)) {
            touch($db);
        }
        parent::setUp();
    }
}
