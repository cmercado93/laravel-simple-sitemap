<?php

namespace Cmercado93\LaravelSimpleSitemap\Common;

class Url
{
    /**
     * URL de la página.
     *
     * @var string
     */
    protected $url;

    /**
     * Fecha y hora de la última actualización.
     *
     * @var string|null
     */
    protected $lastUpdate;

    /**
     * Frecuencia de cambio de la página.
     *
     * @var string|null
     */
    protected $frequency;

    /**
     * Prioridad de la página.
     *
     * @var string|null
     */
    protected $priority;

    protected function __construct() {}

    /**
     * Crea una nueva instancia de la clase Url.
     *
     * @param string $url
     *
     * @return Cmercado93\LaravelSimpleSitemap\Common\Url
     */
    public static function create(string $url)
    {
        $newNode = new static();

        $newNode->url = $url;

        return $newNode;
    }

    /**
     * Establece la fecha y hora de la última actualización.
     *
     * @param \DateTimeInterface|null $lastUpdate Fecha y hora de la última actualización.
     *
     * @return Cmercado93\LaravelSimpleSitemap\Common\Url
     */
    public function lastUpdate(?\DateTimeInterface $lastUpdate)
    {
        $this->lastUpdate = $lastUpdate ? $lastUpdate->format('c') : null;

        return $this;
    }

    /**
     * Establece la frecuencia de cambio de la página.
     *
     * @param string|null $frequency Frecuencia de cambio de la página.
     *
     * @return Cmercado93\LaravelSimpleSitemap\Common\Url
     */
    public function frequency(?string $frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Establece la prioridad de la página.
     *
     * @param float $priority Prioridad de la página.
     *
     * @return Cmercado93\LaravelSimpleSitemap\Common\Url
     */
    public function priority(?float $priority)
    {
        $this->priority = !is_null($priority) ? number_format($priority, 1, '.', '') : null;

        return $this;
    }

    /**
     * Construye y devuelve la representación en formato XML de la URL con su información adicional.
     *
     * @return string
     */
    public function build() : string
    {
        $str = "<loc>{$this->url}</loc>";

        if ($this->lastUpdate) {
            $str .= "<lastmod>{$this->lastUpdate}</lastmod>";
        }

        if (is_string($this->frequency)) {
            $str .= "<changefreq>{$this->frequency}</changefreq>";
        }

        if ($this->priority) {
            $str .= "<priority>{$this->priority}</priority>";
        }

        return "<url>{$str}</url>";
    }
}