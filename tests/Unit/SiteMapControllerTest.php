<?php

namespace package\tests\Unit;

use Cmercado93\LaravelSimpleSitemap\Common\Frequency;
use Cmercado93\LaravelSimpleSitemap\Controller\SiteMapController;
use Cmercado93\LaravelSimpleSitemap\SiteMapProvider;
use Cmercado93\LaravelSimpleSitemap\Tests\TestCase;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;

class SiteMapControllerTest extends TestCase
{
    protected function invokeProtectedMethod($method, ...$args)
    {
        $siteMapControllerIns = new SiteMapController;

        $reflection = new \ReflectionClass($siteMapControllerIns);

        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($siteMapControllerIns, $args);
    }

    public function test_makeSiteMap_1()
    {
        $expectedResponse = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>http://localhost/page-1</loc><lastmod>2024-01-01T09:01:01+00:00</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url></urlset>';

        $urls = [
            [
                'url' => 'http://localhost/page-1',
                'frequency' => 'weekly',
                'priority' => 0.7,
                'last_update' => '2024-01-01 09:01:01',
            ]
        ];

        $result = $this->invokeProtectedMethod('makeSiteMap', $urls);

        $this->assertXmlStringEqualsXmlString($result, $expectedResponse);
    }

    public function test_makeSiteMap_2()
    {
        $expectedResponse = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>http://localhost/page-1</loc><lastmod>2024-01-01T09:01:01+00:00</lastmod><priority>0.7</priority></url></urlset>';

        $urls = [
            [
                'url' => 'http://localhost/page-1',
                'priority' => 0.7,
                'last_update' => '2024-01-01 09:01:01',
            ]
        ];

        $result = $this->invokeProtectedMethod('makeSiteMap', $urls);

        $this->assertXmlStringEqualsXmlString($result, $expectedResponse);
    }

    public function test_makeSiteMap_3()
    {
        $expectedResponse = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>http://localhost/page-1</loc><lastmod>2024-01-01T09:01:01+00:00</lastmod><priority>0.7</priority></url></urlset>';

        $urls = [
            [
                'priority' => 0.7,
                'last_update' => '2024-01-01 09:01:01',
            ]
        ];

        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Undefined index: url');

        $result = $this->invokeProtectedMethod('makeSiteMap', $urls);

        $this->assertXmlStringEqualsXmlString($result, $expectedResponse);
    }

    public function test_makeSiteMap_4()
    {
        $urls = [];

        $result = $this->invokeProtectedMethod('makeSiteMap', $urls);

        $this->assertNull($result);
    }

    public function test_prepareRoutes_0()
    {
        Route::get('page-1', function () {return 1;})
            ->name('uri-1');

        $result = $this->invokeProtectedMethod('prepareRoutes');

        $this->assertInstanceOf(RouteCollection::class, $result);

        $route = $result->getByName('uri-1');

        $this->assertNull($route);
    }

    public function test_prepareRoutes_1()
    {
        Route::get('page-1', function () {return 1;})
            ->name('uri-1')
            ->sitemap();

        $result = $this->invokeProtectedMethod('prepareRoutes');

        $this->assertInstanceOf(RouteCollection::class, $result);

        $route = $result->getByName('uri-1');

        $this->assertNotNull($route);

        $this->assertIsArray($route->getAction('sitemap'));
    }
}
