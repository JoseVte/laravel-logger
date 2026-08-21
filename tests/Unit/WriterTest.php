<?php

use Monolog\Level;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Laravel\ChannelLog\Services\Writer;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

function configureChannel(string $channel, Level $level = Level::Debug, array $extra = []): string
{
    $path = 'logs/' . $channel . '.log';

    Config::set("laravel-logger.channels.{$channel}", array_merge([
        'path' => $path,
        'level' => $level,
    ], $extra));

    File::delete(storage_path($path));

    return storage_path($path);
}

afterEach(function () {
    collect(File::glob(storage_path('logs/*-channel.log')))->each(fn (string $file) => File::delete($file));
});

it('throws when writing to an unconfigured channel via writeLog', function () {
    $writer = new Writer();

    expect(fn () => $writer->writeLog('missing-channel', 'info', 'a message'))
        ->toThrow(InvalidArgumentException::class, 'Invalid channel used.');
});

it('throws when writing to an unconfigured channel via write', function () {
    $writer = new Writer();

    expect(fn () => $writer->write('missing-channel', 'a message'))
        ->toThrow(InvalidArgumentException::class, 'Invalid channel used.');
});

it('writes the message to the channel configured log file', function () {
    $logFile = configureChannel('writelog-channel');
    $writer = new Writer();

    $writer->writeLog('writelog-channel', 'info', 'hello world');

    expect(File::get($logFile))
        ->toContain('hello world')
        ->toContain('INFO');
});

it('resolves the level configured for the channel when using write', function () {
    $logFile = configureChannel('write-channel', Level::Warning);
    $writer = new Writer();

    $writer->write('write-channel', 'careful now');

    expect(File::get($logFile))
        ->toContain('careful now')
        ->toContain('WARNING');
});

it('proxies dynamic level methods to writeLog', function () {
    $logFile = configureChannel('call-channel');
    $writer = new Writer();

    $writer->error('call-channel', 'boom');

    expect(File::get($logFile))
        ->toContain('boom')
        ->toContain('ERROR');
});

it('ignores unknown dynamic methods', function () {
    configureChannel('call-channel');
    $writer = new Writer();

    expect(fn () => $writer->notALogLevel('call-channel', 'ignored'))->not->toThrow(Throwable::class);
});

it('formats array messages with var_export', function () {
    $logFile = configureChannel('array-channel');
    $writer = new Writer();

    $writer->writeLog('array-channel', 'info', ['foo' => 'bar']);

    expect(File::get($logFile))->toContain("'foo' => 'bar'");
});

it('formats Jsonable messages as json', function () {
    $logFile = configureChannel('jsonable-channel');
    $writer = new Writer();

    $message = new class () implements Jsonable {
        public function toJson($options = 0): string
        {
            return '{"foo":"bar"}';
        }
    };

    $writer->writeLog('jsonable-channel', 'info', $message);

    expect(File::get($logFile))->toContain('{"foo":"bar"}');
});

it('formats Arrayable messages with var_export', function () {
    $logFile = configureChannel('arrayable-channel');
    $writer = new Writer();

    $message = new class () implements Arrayable {
        public function toArray(): array
        {
            return ['foo' => 'bar'];
        }
    };

    $writer->writeLog('arrayable-channel', 'info', $message);

    expect(File::get($logFile))->toContain("'foo' => 'bar'");
});
