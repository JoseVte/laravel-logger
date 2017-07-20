<?php

namespace Laravel\ChannelLog\Services;

use Config;
use Monolog\Logger;
use InvalidArgumentException;

class Writer
{
    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug' => Logger::DEBUG,
        'info' => Logger::INFO,
        'notice' => Logger::NOTICE,
        'warning' => Logger::WARNING,
        'error' => Logger::ERROR,
        'critical' => Logger::CRITICAL,
        'alert' => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    /**
     * The Log channels.
     *
     * @var array
     */
    private $channels;

    /**
     * Writer constructor.
     */
    public function __construct()
    {
        $this->channels = Config::get('laravel-logger.channels');
    }

    /**
     * Write to log based on the given channel and log level set.
     *
     * @param string $channel
     * @param Logger $level
     * @param mixed  $message
     * @param array  $context
     */
    public function writeLog($channel, $level, $message, array $context = [])
    {
        //check channel exist
        if (!in_array($channel, array_keys($this->channels))) {
            throw new InvalidArgumentException('Invalid channel used.');
        }

        //lazy load logger
        if (!isset($this->channels[$channel]['_instance'])) {
            //create instance
            $this->channels[$channel]['_instance'] = new Logger($channel);
            //add custom handler
            $this->channels[$channel]['_instance']->pushHandler(
                new StreamHandler(
                    $channel,
                    storage_path().'/'.$this->channels[$channel]['path'],
                    $this->channels[$channel]['level']
                )
            );
        }

        //write out record
        $this->channels[$channel]['_instance']->{$level}($message, $context);
    }

    /**
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function write($channel, $message, array $context = [])
    {
        //check channel exist
        if (!in_array($channel, array_keys($this->channels))) {
            throw new InvalidArgumentException('Invalid channel used.');
        }

        //get method name for the associated level
        $level = array_flip($this->levels)[$this->channels[$channel]['level']];
        //write to log
        $this->writeLog($channel, $level, $message, $context);
    }

    /**
     * alert('event','Message');.
     *
     * @param Logger $func
     * @param array  $params
     */
    public function __call($func, $params)
    {
        if (in_array($func, array_keys($this->levels))) {
            $this->writeLog($params[0], $func, $params[1]);
        }
    }
}
