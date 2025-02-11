<?php

namespace App\Container;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;

/**
 * Class Logger
 * @package App\Container
 */
class Logger implements HandlerInterface
{

    private static $instance;

    public static function init(): void
    {
        $logger = new \Monolog\Logger('MIX');
        $rotatingFileHandler = new RotatingFileHandler(__DIR__ . '/../../logs/mix.log', 7);
        $rotatingFileHandler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n", 'Y-m-d H:i:s.u'));
        $logger->pushHandler($rotatingFileHandler);
        $logger->pushHandler(new Logger());
        self::$instance = $logger;
    }

    /**
     * @return \Monolog\Logger
     */
    public static function instance(): \Monolog\Logger
    {
        if (!isset(self::$instance)) {
            static::init();
        }
        return self::$instance;
    }

    public function isHandling(array $record): bool
    {
        return $record['level'] >= \Monolog\Logger::DEBUG;
    }

    public function handle(array $record): bool
    {
        $message = sprintf("%s  %s  %s\n", $record['datetime']->format('Y-m-d H:i:s.u'), $record['level_name'], $record['message']);
        switch (PHP_SAPI) {
            case 'cli':
            case 'cli-server':
                file_put_contents("php://stdout", $message);
                break;
        }
        return false;
    }

    public function handleBatch(array $records): void
    {
        // TODO: Implement handleBatch() method.
    }

    public function close(): void
    {
        // TODO: Implement close() method.
    }

}
