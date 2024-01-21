# Laravel Simple Sitemap

Esta librería simplifica la creación de un archivo `sitemap.xml` utilizando directamente los datos proporcionados en la definición de las rutas.

### Instalación:

> Requisitos
- [PHP 7.1.3+](https://php.net/releases/)
- [Laravel 5.8+](https://github.com/laravel/laravel)

Instala la librería mediante Composer:

```bash
composer require cmercado93/laravel-simple-sitemap
```

## #Forma de uso

Para usarlo simplemente hay que agregar el método `sitemap` a la definición de la ruta.

**/routes/web.php**
```php
<?php

use App\Http\Controllers\TestController;
use Cmercado93\LaravelSimpleSitemap\Common\Frequency;

Route::get('/', function () {
    return view('welcome');
})
    ->sitemap([
        'priority' => 1,
        'frequency' => Frequency::WEEKLY,
        'last_update' => '2024-01-01',
    ]);
```

### Resultado del ejemplo anterior

El sitemap se mostrara al ingresar a la URL `http://mi.sitio/sitemap.xml`

```xml
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://mi.sitio/</loc>
    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>
```

### Definición del Método `sitemap`

El método `sitemap` facilita la inclusión de una entrada en el archivo de salida `/sitemap.xml`. Puede ser utilizado con o sin argumento, siendo el argumento principal un arreglo con varias propiedades:

- `priority` *(opcional)*:
  - Definición: Prioridad asignada a la URL en el sitemap.
  - Tipo: Valor flotante entre **0** y **1**.

- `frequency` *(opcional)*:
  - Definición: Frecuencia de actualizaciones que recibe la URL.
  - Referencia: Constantes de la clase `Cmercado93\LaravelSimpleSitemap\Common\Frequency`.

- `last_update` *(opcional)*:
  - Definición: Fecha de la última modificación de la ruta.
  - Tipo: Cadena de texto con formato `Y-m-d` u objeto que implemente la interfaz `\DateTimeInterface`.

### Licencia
Distribuido bajo la licencia MIT. Vea `LICENSE.md` para más información.
