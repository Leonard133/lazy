<?php

namespace Leonard133\Lazy\Tests;

use Orchestra\Testbench\TestCase;
use Leonard133\Lazy\LazyServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [LazyServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
