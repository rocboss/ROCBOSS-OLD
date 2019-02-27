<?php

use Lcobucci\JWT\Builder;
use Elastica\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ElasticSearchHandler;
use aryelgois\Medools\MedooConnection;

/**
 * Batio kernel class
 *
 * @author Roc <i@rocs.me>
 * @link https://github.com/rocboss/batio
 */
class Batio
{
    const VERSION = 'Batio 1.1.0';

    protected static $_log;
    protected static $_es;
    protected static $_redis;
    protected static $_db = [];
    protected static $_cache = [];

    /**
     * Bootstrap
     * @method bootstrap
     * @return void
     */
    public static function bootstrap()
    {
        // Set timezone
        date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Shanghai'));

        // Filters
        if (get_magic_quotes_gpc()) {
            $_GET = self::_stripslashesDeep($_GET);
            $_POST = self::_stripslashesDeep($_POST);
            $_COOKIE = self::_stripslashesDeep($_COOKIE);
        }

        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        /*
        |--------------------------------------------------------------------------
        | Flight registers/maps start
        |--------------------------------------------------------------------------
        | You can register more components in here.
        */

        // Log
        app()->register('log', [__CLASS__, 'log']);

        // DB
        app()->register('db', [__CLASS__, 'db']);

        // ElasticSearch
        app()->register('es', [__CLASS__, 'elasticsearch']);

        // Redis
        app()->register('redis', [__CLASS__, 'redis']);

        // Cache
        app()->register('cache', [__CLASS__, 'cache']);

        // Halt response
        app()->map('halt', [__CLASS__, 'halt']);

        // Handle 404 error
        app()->map('notFound', function () {
            // Record Log.
            app()->log()->error('404 NOT FOUND.', json_decode(json_encode(app()->request()), true));

            return self::halt([
                'code' => 404,
                'msg'  => '404 NOT FOUND.'
            ], 404);
        });

        // Handle 500 error
        app()->map('error', function ($ex) {
            // Record Log.
            app()->log()->error("500 Internal Server Error.\n".$ex->getMessage()."\n".$ex->getTraceAsString());

            $traceArr = explode("\n", $ex->getTraceAsString());
            array_unshift($traceArr, $ex->getMessage());

            return self::halt([
                'code' => 500,
                'msg'  => '500 Internal Server Error.',
                'data' => filter_var(env('DEBUG_MODE', false), FILTER_VALIDATE_BOOLEAN) ? $traceArr : 'Please check the error log.'
            ], 500);
        });

        /*
        |--------------------------------------------------------------------------
        | Flight registers/maps end
        |--------------------------------------------------------------------------
        */

        // Middleware
        new Middleware();

        // Route
        require APP_PATH.'/config/routes.php';
    }

    /**
      * Log
      * @method log
      * @param  string $name
      * @return Object
      */
    public static function log($name = 'system')
    {
        $logDir = app()->get('log.path').'/batio_'.date('Y-m-d').'.log';

        if (!isset(self::$_log)) {
            $log = new Logger($name);
            if (env('ES_HOST')) {
                // Create ElasticSearch Instance.
                $elasticaClient = new Client([
                    'host' => env('ES_HOST'),
                    'port' => env('ES_PORT', 9200),
                    'transport' => ENV('ES_TRANSPORT', 'http'),
                    'username' => env('ES_USER'),
                    'password' => env('ES_PASSWORD')
                ]);
                $elasticaIndex = $elasticaClient->getIndex(env('ES_LOG_INDEX').'-'.date('Ymd'));
                if ($elasticaIndex->exists()) {
                    if ($elasticaIndex->getSettings()->getNumberOfReplicas() != 0) {
                        // reset replicas
                        $elasticaIndex->getSettings()->setNumberOfReplicas(0);
                    }
                } else {
                    $elasticaIndex->create([
                        'number_of_replicas' => 0,
                    ]);
                }

                $log->pushHandler(new ElasticSearchHandler($elasticaClient, [
                    'index' => env('ES_LOG_INDEX').'-'.date('Ymd'),
                    'type' => env('ES_LOG_TYPE'),
                    'ignore_error' => true,
                ], Logger::INFO));
            } else {
                // Create File Stream Instance.
                $log->pushHandler(new StreamHandler($logDir, Logger::INFO));
            }

            self::$_log = $log;
        }

        return self::$_log;
    }

    /**
     * Get elasticSearch instance
     * @method elasticsearch
     * @return Object
     */
    public static function elasticsearch()
    {
        if (!isset(self::$_es)) {
            self::$_es = new ElasticSearch([
                [
                    'host' => env('ES_HOST'),
                    'port' => env('ES_PORT'),
                    'scheme' => ENV('ES_TRANSPORT', 'http'),
                    'user' => env('ES_USER', null),
                    'pass' => env('ES_PASSWORD', null)
                ]
            ]);
        }

        return self::$_es;
    }
    
    /**
     * Redis
     * @method redis
     * @return Object
     */
    public static function redis()
    {
        if (!isset(self::$_redis)) {
            self::$_redis = new \Predis\Client([
                'scheme' => env('REDIS_SCHEME', 'tcp'),
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'port' => env('REDIS_PORT', 6379),
            ], [
                'parameters' => [
                    'password' => env('REDIS_PASSWORD', null),
                ],
            ]);
            // select DB
            self::$_redis->select(env('REDIS_DATABASE', 1));
        }

        return self::$_redis;
    }

    /**
     * Get database instance
     * @method db
     * @param  string $database
     * @return Object
     */
    public static function db($database = 'default')
    {
        if (!isset(self::$_db[$database])) {
            MedooConnection::loadConfig(APP_PATH.'/config/database.php');
            self::$_db[$database] = MedooConnection::getInstance($database);
        }

        return self::$_db[$database];
    }

    /**
     * Cache
     * @method cache
     * @param  string $path
     * @return mixed
     */
    public static function cache($path = 'data')
    {
        $path = app()->get('cache.path').'/'.$path;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (!isset(self::$_cache[$path])) {
            $cache = new \Doctrine\Common\Cache\FilesystemCache($path, '.cache');
            self::$_cache[$path] = $cache;
        }

        return self::$_cache[$path];
    }

    /**
     * Halt
     * Do something before sending response.
     * @method halt
     * @param  array   $msg
     * @param  integer $code
     * @return mixed
     */
    public static function halt(array $msg, $code = 200)
    {
        return app()->response(false)
                ->status($code)
                ->header("Content-Type", "application/json; charset=utf8")
                ->write(json_encode($msg))
                ->send();
    }

    /**
     * _stripslashesDeep
     * Deep data filters.
     * @method _stripslashesDeep
     * @param  mixed   $data
     * @return mixed
     */
    protected static function _stripslashesDeep($data)
    {
        if (is_array($data)) {
            return array_map([__CLASS__, __FUNCTION__], $data);
        } else {
            return stripslashes($data);
        }
    }
}
