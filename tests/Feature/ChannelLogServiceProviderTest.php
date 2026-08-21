<?php

use Illuminate\Support\ServiceProvider;
use Laravel\ChannelLog\Services\Writer;
use Laravel\ChannelLog\ChannelLogServiceProvider;

it('registers the writer as a singleton bound to ChannelLog', function () {
    expect($this->app->make('ChannelLog'))
        ->toBeInstanceOf(Writer::class)
        ->and($this->app->make('ChannelLog'))->toBe($this->app->make('ChannelLog'));
});

it('publishes the default config file', function () {
    $publishable = ServiceProvider::pathsToPublish(ChannelLogServiceProvider::class, 'config');
    $configSource = realpath(__DIR__ . '/../../src/Config/default.php');

    expect($publishable)->toHaveKey($configSource)
        ->and($publishable[$configSource])->toBe(config_path('laravel-logger.php'));
});
