<?php

namespace Laravel\ChannelLog\Services;

use Monolog\Logger;
use Monolog\LogRecord;

class StreamHandler extends \Monolog\Handler\StreamHandler
{
    /**
     * Channel name.
     *
     * @var string
     */
    protected $channel;

    /**
     * Construct.
     *
     * @param string   $channel        Channel name to write
     * @param bool|int $stream
     * @param bool|int $level
     * @param bool     $bubble
     * @param null     $filePermission
     * @param bool     $useLocking
     *
     * @throws \Exception
     */
    public function __construct($channel, $stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false)
    {
        $this->channel = $channel;

        parent::__construct($stream, $level, $bubble);
    }

    /**
     * When to handle the log record.
     */
    public function isHandling(LogRecord $record): bool
    {
        return $record->level->value >= $this->level->value && $record->channel === $this->channel;
    }
}
