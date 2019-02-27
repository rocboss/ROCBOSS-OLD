<?php
/**
 * Middleware
 */
class Middleware
{
    public static $callback = null;
    public static $instance = null;
    public static $maps = [];

    public function __construct()
    {
        // Map Middlewares
        if (is_array(app()->get('middlewares'))) {
            foreach (app()->get('middlewares') as $middleware => $maps) {
                app()->map($middleware, function () use ($maps) {
                    list($name, $params) = func_get_args();
                    if (in_array($name, array_keys($maps))) {
                        return (new $maps[$name])->run($params);
                    }
                    throw new Exception("Can't find the `{$name}` middleware.");
                });
            }
        }
    }

    /**
     * Method __call
     *
     * @param string $middleware
     * @param array $arguments
     * @return mixed
     */
    public function __call($middleware, $arguments)
    {
        $middlewares = app()->get('middlewares');
        if (is_array($middlewares)) {
            if (in_array($middleware, array_keys($middlewares))) {
                $this->register($middlewares[$middleware], $arguments);
                return self::getInstance();
            }
        }
        throw new Exception("Can't find the `{$middleware}` middleware.");
    }

    /**
     * Register to current router.
     *
     * @param string $middleware
     * @param array $arguments
     * @return void
     */
    public function register($middleware, array $arguments)
    {
        $hash = md5(implode('@', self::$callback));
        self::$maps[$hash] = [$middleware, $arguments];
    }

    /**
     * Set callback
     *
     * @param array $callback
     * @return Object
     */
    public function setCallback($callback)
    {
        self::$callback = $callback;

        return self::getInstance();
    }

    /**
     * Middleware intercept
     *
     * @param array $callback
     * @return mixed
     */
    public static function check($callback)
    {
        $hash = md5(implode('@', $callback));
        $middlewareMap = !empty(self::$maps[$hash]) ? self::$maps[$hash] : [];
        
        if (!empty($middlewareMap) && count($middlewareMap) == 2) {
            $class = $middlewareMap[0];
            $params = $middlewareMap[1];
    
            return (new $class())->run($params);
        }
        return true;
    }

    /**
     * Get middleware instance
     *
     * @return Object
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
