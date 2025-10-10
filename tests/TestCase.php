<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Load .env.testing file if it exists
        $testingEnvFile = base_path('.env.testing');
        if (File::exists($testingEnvFile)) {
            $dotenv = \Dotenv\Dotenv::createImmutable(base_path(), '.env.testing');
            $dotenv->load();
        }
    }
}
