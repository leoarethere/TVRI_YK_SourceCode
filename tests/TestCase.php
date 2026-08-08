<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");
        $isInMemorySqlite = $connection === 'sqlite' && $database === ':memory:';
        $isDedicatedTestingDatabase = str_contains(strtolower($database), 'test');

        if (! app()->environment('testing') || (! $isInMemorySqlite && ! $isDedicatedTestingDatabase)) {
            throw new \RuntimeException(
                "Refusing to run tests against database [{$database}] on connection [{$connection}]."
            );
        }
    }
}
