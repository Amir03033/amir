<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests render Blade views but never need the compiled assets (CI has no `npm run build`).
        $this->withoutVite();
    }
}
