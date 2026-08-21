<?php

use Laravel\ChannelLog\ChannelLog;
use Laravel\ChannelLog\Services\Writer;

it('exposes ChannelLog as its facade accessor', function () {
    $accessor = (new ReflectionMethod(ChannelLog::class, 'getFacadeAccessor'))->invoke(null);

    expect($accessor)->toBe('ChannelLog');
});

it('resolves the underlying writer through the facade', function () {
    expect(ChannelLog::getFacadeRoot())->toBeInstanceOf(Writer::class);
});
