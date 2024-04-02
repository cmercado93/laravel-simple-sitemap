<?php

namespace Cmercado93\LaravelSimpleSitemap\Tests\Unit\Common;

use Cmercado93\LaravelSimpleSitemap\Common\SitemapException;
use Cmercado93\LaravelSimpleSitemap\Common\Validations;
use Cmercado93\LaravelSimpleSitemap\Tests\TestCase;

class ValidationsTest extends TestCase
{

    public static function get_invalid_data_for_test()
    {
        return [
            [-0.1, 'siempre', [1], ''],
            [2, '', 1, 'x'],
        ];
    }

    public static function get_valid_data_for_test()
    {
        return [
            [null, null, null, null],
            [0, null, null, null],
            [0.2, 'always', null, null],
            [0.4, 'never', ['param-1' => 'value'], null],
            [0.9, 'weekly', ['param-1' => 'value'], '2024-01-01 20:05:01'],
            [1, 'monthly', ['param-1' => 'value'], '2024-01-01'],
            [1, 'monthly', ['param-1' => 'value'], new \DateTime],
        ];
    }

    public static function get_invalid_data_for_priority_test()
    {
        return [
            [null],
            [''],
            ['a'],
            [-0.1],
            [1.1],
        ];
    }

    public static function get_valid_data_for_priority_test()
    {
        return [
            [0],
            [0.5],
            [1],
        ];
    }

    public static function get_invalid_data_for_frequency_test()
    {
        return [
            [null],
            [''],
            ['a'],
        ];
    }

    public static function get_valid_data_for_frequency_test()
    {
        return [
            ['always'],
            ['hourly'],
            ['daily'],
            ['weekly'],
            ['monthly'],
            ['yearly'],
            ['never'],
        ];
    }

    public static function get_invalid_data_for_parameters_test()
    {
        return [
            [null],
            [1],
            [""],
            [
                [1]
            ],
        ];
    }

    public static function get_valid_data_for_parameters_test()
    {
        return [
            [
                ['param-1' => 'value'],
            ],
        ];
    }

    public static function get_invalid_data_for_lastUpdate_test()
    {
        return [
            [null],
            [1],
            ['string'],
            [new \StdClass],
            ['2000-00-00'],
            ['2000-01-01 12:05:62'],
        ];
    }

    public static function get_valid_data_for_lastUpdate_test()
    {
        return [
            ['2000-01-01'],
            ['2000-01-01 12:45:52'],
            [new \DateTime()],
        ];
    }
    /**
     * @dataProvider get_valid_data_for_test
     */
    public function test_0($priority, $frequency, $parameters, $lastUpdate)
    {
        $data = [
            'priority' => $priority,
            'frequency' => $frequency,
            'parameters' => $parameters,
            'last_update' => $lastUpdate,
        ];

        $result = Validations::validate($data);

        $this->assertNull($result);
    }

    /**
     * @dataProvider get_invalid_data_for_test
     */
    public function test_1($priority, $frequency, $parameters, $lastUpdate)
    {
        $data = [
            'priority' => $priority,
            'frequency' => $frequency,
            'parameters' => $parameters,
            'last_update' => $lastUpdate,
        ];

        $this->expectException(SitemapException::class);

        Validations::validate($data);
    }

    /**
     * @dataProvider get_invalid_data_for_priority_test
     */
    public function test_priority_0($priority)
    {
        $this->expectException(SitemapException::class);
        $this->expectExceptionMessage('Sitemap: La prioridad debe ser un número entre 0 y 1');

        Validations::validatePriority($priority);
    }

    /**
     * @dataProvider get_valid_data_for_priority_test
     */
    public function test_priority_1($priority)
    {
        Validations::validatePriority($priority);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider get_invalid_data_for_frequency_test
     */
    public function test_frequency_0($frequency)
    {
        $this->expectException(SitemapException::class);
        $this->expectExceptionMessage('Sitemap: La frecuencia no es válida');

        Validations::validateFrequency($frequency);
    }

    /**
     * @dataProvider get_valid_data_for_frequency_test
     */
    public function test_frequency_1($frequency)
    {
        Validations::validateFrequency($frequency);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider get_invalid_data_for_parameters_test
     */
    public function test_parameters_0($parameters)
    {
        $this->expectException(SitemapException::class);
        $this->expectExceptionMessage('Sitemap: Los parámetros deben ser un array valido');

        Validations::validateParameters($parameters);
    }

    /**
     * @dataProvider get_valid_data_for_parameters_test
     */
    public function test_parameters_1($parameters)
    {
        Validations::validateParameters($parameters);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider get_invalid_data_for_lastUpdate_test
     */
    public function test_lastUpdate_0($lastUpdate)
    {
        $this->expectException(SitemapException::class);
        $this->expectExceptionMessage('La fecha de última actualización no es válida');

        Validations::validateLastUpdate($lastUpdate);
    }

    /**
     * @dataProvider get_valid_data_for_lastUpdate_test
     */
    public function test_lastUpdate_1($lastUpdate)
    {
        Validations::validateLastUpdate($lastUpdate);

        $this->assertTrue(true);
    }
}
