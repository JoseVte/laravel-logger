## Laravel 5.x ChannelLog

<p align="center">
<a href="https://packagist.org/packages/josrom/laravel-logger"><img src="https://poser.pugx.org/josrom/laravel-logger/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/josrom/laravel-logger"><img src="https://poser.pugx.org/josrom/laravel-logger/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/josrom/laravel-logger"><img src="https://poser.pugx.org/josrom/laravel-logger/license.svg" alt="License"></a>
</p>

### Introduction

ChannelLog provides a feature to log in different files.

### Installation

To get the last version of ChannelLog, simply require the project using [Composer](https://getcomposer.org/):

```bash
composer require josrom/laravel-logger
```

Instead, you may of course manually update your require block and run composer update if you so choose:

```json
{
    "require": {
        "josrom/laravel-logger": "^0.1"
    }
}
```

Add the service provider and alias to `config/app.php` file:

```php
'providers' => [
    // Other Service Providers

    Laravel\ChannelLog\ChannelLogServiceProvider::class,
],

'aliases' => [
    // Other Alias
    
    'ChannelLog' => \Laravel\ChannelLog\ChannelLog::class,
],
```

After to set up the provider, copy the default config file from the package using the `vendor:publish` artisan command:

```bash
php artisan vendor:publish --provider="Laravel\ChannelLog\ChannelLogServiceProvider"
```