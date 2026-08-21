# ChannelLog

ChannelLog lets you log to separate files per channel, each with its own minimum level, so you are not stuck with a single catch-all log.

<p align="center">
<a href="https://packagist.org/packages/josrom/laravel-logger"><img src="https://poser.pugx.org/josrom/laravel-logger/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/josrom/laravel-logger"><img src="https://poser.pugx.org/josrom/laravel-logger/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/josrom/laravel-logger"><img src="https://poser.pugx.org/josrom/laravel-logger/license.svg" alt="License"></a>
<a href="https://github.com/josrom/laravel-logger/actions/workflows/run-tests.yml"><img src="https://github.com/josrom/laravel-logger/actions/workflows/run-tests.yml/badge.svg" alt="Tests"></a>
<a href="https://github.com/josrom/laravel-logger/actions/workflows/php-cs-fixer.yml"><img src="https://github.com/josrom/laravel-logger/actions/workflows/php-cs-fixer.yml/badge.svg" alt="Code Style"></a>
</p>

## Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Extras](#extras)
* [Testing](#testing)
* [Supported versions](#supported-versions)

## Requirements

* PHP 8.3 or higher
* Laravel 13.x

For older Laravel versions, check the [supported versions](#supported-versions) table below.

## Installation

Require the package with [Composer](https://getcomposer.org/):

```bash
composer require josrom/laravel-logger
```

Register the service provider and the facade alias in your `config/app.php` file:

```php
'providers' => [
    // Other Service Providers

    Laravel\ChannelLog\ChannelLogServiceProvider::class,
],

'aliases' => [
    // Other Aliases

    'ChannelLog' => Laravel\ChannelLog\ChannelLog::class,
],
```

Then publish the config file:

```bash
php artisan vendor:publish --provider="Laravel\ChannelLog\ChannelLogServiceProvider"
```

This creates `config/laravel-logger.php`.

## Configuration

Each key under `channels` defines a log channel: where it writes to, and its minimum level.

```php
// config/laravel-logger.php

return [
    'channels' => [
        'event' => [
            'path' => 'logs/laravel-event.log',
            'level' => \Monolog\Level::Info,
        ],
        'error' => [
            'path' => 'logs/laravel-error.log',
            'level' => \Monolog\Level::Error,
        ],
        'info' => [
            'path' => 'logs/laravel-info.log',
            'level' => \Monolog\Level::Info,
            'extras' => ['internet-provider'],
        ],
    ],
];
```

* `path` is relative to the `storage` directory.
* `level` accepts any `Monolog\Level` case (`Debug`, `Info`, `Notice`, `Warning`, `Error`, `Critical`, `Alert`, `Emergency`).
* `extras` is optional, see [Extras](#extras).

## Usage

Use the `ChannelLog` facade to write to a specific channel. The method name sets the log level:

```php
use ChannelLog;

ChannelLog::info('event', 'A user logged in.');
ChannelLog::error('error', 'Something went wrong.', ['user_id' => $user->id]);
```

To write at the level already configured for a channel, use `write()`:

```php
ChannelLog::write('event', 'A user logged in.');
```

Arrays, and anything implementing `Illuminate\Contracts\Support\Arrayable` or `Illuminate\Contracts\Support\Jsonable` (such as Eloquent models and collections), are formatted automatically before being written:

```php
ChannelLog::info('event', $user);
ChannelLog::info('event', ['user_id' => $user->id, 'action' => 'login']);
```

## Extras

Extras add extra context to a channel's log entries.

* `internet-provider`: looks up the requesting IP's internet provider through [ipinfo.io](https://ipinfo.io) and appends it as an additional log line.

## Testing

The package uses [Pest](https://pestphp.com/) with [Orchestra Testbench](https://github.com/orchestral/testbench).

```bash
composer test
```

Check the code style with [PHP CS Fixer](https://cs.symfony.com/):

```bash
composer format
```

Both run automatically on every push and pull request through GitHub Actions.

## Supported versions

| Package version       | Laravel version       |
|------------------------|------------------------|
| <strong>13.*</strong> | <strong>13.*</strong> |
| 12.*                  | 12.*                   |
| 11.*                  | 11.*                   |
| 10.*                  | 10.*                   |
| 9.*                   | 9.*                    |
| 8.*                   | 8.*                    |
| 7.*                   | 7.*                    |
| 6.*                   | 6.*                    |
| 0.1.* / 5.*           | 5.*                    |
