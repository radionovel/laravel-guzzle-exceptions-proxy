<?php
declare(strict_types=1);


namespace ExceptionsProxy\Tests;

use ExceptionsProxy\ServiceProvider;
use ExceptionsProxy\Tests\Exceptions\GuzzleProxyException;
use ExceptionsProxy\Tests\Exceptions\MutedException;
use ExceptionsProxy\Tests\Exceptions\ProxyException;
use ExceptionsProxy\Tests\Exceptions\WrapException;
use ExceptionsProxy\Tests\Exceptions\WrappedException;
use Orchestra\Testbench\TestCase as LaravelTestCase;

abstract class TestCase extends LaravelTestCase
{
    protected array $setupConfig = [
        'exceptions' => [
            'proxy' => [
//                ProxyException::class
            ],

            'dont_report' => [
                MutedException::class,
            ],

            'wrapper' => [
                WrapException::class => WrappedException::class,
                ProxyException::class => GuzzleProxyException::class
            ],
        ],
    ];

    /**
     * @param $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        foreach ($this->setupConfig as $key => $value) {
            $app['config']->set($key, $value);
        }
    }

    /**
     * @param $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * @param  array  $config
     *
     * @return void
     */
    protected function resetApplicationWithConfig(array $config): void
    {
        $this->setupConfig = $config;

        $this->refreshApplication();
    }
}
