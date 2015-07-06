<?php
# 代表了一个HTTP请求。所有来自$_GET,$_POST,$_COOKIE,$_FILES中的数据都要通过Request类获取和访问。默认的Request属性就包括url,base,method,user_agent等。

namespace system\net;

use system\util\Collection;

class Request
{
    public $url;
    
    public $base;
    
    public $method;
    
    public $referrer;
    
    public $ip;
    
    public $ajax;
    
    public $scheme;
    
    public $user_agent;
    
    public $type;
    
    public $length;
    
    public $query;
    
    public $data;
    
    public $cookies;
    
    public $files;
    
    public $secure;
    
    public $accept;
    
    public $proxy_ip;
    
    public function __construct($config = array())
    {
        if (empty($config))
        {
            $config = array(
                'url' => self::getVar('REQUEST_URI', '/'),
                'base' => str_replace(array(
                    '\\',
                    ' '
                ), array(
                    '/',
                    '%20'
                ), dirname(self::getVar('SCRIPT_NAME'))),
                'method' => self::getMethod(),
                'referrer' => self::getVar('HTTP_REFERER'),
                'ip' => self::getVar('REMOTE_ADDR'),
                'ajax' => self::getVar('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest',
                'scheme' => self::getVar('SERVER_PROTOCOL', 'HTTP/1.1'),
                'user_agent' => self::getVar('HTTP_USER_AGENT'),
                'type' => self::getVar('CONTENT_TYPE'),
                'length' => self::getVar('CONTENT_LENGTH', 0),
                'query' => new Collection($_GET),
                'data' => new Collection($_POST),
                'cookies' => new Collection($_COOKIE),
                'files' => new Collection($_FILES),
                'secure' => self::getVar('HTTPS', 'off') != 'off',
                'accept' => self::getVar('HTTP_ACCEPT'),
                'proxy_ip' => self::getProxyIpAddress()
            );
        }
        
        $this->init($config);
    }
    
    public function init($properties = array())
    {
        foreach ($properties as $name => $value)
        {
            $this->$name = $value;
        }
        
        if ($this->base != '/' && strlen($this->base) > 0 && strpos($this->url, $this->base) === 0)
        {
            $this->url = substr($this->url, strlen($this->base));
        }
        
        if (empty($this->url))
        {
            $this->url = '/';
        }
        else
        {
            $_GET += self::parseQuery($this->url);
            
            $this->query->setData($_GET);
        }
        
        if (strpos($this->type, 'application/json') === 0)
        {
            $body = $this->getBody();
            if ($body != '')
            {
                $data = json_decode($body, true);
                if ($data != null)
                {
                    $this->data->setData($data);
                }
            }
        }
    }
    
    public static function getBody()
    {
        static $body;
        
        if (!is_null($body))
        {
            return $body;
        }
        
        $method = self::getMethod();
        
        if ($method == 'POST' || $method == 'PUT' || $method == 'PATCH')
        {
            $body = file_get_contents('php://input');
        }
        
        return $body;
    }
    
    public static function getMethod()
    {
        $method = self::getVar('REQUEST_METHOD', 'GET');
        
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
        {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }
        elseif (isset($_REQUEST['_method']))
        {
            $method = $_REQUEST['_method'];
        }
        
        return strtoupper($method);
    }
    
    public static function getProxyIpAddress()
    {
        static $forwarded = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED');
        
        $flags = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
        
        foreach ($forwarded as $key)
        {
            if (array_key_exists($key, $_SERVER))
            {
                sscanf($_SERVER[$key], '%[^,]', $ip);
                if (filter_var($ip, \FILTER_VALIDATE_IP, $flags) !== false)
                {
                    return $ip;
                }
            }
        }
        
        return '';
    }
    
    public static function getVar($var, $default = '')
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : $default;
    }
    
    public static function parseQuery($url)
    {
        $params = array();
        
        $args = parse_url($url);
        if (isset($args['query']))
        {
            parse_str($args['query'], $params);
        }
        
        return $params;
    }
}
?>