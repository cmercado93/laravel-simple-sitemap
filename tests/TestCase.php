<?php

namespace Cmercado93\LaravelSimpleSitemap\Tests;

use Cmercado93\LaravelSimpleSitemap\SiteMapProvider;
use Orchestra\Testbench\TestCase as TestCaseBase;

class TestCase extends TestCaseBase
{
    protected function getPackageProviders($app)
    {
        return [
            SiteMapProvider::class,
        ];
    }
}
