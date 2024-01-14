<?php

use Illuminate\Support\Facades\Route;
use Cmercado93\LaravelSimpleSitemap\Controller\SiteMapController;

Route::get("/sitemap.xml", [SiteMapController::class, 'init'])
    ->name('sitemap');
