<?php

namespace Laravel\ChannelLog\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Laravel\ChannelLog\ChannelLogServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ChannelLogServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('laravel-logger.channels', [
            'event' => [
                'path' => 'logs/laravel-event.log',
                'level' => \Monolog\Level::Info,
            ],
        ]);
    }
}
