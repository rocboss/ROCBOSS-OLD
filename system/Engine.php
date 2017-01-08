<?php

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
        $this->vars = [];

        $this->loader     = new Loader();
        $this->dispatcher = new Dispatcher();

        $this->init();
    }

    public function __call($name, $params)
    {
        $callback = $this->dispatcher->get($name);

        if (is_callable($callback)) {
            return $this->dispatcher->run($name, $params);
        }

        $shared = (!empty($params)) ? (bool) $params[0] : true;

        return $this->loader->load($name, $shared);
    }


    public function init()
    {
        static $initialized = false;
        $self = $this;

        if ($initialized) {
            $this->vars = [];
            $this->loader->reset();
            $this->dispatcher->reset();
        }

        $this->loader->register('request', '\system\net\Request');
        $this->loader->register('response', '\system\net\Response');
        $this->loader->register('router', '\system\net\Router');
        $this->loader->register('view', '\system\template\View', [], function ($view) use ($self) {
            $view->path = $self->get('system.views.path');
        });

        $methods = array(
            'start',
            'stop',
            'route',
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
        foreach ($methods as $name) {
            $this->dispatcher->set($name, array(
                $this,
                '_' . $name
            ));
        }

        $this->set('system.base_url', null);
        $this->set('system.handle_errors', true);
        $this->set('system.log_errors', false);
        $this->set('system.views.path', './views');

        $initialized = true;
    }

    public function handleErrors($enabled)
    {
        if ($enabled) {
            set_error_handler(array(
                $this,
                'handleError'
            ));
            set_exception_handler(array(
                $this,
                'handleException'
            ));
        } else {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($errno & error_reporting()) {
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    public function handleException(\Exception $e)
    {
        if ($this->get('system.log_errors')) {
            error_log($e->getMessage());
        }

        $this->error($e);
    }

    public function map($name, $callback)
    {
        if (method_exists($this, $name)) {
            throw new \Exception('Cannot override an existing framework method.');
        }

        $this->dispatcher->set($name, $callback);
    }

    public function register($name, $class, array $params = [], $callback = null)
    {
        if (method_exists($this, $name)) {
            throw new \Exception('Cannot override an existing framework method.');
        }

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
        if ($key === null) {
            return $this->vars;
        }

        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    public function set($key, $value = null)
    {
        if (is_array($key) || is_object($key)) {
            foreach ($key as $k => $v) {
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$key] = $value;
        }
    }

    public function has($key)
    {
        return isset($this->vars[$key]);
    }

    public function clear($key = null)
    {
        if (is_null($key)) {
            $this->vars = [];
        } else {
            unset($this->vars[$key]);
        }
    }

    public function path($dir)
    {
        $this->loader->addDirectory($dir);
    }


    public function _start()
    {
        $dispatched = false;
        $self       = $this;
        $request    = $this->request();
        $response   = $this->response();
        $router     = $this->router();

        if (ob_get_length() > 0) {
            $response->write(ob_get_clean());
        }

        ob_start();

        $this->handleErrors($this->get('system.handle_errors'));

        if ($request->ajax) {
            $response->cache(false);
        }

        $this->after('start', function () use ($self) {
            $self->stop();
        });

        while ($route = $router->route($request)) {
            $params = array_values($route->params);

            $continue = $this->dispatcher->execute($route->callback, $params);

            $dispatched = true;

            if (!$continue) {
                break;
            }

            $router->next();

            $dispatched = false;
        }

        if (!$dispatched) {
            $this->notFound();
        }
    }

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

        try {
            $this->response(false)->status(500)->write($msg)->send();
        } catch (\Exception $ex) {
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
        $base = $this->get('system.base_url');

        if ($base === null) {
            $base = $this->request()->base;
        }

        if ($base != '/' && strpos($url, '://') === false) {
            $url = preg_replace('#/+#', '/', $base . '/' . $url);
        }

        $this->response(false)->status($code)->header('Location', $url)->write($url)->send();
    }

    public function _render($file, $data = null, $key = null)
    {
        if ($key !== null) {
            $this->view()->set($key, $this->view()->fetch($file, $data));
        } else {
            $this->view()->render($file, $data);
        }
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

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $id) {
            $this->halt(304);
        }
    }

    public function _lastModified($time)
    {
        $this->response()->header('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $time));

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $time) {
            $this->halt(304);
        }
    }
}
