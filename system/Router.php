<?php
class Router
{
    /**
     * 获取路由
     * @param int $types
     * @return array
     */
    public static function getRouter($types = 1)
    {
        if (isset($_SERVER['PATH_INFO']))
        {
            $query_string = substr(str_replace(array(
                '.html',
                '.htm',
                '.php',
                '//'
            ) , '', $_SERVER['PATH_INFO']) , 1);
        }
        else
        {
            $query_string = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
        }

        if ($types == 1)
        {
            $temp = explode('/', $query_string);
        }
        elseif ($types == 2)
        {
            $temp = explode('-', $query_string);
        }
        elseif ($types == 3)
        {
            return array(
                'controller' => $_GET['c'],
                'action' => $_GET['a']
            );
        }

        $url = array_filter($temp);

        if (count($url) == 0)
        {
            $url[0] = 'home';
        }
        if (count($url) == 1)
        {
            $url[1] = 'index';
        }

        list($controller, $action) = $url;

        $params = '';

        if (count($url) == 3)
        {
            $params = $url[2];
        }
        elseif (count($url) > 3)
        {
            $params = array();
            
            array_shift($url);

            array_shift($url);

            foreach ($url as $key => $value)
            {
                if( $key%2 == 0 )
                {
                    $params[$value] = $url[$key+1];
                }
            }
        }
        
        return array(
            "controller" => !empty($controller) ? $controller : 'home',
            "action" => !empty($action) ? $action : 'index',
            "params" => $params,
        );
    }
}

?>