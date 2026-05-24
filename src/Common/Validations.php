<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

use Cmercado93\LaravelSimpleSitemap\Common\Frequency;
use Cmercado93\LaravelSimpleSitemap\Common\SitemapException;
use DateTime;
use DateTimeInterface;

class Validations
{
    public static function validate(array $data)
    {
        if (isset($data['priority']) && !is_null($data['priority'])) {
            static::validatePriority($data['priority']);
        }

        if (isset($data['frequency']) && !is_null($data['frequency'])) {
            static::validateFrequency($data['frequency']);
        }

        if (isset($data['parameters']) && !is_null($data['parameters'])) {
            static::validateParameters($data['parameters']);
        }

        if (isset($data['last_update']) && !is_null($data['last_update'])) {
            static::validateLastUpdate($data['last_update']);
        }
    }

    public static function validatePriority($priority)
    {
        if (!is_numeric($priority) || $priority < 0 || $priority > 1) {
            throw new SitemapException("Sitemap: La prioridad debe ser un número entre 0 y 1");
        }
    }

    public static function validateFrequency($frequency)
    {
        if (!is_string($frequency) || !in_array($frequency, Frequency::all())) {
            throw new SitemapException("Sitemap: La frecuencia no es válida");
        }
    }

    public static function validateParameters($parameters)
    {
        if (!is_array($parameters) || !count($parameters) || array_is_list($parameters)) {
            throw new SitemapException("Sitemap: Los parámetros deben ser un array valido");
        }

        foreach ($parameters as $value) {
            if (!is_scalar($value)) {
                throw new SitemapException("Sitemap: Los parámetros deben ser un array valido");
            }
        }
    }

    public static function validateLastUpdate($lastUpdate)
    {
        if (is_string($lastUpdate)) {
            if ($dateTime = DateTime::createFromFormat('Y-m-d', $lastUpdate)) {
                if ($dateTime->format('Y-m-d') == $lastUpdate) {
                    $lastUpdate = $dateTime;
                }
            } elseif ($dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $lastUpdate)) {
                if ($dateTime->format('Y-m-d H:i:s') == $lastUpdate) {
                    $lastUpdate = $dateTime;
                }
            } else {
                $lastUpdate = null;
            }
        }

        if (!($lastUpdate instanceof DateTimeInterface)) {
            throw new SitemapException("Sitemap: La fecha de última actualización no es válida");
        }

        // PHP parsea fechas como '0000-00-00' sin error; este chequeo las descarta.
        if ($lastUpdate->format('U') <= 0) {
            throw new SitemapException("Sitemap: La fecha de última actualización no es válida");
        }
    }
}
