<?php
# 负责框架内事件的分发处理。事件即是对类方法或函数的简单的称呼（别名）。它还允许你在事件上的挂钩点挂载别的函数，能够改变函数的输入或者输出。

namespace system\core;

class Dispatcher
{
    protected $events = array();
    
    protected $filters = array();
    
    # 对事件进行分发处理
    public function run($name, array $params = array())
    {
        $output = '';
        
        # 运行前置过滤器
        if (!empty($this->filters[$name]['before']))
        {
            $this->filter($this->filters[$name]['before'], $params, $output);
        }
        
        # 运行所请求的方法
        $output = $this->execute($this->get($name), $params);
        
        # 运行后置过滤器
        if (!empty($this->filters[$name]['after']))
        {
            $this->filter($this->filters[$name]['after'], $params, $output);
        }
        
        return $output;
    }
    
    # 将回调注册到一个事件之中
    public function set($name, $callback)
    {
        $this->events[$name] = $callback;
    }
    
    # 得到事件关联的回调
    public function get($name)
    {
        return isset($this->events[$name]) ? $this->events[$name] : null;
    }
    
    public function has($name)
    {
        return isset($this->events[$name]);
    }
    
    public function clear($name = null)
    {
        if ($name !== null)
        {
            unset($this->events[$name]);

            unset($this->filters[$name]);
        }
        else
        {
            $this->events  = array();

            $this->filters = array();
        }
    }
    
    # 在事件上挂一个回调函数
    public function hook($name, $type, $callback)
    {
        $this->filters[$name][$type][] = $callback;
    }
    
    public function filter($filters, &$params, &$output)
    {
        $args = array(
            &$params,
            &$output
        );
        foreach ($filters as $callback)
        {
            $continue = $this->execute($callback, $args);

            if ($continue === false)
                
                break;
        }
    }
    
    public static function execute($callback, array &$params = array())
    {
        if (is_callable($callback))
        {
            return is_array($callback) ? self::invokeMethod($callback, $params) : self::callFunction($callback, $params);
        }
        else
        {
            throw new \Exception('Invalid callback specified.');
        }
    }
    
    public static function callFunction($func, array &$params = array())
    {
        switch (count($params))
        {
            case 0:
                return $func();

            case 1:
                return $func($params[0]);
            
            case 2:
                return $func($params[0], $params[1]);
            
            case 3:
                return $func($params[0], $params[1], $params[2]);
            
            case 4:
                return $func($params[0], $params[1], $params[2], $params[3]);
            
            case 5:
                return $func($params[0], $params[1], $params[2], $params[3], $params[4]);
            
            default:
                return call_user_func_array($func, $params);
        }
    }
    
    # 调用一个方法
    public static function invokeMethod($func, array &$params = array())
    {
        list($class, $method) = $func;
        
        $instance = is_object($class);
        
        switch (count($params))
        {
            case 0:
                return ($instance) ? $class->$method() : $class::$method();
            
            case 1:
                return ($instance) ? $class->$method($params[0]) : $class::$method($params[0]);
            
            case 2:
                return ($instance) ? $class->$method($params[0], $params[1]) : $class::$method($params[0], $params[1]);
            
            case 3:
                return ($instance) ? $class->$method($params[0], $params[1], $params[2]) : $class::$method($params[0], $params[1], $params[2]);
            
            case 4:
                return ($instance) ? $class->$method($params[0], $params[1], $params[2], $params[3]) : $class::$method($params[0], $params[1], $params[2], $params[3]);
            
            case 5:
                return ($instance) ? $class->$method($params[0], $params[1], $params[2], $params[3], $params[4]) : $class::$method($params[0], $params[1], $params[2], $params[3], $params[4]);
            
            default:
                return call_user_func_array($func, $params);
        }
    }
    
    public function reset()
    {
        $this->events  = array();
        
        $this->filters = array();
    }
}
?>