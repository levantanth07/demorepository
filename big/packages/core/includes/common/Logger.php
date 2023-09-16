<?php

class Logger
{

    private static $logger;

    /**
     * Gets the handler.
     *
     * @return     Monolog  The handler.
     */
    public static function getLogger()
    {
        if (!defined('LOG_PATH') || !is_dir(LOG_PATH) || !is_writable(LOG_PATH)) {
            return false;
        }

        if (is_null(self::$logger)) {
            self::$logger =  new Monolog\Logger('tuha-log');
            $formatter = new Monolog\Formatter\LineFormatter(
                null, // Format of message in log, default [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n
                null, // Datetime format
                true, // allowInlineLineBreaks option, default false
                true  // discard empty Square brackets in the end, default false
            );

            $logFilePath = LOG_PATH . '/' . date('Y-m-d') . '.log';
            $streamHandler = new Monolog\Handler\StreamHandler($logFilePath, Monolog\Logger::DEBUG);
            $streamHandler->setFormatter($formatter);

            
            self::$logger->pushHandler($streamHandler);
        }

        return self::$logger;
    }
}
