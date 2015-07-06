<?php
# 负责框架内对象的加载。用自定义的初始化参数来生成新的类实例，并且维护可复用的类实例的列表，还处理类的自动加载。

namespace system\core;

class Loader
{
    protected $classes = array();
    
    protected $instances = array();
    
    protected static $dirs = array();
    
    public function register($name, $class, array $params = array(), $callback = null)
    {
        # 先清除原先实例
        unset($this->instances[$name]);
        
        $this->classes[$name] = array($class, $params, $callback);
    }
    
    public function unregister($name)
    {
        unset($this->classes[$name]);
    }
    
    # 加载一个已注册的类
    public function load($name, $shared = true)
    {
        $obj = null;
        
        # $this->classes 注册过的类; $this->instances 加载过的实例
        if (isset($this->classes[$name]))
        {
            list($class, $params, $callback) = $this->classes[$name];
            
            $exists = isset($this->instances[$name]);
            
            if ($shared)
            {
                $obj = ($exists) ? $this->getInstance($name) : $this->newInstance($class, $params);
                
                if (!$exists)
                {
                    $this->instances[$name] = $obj;
                }
            }
            else
            {
                $obj = $this->newInstance($class, $params);
            }
            
            if ($callback && (!$shared || !$exists))
            {
                $ref = array(&$obj);

                call_user_func_array($callback, $ref);
            }
        }
        
        return $obj;
    }
    
    # 获取实例
    public function getInstance($name)
    {
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }
    
    # 返回新建的实例
    public function newInstance($class, array $params = array())
    {
        if (is_callable($class))
        {
            return call_user_func_array($class, $params);
        }
        
        switch (count($params))
        {
            case 0:
                return new $class();
            
            case 1:
                return new $class($params[0]);
            
            case 2:
                return new $class($params[0], $params[1]);
            
            case 3:
                return new $class($params[0], $params[1], $params[2]);
            
            case 4:
                return new $class($params[0], $params[1], $params[2], $params[3]);
            
            case 5:
                return new $class($params[0], $params[1], $params[2], $params[3], $params[4]);
            
            default:
                $refClass = new \ReflectionClass($class);
                return $refClass->newInstanceArgs($params);
        }
    }
    
    public function reset()
    {
        $this->classes   = array();

        $this->instances = array();
    }
    
    # 实现自动加载
    public static function autoload($enabled = true, $dirs = array())
    {
        if ($enabled)
        {
            # 将函数注册到SPL __autoload函数栈中。如果该栈中的函数尚未激活，则激活它们。这是实现自动加载重要函数
            spl_autoload_register(array(__CLASS__, 'loadClass'));
        }
        else
        {
            spl_autoload_unregister(array(__CLASS__, 'loadClass'));
        }
        
        if (!empty($dirs))
        {
            self::addDirectory($dirs);
        }
    }
    
    # 自动加载类
    public static function loadClass($class)
    {
        $class_file = str_replace(array('\\', '_'), '/', $class) . '.php';
        
        foreach (self::$dirs as $dir)
        {
            $file = $dir . '/' . $class_file;

            if (file_exists($file))
            {
                require $file;

                return;
            }
        }
    }
    
    # 引入类库所在目录，用于自动加载时的目录拼接
    public static function addDirectory($dir)
    {
        if (is_array($dir) || is_object($dir))
        {
            foreach ($dir as $value)
            {
                self::addDirectory($value);
            }
        }
        else if (is_string($dir))
        {
            if (!in_array($dir, self::$dirs))
                
                self::$dirs[] = $dir;
        }
    }
}
?>