<?php

use Monolog\Level;
use Monolog\LogRecord;
use Laravel\ChannelLog\Services\StreamHandler;

function makeLogRecord(string $channel, Level $level): LogRecord
{
    return new LogRecord(new DateTimeImmutable(), $channel, $level, 'a message');
}

it('handles a record matching its channel and a sufficient level', function () {
    $handler = new StreamHandler('event', 'php://memory', Level::Info);

    expect($handler->isHandling(makeLogRecord('event', Level::Warning)))->toBeTrue();
});

it('does not handle a record with a lower level than configured', function () {
    $handler = new StreamHandler('event', 'php://memory', Level::Warning);

    expect($handler->isHandling(makeLogRecord('event', Level::Info)))->toBeFalse();
});

it('does not handle a record from a different channel', function () {
    $handler = new StreamHandler('event', 'php://memory', Level::Debug);

    expect($handler->isHandling(makeLogRecord('other', Level::Debug)))->toBeFalse();
});
