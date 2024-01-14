<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

use Cmercado93\LaravelSimpleSitemap\Common\Url;

class SiteMap
{
    /**
     * Etiqueta de inicio del archivo XML del mapa del sitio.
     *
     * @var string
     */
    const START_TAG = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    /**
     * Etiqueta de cierre del archivo XML del mapa del sitio.
     *
     * @var string
     */
    const END_TAG = '</urlset>';

    /**
     * Contenido del mapa del sitio, almacenando representaciones XML de URLs.
     *
     * @var array
     */
    protected $content = [];

    /**
     * Agrega una URL al mapa del sitio.
     *
     * @param Cmercado93\LaravelSimpleSitemap\Common\Url $siteMapUrl
     * @return void
     */
    public function add(Url $siteMapUrl)
    {
        $this->content[] = $siteMapUrl->build();
    }

    /**
     * Construye y devuelve el contenido completo del mapa del sitio en formato XML.
     *
     * @return string
     */
    public function build() : string
    {
        return static::START_TAG . implode("", $this->content) . static::END_TAG;
    }
}