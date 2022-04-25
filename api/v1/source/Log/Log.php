<?php

namespace Source\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    use \Source\Service\TraitsController\SessionController;

    /**
     * @var object $log 
     */
    private $log;

    public static function setCritical(string $msg, array $info = [], bool|array $user = false)
    {
        $log = new Logger('api');
        $log->pushHandler(new StreamHandler(__DIR__ . '/api_critical.log', Logger::CRITICAL));
        $log->pushProcessor(function ($record) use ($user) {
            $record['extra']['user'] = $user || NULL;
            $record['extra']['server'] =
                [
                    "userAgent" =>  $_SERVER['HTTP_USER_AGENT'],
                    "url" =>  $_SERVER['REDIRECT_URL'],
                    "REMOTE_ADDR" => $_SERVER['REMOTE_ADDR']
                ];

            $record['extra']['post'] = $_POST;
            $record['extra']['get'] = $_GET;

            return $record;
        });
        $log->critical($msg, $info);
    }

    public static function setDebug(string $msg, array $info = [], ?array $user = null)
    {
        $log = new Logger('api');
        $log->pushHandler(new StreamHandler(__DIR__ . '/api_debug.log', Logger::DEBUG));
        $log->pushProcessor(function ($record) use ($user) {
            $record['extra']['user'] = $user ?? NULL;
            $record['extra']['server'] =
                [
                    "REMOTE_ADDR" =>  $_SERVER['REMOTE_ADDR']
                ];

            return $record;
        });
        $log->debug($msg, $info);
    }

    public static function authorization(string $msg)
    {
        $log = new Logger('api');
        $log->pushHandler(new StreamHandler(__DIR__ . '/api_authorization.log', Logger::INFO));
        $log->pushProcessor(function ($record) {
            $record['extra']['server'] =
                [
                    "REMOTE_ADDR" =>  $_SERVER['REMOTE_ADDR']
                ];
            $record['extra']['credentials'] = $_POST;

            return $record;
        });
        $log->info($msg);
    }


    public static function DB($msg, $info = [])
    {
        $log = new Logger('api');
        $log->pushHandler(new PDOHandler);
        $log->debug($msg, $info);
    }
}
