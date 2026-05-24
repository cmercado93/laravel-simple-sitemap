<?php

namespace Cmercado93\LaravelSimpleSitemap\Tests\Unit;

use Cmercado93\LaravelSimpleSitemap\Common\SitemapException;
use Cmercado93\LaravelSimpleSitemap\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class EdgeCasesTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Sitemap vacío
    // -------------------------------------------------------------------------

    public function test_no_sitemap_routes_returns_empty_envelope()
    {
        Route::get('/page', fn() => 'page')->name('page');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>',
            $response->content()
        );
    }

    // -------------------------------------------------------------------------
    // Ruta sin ninguna opción → solo <loc>
    // -------------------------------------------------------------------------

    public function test_sitemap_with_no_options_renders_only_loc()
    {
        Route::get('/minimal', fn() => 'ok')
            ->name('minimal')
            ->sitemap();

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <url><loc>http://localhost/minimal</loc></url>
            </urlset>',
            $response->content()
        );
    }

    // -------------------------------------------------------------------------
    // Priority = 0 (borde inferior) debe renderizarse
    // -------------------------------------------------------------------------

    public function test_priority_zero_renders_in_xml()
    {
        Route::get('/low', fn() => 'ok')
            ->name('low')
            ->sitemap(['priority' => 0]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('<priority>0.0</priority>', $response->content());
    }

    // -------------------------------------------------------------------------
    // Priority = 1 (borde superior)
    // -------------------------------------------------------------------------

    public function test_priority_one_renders_in_xml()
    {
        Route::get('/top', fn() => 'ok')
            ->name('top')
            ->sitemap(['priority' => 1]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('<priority>1.0</priority>', $response->content());
    }

    // -------------------------------------------------------------------------
    // Rutas mezcladas: solo las que tienen .sitemap() aparecen
    // -------------------------------------------------------------------------

    public function test_routes_without_sitemap_are_excluded()
    {
        Route::get('/included', fn() => 'ok')->name('included')->sitemap();
        Route::get('/excluded', fn() => 'ok')->name('excluded');

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('/included', $response->content());
        $this->assertStringNotContainsString('/excluded', $response->content());
    }

    // -------------------------------------------------------------------------
    // Rutas sin nombre reciben nombre temporal y aparecen igual
    // -------------------------------------------------------------------------

    public function test_unnamed_route_appears_in_sitemap()
    {
        Route::get('/unnamed-page', fn() => 'ok')
            ->sitemap(['priority' => 0.5]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('/unnamed-page', $response->content());
    }

    // -------------------------------------------------------------------------
    // Múltiples rutas se ordenan por priority descendente
    // -------------------------------------------------------------------------

    public function test_routes_are_sorted_by_priority_descending()
    {
        Route::get('/low',    fn() => 'ok')->name('low')->sitemap(['priority' => 0.2]);
        Route::get('/high',   fn() => 'ok')->name('high')->sitemap(['priority' => 0.9]);
        Route::get('/medium', fn() => 'ok')->name('medium')->sitemap(['priority' => 0.5]);

        $response = $this->get('/sitemap.xml');
        $xml = $response->content();

        $posHigh   = strpos($xml, '/high');
        $posMedium = strpos($xml, '/medium');
        $posLow    = strpos($xml, '/low');

        $this->assertLessThan($posMedium, $posHigh);
        $this->assertLessThan($posLow, $posMedium);
    }

    // -------------------------------------------------------------------------
    // last_update como DateTimeInterface (no como string)
    // -------------------------------------------------------------------------

    public function test_last_update_as_datetime_object()
    {
        $date = new \DateTime('2024-06-15 12:00:00', new \DateTimeZone('UTC'));

        Route::get('/dt', fn() => 'ok')
            ->name('dt')
            ->sitemap(['last_update' => $date]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('<lastmod>2024-06-15T12:00:00+00:00</lastmod>', $response->content());
    }

    // -------------------------------------------------------------------------
    // Parámetro con caracteres especiales → URL encoding correcto
    // -------------------------------------------------------------------------

    public function test_url_encoding_of_special_characters_in_parameter()
    {
        Route::get('/category/{slug}', fn($slug) => $slug)
            ->name('category')
            ->sitemap(['parameters' => ['slug' => 'café & more']]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('caf%C3%A9%20%26%20more', $response->content());
    }

    // -------------------------------------------------------------------------
    // Múltiples parámetros en la ruta
    // -------------------------------------------------------------------------

    public function test_route_with_multiple_parameters()
    {
        Route::get('/shop/{category}/{product}', fn($c, $p) => "$c/$p")
            ->name('product')
            ->sitemap([
                'priority'   => 0.8,
                'parameters' => ['category' => 'books', 'product' => 'laravel-guide'],
            ]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('/shop/books/laravel-guide', $response->content());
    }

    // -------------------------------------------------------------------------
    // Dos rutas con la misma priority no crashean y ambas aparecen
    // -------------------------------------------------------------------------

    public function test_routes_with_equal_priority_both_appear()
    {
        Route::get('/a', fn() => 'ok')->name('a')->sitemap(['priority' => 0.5]);
        Route::get('/b', fn() => 'ok')->name('b')->sitemap(['priority' => 0.5]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('/a', $response->content());
        $this->assertStringContainsString('/b', $response->content());
    }

    // -------------------------------------------------------------------------
    // Sin priority ni frequency → no se renderizan esas etiquetas
    // -------------------------------------------------------------------------

    public function test_missing_optional_fields_are_not_rendered()
    {
        Route::get('/clean', fn() => 'ok')
            ->name('clean')
            ->sitemap(['last_update' => '2024-01-01']);

        $content = $this->get('/sitemap.xml')->content();

        $this->assertStringNotContainsString('<priority>', $content);
        $this->assertStringNotContainsString('<changefreq>', $content);
        $this->assertStringContainsString('<lastmod>', $content);
    }

    // -------------------------------------------------------------------------
    // Ruta con todos los campos completos
    // -------------------------------------------------------------------------

    public function test_route_with_all_fields_complete()
    {
        Route::get('/full', fn() => 'ok')
            ->name('full')
            ->sitemap([
                'priority'    => 0.6,
                'frequency'   => 'hourly',
                'last_update' => '2025-01-15 08:30:00',
            ]);

        $content = $this->get('/sitemap.xml')->content();

        $this->assertStringContainsString('<loc>http://localhost/full</loc>', $content);
        $this->assertStringContainsString('<lastmod>2025-01-15T08:30:00+00:00</lastmod>', $content);
        $this->assertStringContainsString('<changefreq>hourly</changefreq>', $content);
        $this->assertStringContainsString('<priority>0.6</priority>', $content);
    }

    // -------------------------------------------------------------------------
    // Validación: array de parámetros con todos los tipos escalares válidos
    // -------------------------------------------------------------------------

    public function test_parameters_accept_all_scalar_types()
    {
        Route::get('/types/{a}/{b}/{c}', fn($a, $b, $c) => "$a/$b/$c")
            ->name('types')
            ->sitemap([
                'parameters' => [
                    'a' => 'string',
                    'b' => 42,
                    'c' => 3.14,
                ],
            ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('/types/string/42/3.14', $response->content());
    }

    // -------------------------------------------------------------------------
    // Validación: array de parámetros con valor no escalar lanza excepción
    // -------------------------------------------------------------------------

    public function test_parameters_with_nested_array_throws_exception()
    {
        $this->expectException(SitemapException::class);

        Route::get('/bad', fn() => 'ok')
            ->name('bad')
            ->sitemap(['parameters' => ['key' => ['nested' => 'array']]]);
    }

    // -------------------------------------------------------------------------
    // Ruta del propio /sitemap.xml no aparece en el sitemap
    // -------------------------------------------------------------------------

    public function test_sitemap_route_itself_does_not_appear_in_output()
    {
        Route::get('/page', fn() => 'ok')->name('page')->sitemap(['priority' => 0.5]);

        $content = $this->get('/sitemap.xml')->content();

        $this->assertStringNotContainsString('/sitemap.xml', $content);
    }
}
