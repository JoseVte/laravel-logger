<?php

namespace Laravel\ChannelLog;

use Illuminate\Support\ServiceProvider;

class ChannelLogServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/default.php' => config_path('laravel-logger.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('ChannelLog', 'Laravel\ChannelLog\Services\Writer');
    }
}
