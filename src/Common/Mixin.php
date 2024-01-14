<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

use DateTime;
use DateTimeInterface;
use Cmercado93\LaravelSimpleSitemap\Common\Frequency;
use Cmercado93\LaravelSimpleSitemap\Common\SitemapException;

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
            if (isset($data['priority'])) {
                if (!is_numeric($data['priority']) || $data['priority'] < 0 || $data['priority'] > 1) {
                    throw new SitemapException("Sitemap: La prioridad debe ser un número entre 0 y 1");
                }
            }

            if (isset($data['frequency'])) {
                if (!is_string($data['frequency']) || !in_array($data['frequency'], Frequency::all())) {
                    throw new SitemapException("Sitemap: La frecuencia no es válida");
                }
            }

            if (isset($data['parameters'])) {
                if (!is_array($data['parameters']) || !count($data['parameters'])) {
                    throw new SitemapException("Sitemap: Los parámetros deben ser un array no vacío");
                }
            }

            if (isset($data['last_update'])) {
                if (!($data['last_update'] instanceof DateTimeInterface) && !(DateTime::createFromFormat('Y-m-d', $data['last_update']) || DateTime::createFromFormat('Y-m-d H:i:s', $data['last_update']))) {
                    throw new SitemapException("Sitemap: La fecha de última actualización no es válida");
                }
            }

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
