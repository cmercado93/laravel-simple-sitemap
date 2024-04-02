<?php

namespace Cmercado93\LaravelSimpleSitemap\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\RouteCollection;
use Cmercado93\LaravelSimpleSitemap\Common\Url;
use Cmercado93\LaravelSimpleSitemap\Common\SiteMap;

class SiteMapController
{
    /**
     * Inicializa la generación y entrega de un sitemap XML para motores de búsqueda.
     *
     * Este método obtiene las rutas disponibles, construye un sitemap a partir de ellas y
     * responde con el contenido XML resultante. En el caso de que no haya rutas disponibles,
     * se devuelve un sitemap vacío con una respuesta HTTP 200, indicando a los motores de
     * búsqueda que se ha considerado la estructura del sitemap.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function init(Request $request)
    {
        $urls = $this->getRoutes($request);
        $siteMapXml = $this->makeSiteMap($urls);

        return new Response($siteMapXml ?? "", 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * Construye un archivo de sitemap a partir de un conjunto de URLs con información adicional.
     *
     * Este método crea un sitemap ordenando las URLs proporcionadas según su prioridad y
     * construyendo cada entrada con detalles como frecuencia, prioridad y última actualización.
     * El sitemap resultante se devuelve como una cadena de texto XML.
     *
     * @param array $urls
     *
     * @return string|null
     */
    protected function makeSiteMap(array $urls) : ?string
    {
        $sitemap = new SiteMap();

        $urls = collect($urls)->sortByDesc('priority');

        if ($urls->isEmpty()) {
            return null;
        }

        // creamos cada linea del sitemap
        foreach ($urls as $url) {
            $sitemapUrl = Url::create($url['url'])
                ->frequency($url['frequency'] ?? null)
                ->priority($url['priority'] ?? null)
                ->lastUpdate($url['last_update'] ? now()->parse($url['last_update']) : null);

            $sitemap->add($sitemapUrl);
        }

        return $sitemap->build();
    }

    /**
     * Obtiene las rutas del sistema junto con información relevante para la generación de un sitemap.
     *
     * Este método recorre todas las rutas preparadas y extrae información necesaria para la generación
     * de un sitemap, como la URL de la ruta, la frecuencia, la prioridad y la última actualización.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getRoutes(Request $request) : array
    {
        $routes = $this->prepareRoutes();
        $urlGenerator = new UrlGenerator($routes, $request);

        $sitemapRoutes = [];

        foreach ($routes as $route) {
            // Obtén la acción de la ruta
            $action = $route->getAction('sitemap');

            // Accede a los valores de la acción sitemap
            $url = $urlGenerator->route($route->getName(), $action['parameters'] ?? []);

            $sitemapRoutes[] = [
                'url' => $this->urlencode($url),
                'frequency' => $action['frequency'] ?? null,
                'priority' => $action['priority'] ?? null,
                'last_update' => $action['last_update'] ?? null,
            ];
        }

        return $sitemapRoutes;
    }

    /**
     * Codificamos la URL escapando los caracteres especiales
     *
     * @param  string
     * @return string
     */
    protected function urlencode(string $url) : string
    {
        $url = parse_url($url);

        $str = ($url['schema'] ?? 'http://') . $url['host'];

        if (isset($url['port'])) {
            $str .= ':' . $url['port'];
        }

        if (isset($url['path'])) {
            $str .= htmlentities($url['path']);
        } else {
            $str .= '/';
        }

        if (isset($url['query'])) {
            $str = trim($str, '/');
            $str .= '?' . htmlentities($url['query']);
        }

        return $str;
    }

    /**
     * Prepara las rutas existentes asignándoles nombres en caso de no tener uno.
     *
     * Este método asegura que todas las rutas tengan un nombre, ya sea utilizando su
     * nombre existente o generando uno único si no se ha establecido. Los nombres
     * generados siguen el patrón 'sitemap-tmp-X' donde X es un valor
     * numérico que se incrementa de forma única.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function prepareRoutes() : RouteCollection
    {
        $routes = Route::getRoutes();
        $newRoutes = new RouteCollection;

        foreach ($routes as $key => $route) {
            if (!$route->getAction('sitemap')) {
                continue;
            }

            if (!$route->getName()) {
                $route->name("sitemap-tmp-" . $key);
            }

            $newRoutes->add($route);
        }

        return $newRoutes;
    }
}