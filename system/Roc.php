<?php
class Roc
{
    /**
     * 开启框架
     * @param array $Router
     */
    public static function Start($Router)
    {
        $Controller = self::checkRouter(Filter::in($Router['controller']));

        $Action = self::checkRouter(Filter::in($Router['action']));

        $ControlName = $Controller . 'Control';

        $ControlFile = 'application/controller/' . $Controller . '.php';

        if (is_file($ControlFile))
        {
            // 载入公用控制器
            require 'application/controller/common.php';
            // 载入请求控制器
            require $ControlFile;
            // 实例化控制器
            $newController = new $ControlName();
        }
        else
        {
            // 控制器不存在
            die('Controller [' . $Controller . '] Does Not Exist');
        }
        
        if (method_exists($newController, $Action))
        {
            // 调用方法
            $newController->{$Action}();
        }
        else
        {
            // 方法不存在
            die('Method [' . $Action . '] Does Not Exist');
        }
    }
    /**
     * 载入系统类库
     * @param array $libs
     */
    public static function loadSystemLibs($libs)
    {
        foreach ($libs as $lib)
        {
            $core = 'system/libs/' . $lib . '.lib.php';
            
            require $core;
        }
    }
    /**
     * 过滤路由参数
     * @param string $str
     * @return string
     */
    public static function checkRouter($str)
    {
        if (preg_match('/^[A-Za-z0-9_]+$/', $str) != 1)
        {
            die('Illegal Character !');
        }
        else
        {
            return $str;
        }
    }
}
?>