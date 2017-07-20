<?php

namespace Laravel\ChannelLog;

use Illuminate\Support\Facades\Facade;

class ChannelLog extends Facade
{
    /**
     * Get the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ChannelLog';
    }
}
