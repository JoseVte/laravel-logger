<?php

namespace Laravel\ChannelLog\Services;

use Config;
use Exception;
use Monolog\Logger;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

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
            $logger = new Logger($channel);
            $formatter = new LineFormatter();
            $formatter->includeStacktraces(true);

            $handler = new StreamHandler(
                $channel,
                storage_path().'/'.$this->channels[$channel]['path'],
                $this->channels[$channel]['level']
            );
            $handler->setFormatter($formatter);

            //add custom handler
            $this->channels[$channel]['_instance'] = $logger->pushHandler($handler);
        }

        //write out record
        $message = $this->formatMessage($message);
        $this->channels[$channel]['_instance']->{$level}($message, $context);

        // Extra log functions
        if (array_has($this->channels, $channel.'.extras')) {
            if (in_array('internet-provider', $this->channels[$channel]['extras'])) {
                try {
                    $client = new GuzzleClient();
                    $response = $client->request('GET', 'https://ipinfo.io/'.request()->ip().'/org');
                    $this->channels[$channel]['_instance']->{$level}('Internet provider: '.$response->getBody()->getContents(), $context);
                } catch (Exception $exception) {
                    $this->channels[$channel]['_instance']->{$level}('The internet provider cannot be found', $context);
                }
            }
        }
    }

    /**
     * Write in the log.
     *
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

    /**
     * Format the parameters for the logger.
     *
     * @param  mixed  $message
     * @return mixed
     */
    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        } elseif ($message instanceof Jsonable) {
            return $message->toJson();
        } elseif ($message instanceof Arrayable) {
            return var_export($message->toArray(), true);
        }

        return $message;
    }
}
