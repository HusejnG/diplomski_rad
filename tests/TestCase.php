<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Testovi provjeravaju HTML koji generiše server, a ne kompajlirani
        // CSS/JS, pa ne smiju zavisiti od prethodnog `npm run build`.
        $this->withoutVite();
    }
}
