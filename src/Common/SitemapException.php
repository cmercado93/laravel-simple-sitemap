<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

use Exception;

class SitemapException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}