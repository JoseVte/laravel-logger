<?php

namespace Laravel\ChannelLog\Services;

use Monolog\Logger;

class StreamHandler extends \Monolog\Handler\StreamHandler
{
    /**
     * Channel name.
     *
     * @var string
     */
    protected $channel;

    /**
     * @param string   $channel Channel name to write
     * @param bool|int $stream
     * @param bool|int $level
     * @param bool     $bubble
     * @param null     $filePermission
     * @param bool     $useLocking
     *
     * @see parent __construct for params
     */
    public function __construct($channel, $stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false)
    {
        $this->channel = $channel;

        parent::__construct($stream, $level, $bubble);
    }

    /**
     * When to handle the log record.
     *
     * @param array $record
     *
     * @return bool
     */
    public function isHandling(array $record)
    {
        //Handle if Level high enough to be handled (default mechanism)
        //AND CHANNELS MATCHING!
        if (isset($record['channel'])) {
            return $record['level'] >= $this->level && $record['channel'] == $this->channel;
        } else {
            return $record['level'] >= $this->level;
        }
    }
}
