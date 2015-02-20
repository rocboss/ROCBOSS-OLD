<?php
session_start();

# 开发调试时建议设为 E_ALL ，运营时请设为 0
error_reporting(0);

date_default_timezone_set('PRC');

define('ROC', TRUE);

# 定义项目根目录，若是子目录则为 '/子目录名/'
define('ROOT', '/');

# 引入框架核心文件
require 'system/Roc.php';

# 引入框架路由文件
require 'system/Router.php';

# 载入应用配置
require 'application/config/config.php';

# 载入系统类库
Roc::loadSystemLibs(array(
	# 系统加密类
	'Secret',

	# 系统过滤类
	'Filter',

	# 系统数据库操作类
	'DB',

	# 系统模板引擎类
	'Template',

	# 系统图像类
	'Image',

	# 系统分页类
	'Page',

	# 系统工具类
	'Utils'
));

# 获取路由
$Router = Router::getRouter();

# 开启框架
Roc::Start($Router);

?>