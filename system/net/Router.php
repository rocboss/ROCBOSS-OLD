<?php
# 负责将一个HTTP讲求发送到指定的函数进行处理，试图将请求的URL和一系列用户定义的URL范式进行匹配。

namespace system\net;

class Router
{
    protected $routes = array();
    
    protected $index = 0;
    
    public function getRoutes()
    {
        return $this->routes;
    }
    
    public function clear()
    {
        $this->index = 0;
        
        $this->routes = array();
    }
    
    public function map($pattern, $callback, $pass_route = false)
    {
        $url     = $pattern;
        
        $methods = array('*');
        
        if (strpos($pattern, ' ') !== false)
        {
            list($method, $url) = explode(' ', trim($pattern), 2);
            
            $methods = explode('|', $method);
        }
        
        $this->routes[] = new Route($url, $callback, $methods, $pass_route);
    }
    
    public function route(Request $request)
    {
        while ($route = $this->current())
        {
            if ($route !== false && $route->matchMethod($request->method) && $route->matchUrl($request->url))
            {
                return $route;
            }
            $this->next();
        }
        
        return false;
    }
    
    public function current()
    {
        return isset($this->routes[$this->index]) ? $this->routes[$this->index] : false;
    }
    
    public function next()
    {
        $this->index++;
    }
    
    public function reset()
    {
        $this->index = 0;
    }
}
?>