# Generador de sitemap.xml para Laravel

Esta libreria permite generar un sitemap.xml a partir de las rutas de Laravel.

Es compatible con versiones de Laravel mayores a 5.8

Al crear la cache de las rutas los datos son tambien son guardados.

## Instalación:

Instala la librería mediante Composer:

```bash
composer require cmercado93/laravel-simple-sitemap
```
## Uso en routes/web.php:

```php
<?php

use App\Http\Controllers\TestController;
use Cmercado93\LaravelSimpleSitemap\Common\Frequency;

Route::get('/', function () {
    return view('welcome');
})
    ->sitemap([
        'priority' => 1,
        'frequency' => Frequency::MONTHLY,
        'last_update' => '2024-01-01',
    ]);

Route::get('/page-0', [TestController::class, 'index'])
    ->sitemap()
    ->name('page-0');

Route::get('/page-1/{param-1}', [TestController::class, 'page1'])
    ->sitemap([
        'priority' => 0.7,
        'frequency' => Frequency::MONTHLY,
        'last_update' => '2024-01-01',
        'parameters' => [
            'param-1' => 'value-1',
        ],
    ])
    ->name('page-1');

Route::get('/page-2', [TestController::class, 'page2'])
    ->sitemap([
        'priority' => 0.8,
        'last_update' => now(),
        'parameters' => [
            'param-1' => 'value-1',
            'param-2' => 'value-2',
            'param-3' => 'value-3',
        ],
    ])
    ->name('page-2');

Route::get('/page-3', [TestController::class, 'page3'])
    ->name('page-3');

Route::get('/page-4/{param-1}', [TestController::class, 'page4'])
    ->sitemap([
        'parameters' => [
            'param-1' => 'value-1',
            'param-2' => 'value-2',
            'param-3' => 'value-3',
        ],
    ])
    ->name('page-4');
```

## Resultado del ejemplo anterior

El sitemap se mostrara al ingresar a la URL `http://mi.sitio/sitemap.xml`

```xml
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://mi.sitio/</loc>
    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>http://mi.sitio/page-2?param-1=value-1&amp;param-2=value-2&amp;param-3=value-3</loc>
    <lastmod>2024-01-14T01:06:06+00:00</lastmod>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>http://mi.sitio/page-1/value-1</loc>
    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc>http://mi.sitio/page-0</loc>
  </url>
  <url>
    <loc>http://mi.sitio/page-4/value-1?param-2=value-2&amp;param-3=value-3</loc>
  </url>
</urlset>
```
