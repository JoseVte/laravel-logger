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
        $this->package('josrom/laravel-logger');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('ChannelLog', 'Laravel\ChannelLog\Services\Writer');
    }

    /**
     * Register the package's component namespaces.
     *
     * @param string $package
     * @param string $namespace
     * @param string $path
     */
    public function package($package, $namespace = null, $path = null)
    {
        // Copy the config file
        $files = $this->app['files'];
        if (!$files->exists(base_path('config/laravel-logger.php'))) {
            $files->copy(__DIR__.'/Config/default.php', base_path('config/laravel-logger.php'));
        }
    }
}
