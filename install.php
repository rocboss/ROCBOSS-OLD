<?php
if (isset($_POST['host']))
{
	$host = $_POST['host'];
	$user = $_POST['user'];
	$password = $_POST['password'];
	$name = $_POST['name'];
	$admin_name = $_POST['admin_name'];
	$admin_email = $_POST['admin_email'];
	$admin_password = $_POST['admin_password'];
	$safe_key = $_POST['safe_key'];
	$path = $_POST['path'];

	$conn = mysql_connect($host, $user, $password);

	if (!$conn)
	{
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($name, $conn);

	mysql_query("SET NAMES utf8");
	mysql_query("SET FOREIGN_KEY_CHECKS = 0");
	mysql_query("DROP TABLE IF EXISTS `roc_attachment`");
	mysql_query("DROP TABLE IF EXISTS `roc_favorite`");
	mysql_query("DROP TABLE IF EXISTS `roc_floor`");
	mysql_query("DROP TABLE IF EXISTS `roc_follow`");
	mysql_query("DROP TABLE IF EXISTS `roc_notification`");
	mysql_query("DROP TABLE IF EXISTS `roc_praise`");
	mysql_query("DROP TABLE IF EXISTS `roc_reply`");
	mysql_query("DROP TABLE IF EXISTS `roc_score`");
	mysql_query("DROP TABLE IF EXISTS `roc_topic`");
	mysql_query("DROP TABLE IF EXISTS `roc_user`");
	mysql_query("DROP TABLE IF EXISTS `roc_whisper`");
	mysql_query("DROP TABLE IF EXISTS `roc_tag`");
	mysql_query("DROP TABLE IF EXISTS `roc_topic_tag_connection`");

	mysql_query("
		CREATE TABLE `roc_attachment` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uid` mediumint(8) unsigned NOT NULL,
		  `path` varchar(128) NOT NULL,
		  `time` int(11) unsigned NOT NULL,
		  `tid` int(11) unsigned NOT NULL DEFAULT '0',
		  `pid` int(11) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`,`uid`,`tid`,`pid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_favorite` (
		  `fid` mediumint(8) NOT NULL AUTO_INCREMENT,
		  `uid` mediumint(8) NOT NULL,
		  `tid` int(11) NOT NULL,
		  PRIMARY KEY (`fid`),
		  KEY `fuid` (`fid`),
		  KEY `id` (`tid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_floor` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `pid` int(11) unsigned NOT NULL,
		  `uid` mediumint(8) unsigned NOT NULL,
		  `content` varchar(120) NOT NULL,
		  `posttime` int(11) NOT NULL,
		  PRIMARY KEY (`id`,`pid`,`uid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_follow` (
		  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
		  `fuid` mediumint(8) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`uid`,`fuid`),
		  KEY `uid` (`uid`),
		  KEY `fuid` (`fuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_notification` (
		  `nid` mediumint(8) NOT NULL AUTO_INCREMENT,
		  `atuid` mediumint(8) NOT NULL,
		  `uid` mediumint(8) NOT NULL,
		  `tid` int(11) NOT NULL,
		  `pid` int(11) NOT NULL,
		  `fid` int(11) unsigned NOT NULL,
		  `isread` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0',
		  PRIMARY KEY (`nid`),
		  KEY `atuid` (`atuid`,`isread`,`nid`),
		  KEY `tid` (`tid`),
		  KEY `pid` (`pid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_praise` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
		  `tid` int(11) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`,`uid`,`tid`),
		  KEY `uid` (`uid`),
		  KEY `fuid` (`tid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_reply` (
		  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `tid` int(11) unsigned NOT NULL DEFAULT '0',
		  `uid` mediumint(8) NOT NULL,
		  `content` varchar(250) NOT NULL,
		  `client` varchar(16) NOT NULL,
		  `posttime` int(11) NOT NULL,
		  PRIMARY KEY (`pid`,`tid`,`uid`),
		  KEY `tid` (`tid`,`pid`),
		  KEY `uid` (`uid`,`pid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_score` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `uid` mediumint(8) unsigned NOT NULL,
		  `changed` smallint(6) NOT NULL,
		  `remain` mediumint(8) NOT NULL,
		  `type` tinyint(2) NOT NULL,
		  `time` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`id`,`uid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_tag` (
		  `tagid` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `tagname` varchar(16) NOT NULL,
		  `used` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`tagid`,`tagname`,`used`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_topic` (
		  `tid` int(11) NOT NULL AUTO_INCREMENT,
		  `uid` mediumint(8) NOT NULL,
		  `title` varchar(64) NOT NULL,
		  `content` text NOT NULL,
		  `comments` mediumint(8) NOT NULL DEFAULT '0',
		  `client` varchar(16) DEFAULT NULL,
		  `istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `islock` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `posttime` int(11) NOT NULL,
		  `lasttime` int(11) NOT NULL,
		  PRIMARY KEY (`tid`,`uid`,`title`),
		  KEY `uid` (`uid`,`tid`),
		  KEY `cid` (`lasttime`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_topic_tag_connection` (
		  `tid` int(11) unsigned NOT NULL,
		  `tagid` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`tid`,`tagid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_user` (
		  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		  `username` char(26) NOT NULL,
		  `email` char(36) NOT NULL,
		  `signature` varchar(32) NOT NULL,
		  `password` char(32) NOT NULL,
		  `regtime` int(11) NOT NULL,
		  `lasttime` int(11) NOT NULL,
		  `qqid` char(32) NOT NULL,
		  `scores` mediumint(8) unsigned NOT NULL,
		  `money` mediumint(8) unsigned NOT NULL,
		  `groupid` tinyint(2) unsigned NOT NULL DEFAULT '1',
		  PRIMARY KEY (`uid`),
		  UNIQUE KEY `nickname` (`username`),
		  KEY `email` (`email`),
		  KEY `qqid` (`qqid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("
		CREATE TABLE `roc_whisper` (
		  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		  `atuid` mediumint(8) unsigned NOT NULL,
		  `uid` mediumint(8) unsigned NOT NULL,
		  `content` varchar(255) NOT NULL,
		  `posttime` int(11) NOT NULL,
		  `isread` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `del_flag` mediumint(8) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `atuid` (`atuid`,`isread`,`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT");

	mysql_query("SET FOREIGN_KEY_CHECKS = 1;");

	mysql_query("INSERT INTO `roc_user` (username, email, signature, password, regtime, lasttime, qqid, scores, money, groupid) 
VALUES ('".$admin_name."', '".$admin_email."', '我是管理员，哈哈', '".md5($admin_password)."', '".time()."', '".time()."', '', '1000', '0', '9')");

	mysql_close($conn);

	$content = file_get_contents('application/config/config.php');

	$content = preg_replace('/\'database_name\' => .+?\,/s', '\'database_name\' => \'' . $name . '\',', $content);

	$content = preg_replace('/\'server\' => .+?\,/s', '\'server\' => \'' . $host . '\',', $content);

	$content = preg_replace('/\'username\' => .+?\,/s', '\'username\' => \'' . $user . '\',', $content);

	$content = preg_replace('/\'password\' => .+?\,/s', '\'password\' => \'' . $password . '\',', $content);

	file_put_contents('application/config/config.php', $content);

	$content = file_get_contents('index.php');

	$content = str_replace('define(\'ROOT\', \'/\');', 'define(\'ROOT\', \''.$path.'\');', $content);

	file_put_contents('index.php', $content);

	header('location: ./');
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>ROCBOSS 2.0安装文件</title>
<style type="text/css">
*{padding:0;margin:0;}
html,body{font:normal 12px 'Microsoft Yahei';color:#666;}
.install{width: 300px;margin:20px auto;}
#info{background:#353535;overflow:hidden;height:30px;color: #fff;text-align: center;line-height: 30px;}
#notice{background:#46A880;overflow:hidden;height:30px;color: #fff;text-align: center;line-height: 30px;}
p{height: 30px;line-height: 30px;font-weight: bold;}
.input{width:278px;border:1px solid #ccc;background:#fff;padding:10px;font:normal 12px 'Microsoft Yahei';outline:0;color:#222}
.input:focus{border:1px solid #555;}
.submit{border-radius:2px;text-align:center;border:none;padding:9px 15px;background:#333;cursor:pointer;font:bold 12px 'Microsoft Yahei';color:#fff;margin-top: 10px;}
</style>
 </head>
 <body>
 <div id="info">
 	当前系统环境：<?php if(PHP_OS=='WINNT'){echo('Windows');}else{echo(PHP_OS);}?>PHP <?=@PHP_VERSION?>/<?=@$_SERVER['SERVER_SOFTWARE']?>
</div>
<div id="notice">
	请确保系统满足运行环境要求：PHP >= 5.1 ; 需要开启 pdo_mysql 扩展 ; 需要开启 PATHINFO ; MySQL需要支持 InnoDB 引擎 ; 安装完后请删除本安装文件
</div>
 <div class="install">
	 <form method="post">
	 <p>数据库主机(默认无需修改)</p>
	 <input type="text" name="host" size="30" class="input" value="localhost" />
	 <p>数据库用户</p>
	 <input type="text" name="user" size="30"  class="input" value="root" />
	 <p>数据库密码</p>
	 <input type="text" name="password" size="30"  class="input"  value="root"/>
	 <p>数据库名</p>
	 <input type="text" name="name" size="30"  class="input" value="rocboss" />
	 <p>管理员昵称</p>
	 <input type="text" name="admin_name" size="30" class="input" value="admin" />
	 <p>管理员邮箱</p>
	 <input type="text" name="admin_email" size="30" class="input" value="admin@admin.com" />
	 <p>管理员密码</p>
	 <input type="text" name="admin_password" size="30" class="input" value="123456" />
	 <p>安全密匙KEY（请务必修改，不少于14位）</p>
	 <input type="text" name="safe_key" size="30" class="input" value="dg#i<?php echo rand(10000000, 99999999);?>k7" />
	 <div onclick="if(confirm('确定要安装吗?')){document.forms[0].submit()}" class="submit">开始安装</div>
	 <input type="hidden" name="path" size="30"  class="input" value="<?php echo str_replace('install.php','',$_SERVER['SCRIPT_NAME'])?>"/>
	 </form>
 </div>
 </body>
 </html>