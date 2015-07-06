<?php
# 包含ROCPHP框架的核心功能。负责加载HTTP请求，运行已注册的服务，并生成最后的HTTP响应。

namespace system;

use system\core\Loader;

use system\core\Dispatcher;

class Engine
{
    protected $vars;
    
    protected $loader;
    
    protected $dispatcher;
    
    public function __construct()
    {
        $this->vars = array();
        
        # Dispatcher负责分发处理函数，Loader负责对象的加载
        $this->loader     = new Loader();

        $this->dispatcher = new Dispatcher();
        
        # 引擎初始化
        $this->init();
    }
    
    # __call是一个魔术方法，当调用一个不存在的函数时，会调用到该函数。动态调用通过此方法执行
    public function __call($name, $params)
    {
        # 判断是类还是可直接调用的函数
        $callback = $this->dispatcher->get($name);
        
        # 判断方法或函数是否可以调用，若可以则通过dispatcher处理
        if (is_callable($callback))
        {
            return $this->dispatcher->run($name, $params);
        }
        
        # 是否是共享实例
        $shared = (!empty($params)) ? (bool) $params[0] : true;
        
        # 通过loader加载该类的对象
        return $this->loader->load($name, $shared);
    }
    
    # 初始化引擎
    public function init()
    {
        static $initialized = false;

        $self = $this;
        
        if ($initialized)
        {
            $this->vars = array();
            $this->loader->reset();
            $this->dispatcher->reset();
        }
        
        # 注册默认组件
        $this->loader->register('request', '\system\net\Request');

        $this->loader->register('response', '\system\net\Response');

        $this->loader->register('router', '\system\net\Router');

        $this->loader->register('view', '\system\template\Template');

        $this->loader->register('db', '\system\db\DB');
        
        # 注册框架方法
        $methods = array(
            'start',
            'stop',
            'route',
            'clearRoutes',
            'getNowRoute',
            'halt',
            'error',
            'notFound',
            'render',
            'redirect',
            'etag',
            'lastModified',
            'json',
            'jsonp'
        );

        foreach ($methods as $name)
        {
            $this->dispatcher->set($name, array($this, '_' . $name));
        }
        
        # 默认配置
        $this->set('root', $this->request()->base);

        $this->set('handle_errors', true);

        $this->set('log_errors', false);
        
        $initialized = true;
    }
    
    public function handleErrors($enabled)
    {
        if ($enabled)
        {
            set_error_handler(array($this, 'handleError'));

            set_exception_handler(array($this, 'handleException'));
        }
        else
        {
            restore_error_handler();

            restore_exception_handler();
        }
    }
    
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($errno & error_reporting())
        {
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }
    
    public function handleException(\Exception $e)
    {
        if ($this->get('log_errors'))
        {
            error_log($e->getMessage());
        }
        
        $this->error($e);
    }
    
    # 映射自定义函数
    public function map($name, $callback)
    {
        # 不允许映射已经存在的Engine方法
        if (method_exists($this, $name))
        {
            throw new \Exception('Cannot override an existing framework method.');
        }
        
        # 通过dispatcher的set函数将对应的回调函数绑定到一个事件中
        $this->dispatcher->set($name, $callback);
    }
    
    # 注册自定义类
    public function register($name, $class, array $params = array(), $callback = null)
    {
        # 不允许覆盖已经存在的Engine方法
        if (method_exists($this, $name))
        {
            throw new \Exception('Cannot override an existing framework method.');
        }
        
        # 通过loader的register函数进行注册
        $this->loader->register($name, $class, $params, $callback);
    }
    
    public function before($name, $callback)
    {
        $this->dispatcher->hook($name, 'before', $callback);
    }
    
    public function after($name, $callback)
    {
        $this->dispatcher->hook($name, 'after', $callback);
    }
    
    public function get($key = null)
    {
        if ($key === null) 

            return $this->vars;
        
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }
    
    public function set($key, $value = null)
    {
        if (is_array($key) || is_object($key))
        {
            foreach ($key as $k => $v)
            {
                $this->vars[$k] = $v;
            }
        }
        else
        {
            $this->vars[$key] = $value;
        }
    }
    
    public function has($key)
    {
        return isset($this->vars[$key]);
    }
    
    public function clear($key = null)
    {
        if (is_null($key))
        {
            $this->vars = array();
        }
        else
        {
            unset($this->vars[$key]);
        }
    }
    
    public function path($dir)
    {
        $this->loader->addDirectory($dir);
    }

    public function _clearRoutes()
    {

        $this->router()->clear();
    }
    
    public function _getNowRoute()
    {
        $request    = $this->request();

        $router     = $this->router();

        return $router->route($request)->callback;
    }

    # 启动这个框架
    public function _start()
    {
        $dispatched = false;

        $self       = $this;

        $request    = $this->request();

        $response   = $this->response();
        
        $router     = $this->router();
        
        # 冲刷掉已经存在的输出
        if (ob_get_length() > 0)
        {
            $response->write(ob_get_clean());
        }
        
        # 启动输出缓冲
        ob_start();
        
        $this->handleErrors($this->get('handle_errors'));
        
        # 对AJAX请求关闭缓存
        if ($request->ajax)
        {
            $response->cache(false);
        }
        
        # 允许后置过滤器的运行
        $this->after('start', function() use ($self)
        {
            # start完成之后会调用stop()函数
            $self->stop();
        });
        
        # 对该请求进行路由
        while ($route = $router->route($request))
        {
            $params = array_values($route->params);
            
            # 是否让路由链继续下去
            $continue = $this->dispatcher->execute($route->callback, $params);
            
            $dispatched = true;
            
            if (!$continue)
                break;
            
            $router->next();
            
            $dispatched = false;
        }
        
        # 路由没找匹配到
        if (!$dispatched)
        {
            $this->notFound();
        }
    }
    
    # 停止框架并且输出当前的响应内容
    public function _stop($code = 200)
    {
        $this->response()->status($code)->write(ob_get_clean())->send();
    }
    
    public function _halt($code = 200, $message = '')
    {
        $this->response(false)->status($code)->write($message)->send();
    }
    
    public function _error(\Exception $e)
    {
        $msg = sprintf('<h1>500 Internal Server Error</h1>' . '<h3>%s (%s)</h3>' . '<pre>%s</pre>', $e->getMessage(), $e->getCode(), $e->getTraceAsString());
        
        try
        {
            $this->response(false)->status(500)->write($msg)->send();
        }
        catch (\Exception $ex)
        {
            exit($msg);
        }
    }
    
    public function _notFound()
    {
        $this->response(false)->status(404)->write('<h1>404 Not Found</h1>' . '<h3>The page you have requested could not be found.</h3>' . str_repeat(' ', 512))->send();
    }
    
    public function _route($pattern, $callback, $pass_route = false)
    {
        $this->router()->map($pattern, $callback, $pass_route);
    }
    
    public function _redirect($url, $code = 303)
    {
        $base = $this->get('root');
        
        if ($base === null)
        {
            $base = $this->request()->base;

            $this->set('root', $base);
        }
        
        if ($base != '/' && strpos($url, '://') === false)
        {
            $url = preg_replace('#/+#', '/', $base . '/' . $url);
        }
        
        $this->response(false)->status($code)->header('Location', $url)->write($url)->send();
    }

    public function _json($data, $code = 200, $encode = true)
    {
        $json = ($encode) ? json_encode($data) : $data;
        
        $this->response(false)->status($code)->header('Content-Type', 'application/json')->write($json)->send();
    }
    
    public function _jsonp($data, $param = 'jsonp', $code = 200, $encode = true)
    {
        $json = ($encode) ? json_encode($data) : $data;
        
        $callback = $this->request()->query[$param];
        
        $this->response(false)->status($code)->header('Content-Type', 'application/javascript')->write($callback . '(' . $json . ');')->send();
    }
    
    public function _etag($id, $type = 'strong')
    {
        $id = (($type === 'weak') ? 'W/' : '') . $id;
        
        $this->response()->header('ETag', $id);
        
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $id)
        {
            $this->halt(304);
        }
    }
    
    public function _lastModified($time)
    {
        $this->response()->header('Last-Modified', date(DATE_RFC1123, $time));
        
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $time)
        {
            $this->halt(304);
        }
    }
}
?>