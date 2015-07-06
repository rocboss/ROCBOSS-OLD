<?php
# 负责路由的具体实现。Router相当于对Route的包装。

namespace system\net;

class Route
{
    public $pattern;
    
    public $callback;
    
    public $methods = array();
    
    public $params = array();
    
    public $regex;
    
    public $splat = '';
    
    public $pass = false;
    
    public function __construct($pattern, $callback, $methods, $pass)
    {
        $this->pattern  = $pattern;
        
        $this->callback = $callback;
        
        $this->methods  = $methods;
        
        $this->pass     = $pass;
    }
    
    public function matchUrl($url)
    {
        if ($this->pattern === '*' || $this->pattern === $url)
        {
            if ($this->pass)
            {
                $this->params[] = $this;
            }
            
            return true;
        }
        
        $ids       = array();
        
        $last_char = substr($this->pattern, -1);
        
        if ($last_char === '*')
        {
            $n     = 0;
            
            $len   = strlen($url);
            
            $count = substr_count($this->pattern, '/');
            
            for ($i = 0; $i < $len; $i++)
            {
                if ($url[$i] == '/')

                    $n++;
                
                if ($n == $count)
                    
                    break;
            }
            
            $this->splat = (string) substr($url, $i + 1);
        }
        
        $regex = str_replace(array(
            ')',
            '/*'
        ), array(
            ')?',
            '(/?|/.*?)'
        ), $this->pattern);
        
        $regex = preg_replace_callback('#@([\w]+)(:([^/\(\)]*))?#', function($matches) use (&$ids)
        {
            $ids[$matches[1]] = null;
            
            if (isset($matches[3]))
            {
                return '(?P<' . $matches[1] . '>' . $matches[3] . ')';
            }
            
            return '(?P<' . $matches[1] . '>[^/\?]+)';
        }, $regex);
        
        if ($last_char === '/')
        {
            $regex .= '?';
        }
        else
        {
            $regex .= '/?';
        }
        
        if (preg_match('#^' . $regex . '(?:\?.*)?$#i', $url, $matches))
        {
            foreach ($ids as $k => $v)
            {
                $this->params[$k] = (array_key_exists($k, $matches)) ? urldecode($matches[$k]) : null;
            }
            
            if ($this->pass)
            {
                $this->params[] = $this;
            }
            
            $this->regex = $regex;
            
            return true;
        }
        
        return false;
    }
    
    public function matchMethod($method)
    {
        return count(array_intersect(array($method, '*'), $this->methods)) > 0;
    }
}
?>