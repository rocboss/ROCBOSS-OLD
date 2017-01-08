<?php

class Roc
{
    private static $engine;

    private function __construct()
    {
    }
    private function __destruct()
    {
    }
    private function __clone()
    {
    }

    public static function __callStatic($name, $params)
    {
        static $initialized = false;

        if (!$initialized) {
            require_once __DIR__ . '/autoload.php';

            self::$engine = new \system\Engine();

            $initialized = true;
        }

        return \system\core\Dispatcher::invokeMethod([self::$engine, $name], $params);
    }

    public static function app()
    {
        return self::$engine;
    }
}
