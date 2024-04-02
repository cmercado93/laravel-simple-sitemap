<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

use Cmercado93\LaravelSimpleSitemap\Common\Frequency;
use Cmercado93\LaravelSimpleSitemap\Common\SitemapException;
use Cmercado93\LaravelSimpleSitemap\Common\Validations;
use DateTime;
use DateTimeInterface;

class Mixin
{

    /**
     * mixin para agregar información de sitemap a una ruta.
     *
     * Este mixin permite adjuntar datos específicos del sitemap a una ruta.
     *
     * @param array $data Datos del sitemap que incluyen prioridad, frecuencia, parámetros y última actualización.
     *                   - 'priority': Prioridad de la página (opcional, número entre 0 y 1).
     *                   - 'frequency': Frecuencia de cambio de la página (opcional, valores válidos: 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never').
     *                   - 'parameters': Parámetros adicionales de la ruta (opcional, array no vacío).
     *                   - 'last_update': Fecha y hora de la última actualización (opcional, instancia de \DateTimeInterface o cadena válida en formato 'Y-m-d' o 'Y-m-d H:i:s').
     *
     * @return \Illuminate\Routing\Route
     *
     * @throws Cmercado93\LaravelSimpleSitemap\Common\SitemapException
     */
    public function sitemap()
    {
        return function (array $data = []) {

            Validations::validate($data);

            $data = [
                'priority' => $data['priority'] ?? null,
                'frequency' => $data['frequency'] ?? null,
                'parameters' => $data['parameters'] ?? null,
                'last_update' => $data['last_update'] ?? null,
            ];

            return $this->setAction(array_merge($this->action, $this->parseAction([
                'sitemap' => $data,
                'uses' => $this->action['uses'],
            ])));
        };
    }
}
