<?php
# 开启session
session_start();

# 正式运营时关闭所有调试
error_reporting(0);

# 引入框架入口文件
require 'system/Entrance.php';

# 引入数据库配置文件
require 'app/config/db_config.php';

# 引入路由规则配置文件
require 'app/config/router_config.php';

# 实例化ROC框架，动态调用
$app = ROC::app();

# 路由分发（注册规则）
foreach ($router_config as $path => $rule)
{   
    $app->route($path, $rule);
}

# 是否匹配到路由规则
$isExistRule = true;

# 路由分发（实例化Class）
foreach ($router_config as $path => $rule)
{
    $nowRoute = $app->getNowRoute();

    if (is_array($nowRoute) && $rule == $nowRoute)
    {
        # 清除之前注册的路由
        $app->clearRoutes();

        # 实例化Class
        $class = '\app\controller\\'.$rule[0];

        $rule[0] = new $class($app, $db_config);

        # 只注册当前URL对应的路由
        $app->route($path, $rule);

        $isExistRule = true;

        break;
    }

    $isExistRule = false;
}

if (!$isExistRule)
{
    $app->clearRoutes();
}

# 启动框架
$app->start();
?>
