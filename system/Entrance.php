<?php
# ROC框架入口文件

require_once __DIR__.'/core/Loader.php';

\system\core\Loader::autoload(true, dirname(__DIR__));

class ROC
{
    private static $engine;
    
    # 禁止直接动态实例化
    private function __construct() {}

    private function __destruct() {}
    
    private function __clone() {}
    
    # __callStatic()这个魔术方法能处理所有的静态函数
    public static function __callStatic($name, $params)
    {
        static $initialized = false;
        
        # 这里定义框架的自动加载机制，实际上是依据PSR-0标准来做的        
        if (!$initialized)
        {   
            # Engine类是框架的引擎所在
            self::$engine = new \system\Engine();
            
            $initialized = true;
        }
        
        # ROC框架对Engine包装了一层。对ROC类静态函数的调用，实质上是对Engine类的相应函数的调用
        return \system\core\Dispatcher::invokeMethod(array(self::$engine, $name), $params);
    }
    
    # 返回框架实例，用于动态调用
    public static function app()
    {
        self::$engine = new \system\Engine();
        
        return self::$engine;
    }
}
?>