<?php

namespace Cmercado93\LaravelSimpleSitemap;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Cmercado93\LaravelSimpleSitemap\Common\Mixin;

class SiteMapProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        Route::mixin(new Mixin);
    }
}
