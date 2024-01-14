<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

class Frequency
{
    const ALWAYS = 'always';

    const HOURLY = 'hourly';

    const DAILY = 'daily';

    const WEEKLY = 'weekly';

    const MONTHLY = 'monthly';

    const YEARLY = 'yearly';

    const NEVER = 'never';

    /**
     * Retorno todas los tipos de frecuencias
     *
     * @return array
     */
    public static function all()
    {
        return [
            static::ALWAYS,
            static::HOURLY,
            static::DAILY,
            static::WEEKLY,
            static::MONTHLY,
            static::YEARLY,
            static::NEVER,
        ];
    }
}
