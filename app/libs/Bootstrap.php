<?php

class Bootstrap
{
    protected static $_dbInstances = [];
    protected static $_controllerInstances = [];
    protected static $_modelInstances = [];
    protected static $_router = [];
    protected static $_config = [];

    // 初始化操作
    public static function init()
    {
        // 设置时区
        date_default_timezone_set('Asia/Shanghai');

        if (get_magic_quotes_gpc()) {
            $_GET = self::stripslashesDeep($_GET);
            $_POST = self::stripslashesDeep($_POST);
            $_COOKIE = self::stripslashesDeep($_COOKIE);
        }

        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        Roc::map('controller', [__CLASS__, 'getController']);
        Roc::map('model', [__CLASS__, 'getModel']);
        Roc::map('url', [__CLASS__, 'getUrl']);
        Roc::map('db', [__CLASS__, 'getDb']);
        Roc::map('redis', [__CLASS__, 'getRedis']);
        Roc::map('filter', [__CLASS__, 'getFilter']);
        Roc::map('page', [__CLASS__, 'getPage']);
        Roc::map('secret', [__CLASS__, 'getSecret']);
        Roc::map('client', [__CLASS__, 'getClient']);
        Roc::map('qiniu', [__CLASS__, 'getQiniu']);
        Roc::map('push', [__CLASS__, 'getPush']);
        Roc::map('qq', [__CLASS__, 'getQq']);
        Roc::map('weibo', [__CLASS__, 'getWeibo']);
        Roc::map('geetest', [__CLASS__, 'getGeetest']);
        Roc::map('alipay', [__CLASS__, 'getAlipay']);

        // 自动生成系统配置
        if (!file_exists('_config.php')) {
            $allSysData = Roc::db()->from('roc_config')->select()->many();
            $fileContent = '<?php'."\n".'return ['."\n";

            if (!empty($allSysData)) {
                foreach ($allSysData as $key => $value) {
                    $fileContent .= "'".$value['key']."' => '".$value['value']."', \n";
                    self::$_config[$value['key']] = $value['value'];
                }
            }

            $fileContent .= '];'."\n ?>";

            @file_put_contents('_config.php', $fileContent);
        } else {
            self::$_config = require '_config.php';
        }

        Roc::set(['sys.config'=>self::$_config]);

        // 初始化路由
        self::initRoute();
    }

    public static function getDb($name = 'db')
    {
        if (!isset(self::$_dbInstances[$name])) {
            $db_host = Roc::get($name.'.host');
            $db_port = Roc::get($name.'.port');
            $db_user = Roc::get($name.'.user');
            $db_pass = Roc::get($name.'.pass');
            $db_name = Roc::get($name.'.name');
            $db_charset = Roc::get($name.'.charset');

            try {
                $pdo = new \PDO('mysql:host='.$db_host.';dbname='.$db_name.';port='.$db_port, $db_user, $db_pass);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->exec('SET CHARACTER SET '.$db_charset);
                $db = new DBEngine();
                $db->setDb($pdo);

                self::$_dbInstances[$name] = $db;
            } catch (Exception $e) {
                die(json_encode(['code'=>500, 'msg'=>'MySQL数据库连接失败', 'data'=>''], JSON_UNESCAPED_UNICODE));
            }
        }

        return self::$_dbInstances[$name];
    }

    public static function getRedis()
    {
        try {
            $redis = new \Redis();

            $connect = $redis->connect(Roc::get('redis.host'), Roc::get('redis.port'));
        } catch (Exception $e) {
            die(json_encode(['code'=>500, 'msg'=>'请检查Redis扩展是否已安装、连接信息是否正确', 'data'=>''], JSON_UNESCAPED_UNICODE));
        }

        $isSuccess = true;

        if ($connect) {
            $auth = Roc::get('redis.auth');

            if (!empty($auth)) {
                if (!$redis->auth($auth)) {
                    $isSuccess = false;
                }
            }
        } else {
            $isSuccess = false;
        }

        if ($isSuccess === false) {
            die(json_encode(['code'=>500, 'msg'=>'Redis服务器连接失败', 'data'=>''], JSON_UNESCAPED_UNICODE));
        }

        // 默认预留给杂项使用
        $redis->select(0);

        return $redis;
    }

    public static function getFilter()
    {
        return new Filter();
    }

    public static function getPage()
    {
        return new Page();
    }

    public static function getSecret()
    {
        return new Secret();
    }

    public static function getClient()
    {
        return new Client();
    }

    public static function getQiniu()
    {
        $qiniu = new Qiniu();

        $qiniu->setConfig([
            'access_token' => Roc::get('qiniu.ak'),
            'secret_token' => Roc::get('qiniu.sk'),
            'domain' => Roc::get('qiniu.domain'),
            'bucket' => Roc::get('qiniu.bucket')
        ]);

        return $qiniu;
    }

    public static function getPush()
    {
        $push = new Push();

        $push->setConfig([
            'AppKey' => Roc::get('push.appkey'),
            'AppID' => Roc::get('push.appid'),
            'MasterSecret' => Roc::get('push.mastersecret')
        ]);

        return $push;
    }

    public static function getQq()
    {
        return new QQ(Roc::get('qq.appid'), Roc::get('qq.appkey'), '/register/qq');
    }

    public static function getWeibo()
    {
        return new Weibo(Roc::get('weibo.akey'), Roc::get('weibo.skey'));
    }

    public static function getGeetest()
    {
        return new Geetest(Roc::get('geetest.appid'), Roc::get('geetest.appkey'));
    }

    public static function getAlipay()
    {
        return new Alipay([
            'pid' => Roc::get('alipay.pid'),
            'key' => Roc::get('alipay.key'),
            'cacert' => getcwd().'/../app/libs/cacert.pem',
            'transport' => 'https'
        ]);
    }

    public static function getController($name)
    {
        $class = '\\' . trim(str_replace('/', '\\', $name), '\\') . 'Controller';

        if (!isset(self::$_controllerInstances[$class])) {
            $instance = new $class();
            self::$_controllerInstances[$class] = $instance;
        }

        return self::$_controllerInstances[$class];
    }

    public static function getModel($name = null, $initDb = true)
    {
        if (is_null($name)) {
            return self::getDb();
        }

        $class = '\\' . trim(str_replace('/', '\\', ucfirst($name)), '\\') . 'Model';

        if (!isset(self::$_modelInstances[$class])) {
            $instance = new $class();
            if ($initDb) {
                $instance->setDb(self::getDb());
            }

            self::$_modelInstances[$class] = $instance;
        }

        return self::$_modelInstances[$class];
    }

    public static function getUrl($name, array $params = [])
    {
        if (!isset(self::$_router[$name])) {
            return '/';
        } else {
            $url = self::$_router[$name];

            foreach ($params as $k => $v) {
                if (preg_match('/^\w+$/', $v)) {
                    $url = preg_replace('#@($k)(:([^/\(\)]*))?#', $v, $url);
                }
            }
            return $url;
        }
    }

    public static function initRoute()
    {
        $router = Roc::get('system.router');

        if (is_array($router)) {
            foreach ($router as $route) {
                self::$_router[$route[1]] = $route[0];

                $tmp = explode(':', $route[1]);
                $class = '\\' . trim(str_replace('/', '\\', $tmp[0]), '\\') . 'Controller';
                $func = $tmp[1];
                $pattern = $route[0];

                Roc::route($pattern, [$class, $func]);
            }
        }

        Roc::route('/@module/@controller/@action/*', function () {
            $params = func_get_args();

            $module = array_shift($params);
            $controller = array_shift($params);
            $action = array_shift($params);
            $routeObj = array_shift($params);
            $params = explode('/', $routeObj->splat);

            unset($routeObj);

            $className = $module == 'command' ? '\\'.ucfirst($controller).'Command' : '\\'.$module.'\\'.ucfirst($controller).'Controller';
            $actionName = 'action'.str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));

            if (is_callable([$className, $actionName])) {
                call_user_func_array([$className, $actionName], $params);
            } else {
                return true;
            }
        }, true);
    }

    public static function stripslashesDeep($data)
    {
        if (is_array($data)) {
            return array_map([__CLASS__, __FUNCTION__], $data);
        } else {
            return stripslashes($data);
        }
    }
}
