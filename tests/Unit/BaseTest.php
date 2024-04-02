<?php

namespace Cmercado93\LaravelSimpleSitemap\Tests\Unit;

use Cmercado93\LaravelSimpleSitemap\Common\Frequency;
use Cmercado93\LaravelSimpleSitemap\Common\SitemapException;
use Cmercado93\LaravelSimpleSitemap\Controller\SiteMapController;
use Cmercado93\LaravelSimpleSitemap\SiteMapProvider;
use Cmercado93\LaravelSimpleSitemap\Tests\TestCase;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class BaseTest extends TestCase
{
    public function test_1()
    {
        Route::get('page-1', function () {return 1;})
            ->name('page-1')
            ->sitemap([
                'frequency' => 'weekly',
                'priority' => 0.7,
                'last_update' => '2024-01-01 09:01:01',
            ]);

        $expectedResponse = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>http://localhost/page-1</loc><lastmod>2024-01-01T09:01:01+00:00</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url></urlset>';

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);

        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');

        $this->assertXmlStringEqualsXmlString($response->content(), $expectedResponse);
    }

    public function test_2()
    {
        $this->expectException(SitemapException::class);
        $this->expectExceptionMessage('Sitemap: La frecuencia no es válida');

        Route::get('page-1', function () {return 1;})
            ->name('page-1')
            ->sitemap([
                'frequency' => 'siempre',
                'priority' => 0.7,
                'last_update' => '2024-01-01 09:01:01',
            ]);
    }
}
