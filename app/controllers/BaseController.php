<?php
/**
 * BaseController
 */
class BaseController
{
    protected static $_name;
    protected static $_class;
    protected static $_method;

    /**
     * __construct
     * @return void
     */
    public function __construct()
    {
        // Middleware intercept
        Middleware::check(app()->router()->current()->callback);
    }

    /**
     * __callStatic
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public static function __callStatic($method, $arguments)
    {
        self::$_name = $method;
        self::$_class = get_called_class();
        self::$_method = $method;

        $return = call_user_func_array([(new self::$_class()), self::$_method], $arguments);
        
        return app()->get('isJob') ? $return : app()->json($return);
    }
}
