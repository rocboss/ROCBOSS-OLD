<?php die('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$title}{$sitename}</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$description}">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><!--IE使用本身版本渲染 -->
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="renderer" content="webkit|ie-comp|ie-stand"><!--第三方webkit浏览器优先使用webkit -->
<meta name="apple-mobile-web-app-capable" content="yes"><!--全屏模式运行 -->
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="{$sitename}"> <!-- WEBAPP标题 -->
<link rel="apple-touch-icon-precomposed" href="{$tpl}apple-touch-icon-60x60.jpg" />
<link rel="stylesheet" type="text/css" href="{$css}common.css">
<link rel="stylesheet" type="text/css" href="{$css}icon.css">
<link rel="stylesheet" type="text/css" href="{$css}simditor.css" />
<script type="text/javascript" src="{$js}jquery.js"></script>
<script type="text/javascript" src="{$tpl}assets/layer/layer.js"></script>
<script type="text/javascript" src="{$js}common.js"></script>
{if $loginInfo['uid'] > 0}
	<link rel="stylesheet" type="text/css" href="{$css}rebox.css">
	<script type="text/javascript" src="{$js}ImageUpload.js"></script>
	<script type="text/javascript" src="{$js}module.js"></script>
	<script type="text/javascript" src="{$js}hotkeys.js"></script>
	<script type="text/javascript" src="{$js}simditor.js"></script>
	<script type="text/javascript" src="{$js}post.js"></script>
	<script type="text/javascript" src="{$js}rebox.js"></script>
{/if}
<!--[if lt IE 9]>
	<script src="{$js}html5.js"></script>
	<script src="{$js}css3.js"></script>
<![endif]-->
<script type="text/javascript">
	var root = "{$root}";
	var login_uid = "{$loginInfo.uid}";
	var login_groupid = "{$loginInfo.groupid}";
</script>
<!-- 百度统计 -->
<script>
	var _hmt = _hmt || [];
	(function() {
	  var hm = document.createElement("script");
	  hm.src = "//hm.baidu.com/hm.js?48042604b3c7a9973810a87540843e34";
	  var s = document.getElementsByTagName("script")[0]; 
	  s.parentNode.insertBefore(hm, s);
	})();
</script>
</head>
<body>
<div class="header fixed">
	<div class="main-outlet">
		<h1>
			<a class="logo-mobile" href="{$root}"><i class="icon icon-brandfill x7"></i></a>
			<a class="logo" href="{$root}">{$sitename}</a>
		</h1>
		<div class="nav">
			{if $loginInfo['uid'] > 0}
				<a href="{$root}user/" class="btn-circle" tip-title="我的主页"><img src="{$loginInfo.avatar}" alt="{$loginInfo.username}" id="myAvatar" style="width:30px; border-radius: 50%;"></a>
				<a href="{$root}notification/" class="btn-circle" tip-title="提醒"><i class="icon icon-notice x2"></i>{if $mine['notification'] > 0}<span class="danger">{$mine.notification}<span>{/if}</a>
				<a href="{$root}whisper/" class="btn-circle" tip-title="私信"><i class="icon icon-message x2"></i>{if $mine['whisper'] > 0}<span class="danger">{$mine.whisper}<span>{/if}</a>
				<a href="{$root}setting/" class="btn-circle" tip-title="设置"><i class="icon icon-settings x2"></i></a>
			{if $loginInfo['groupid'] == 9}
				<a href="{$root}admin/" class="btn-circle" tip-title="后台管理中心"><i class="icon icon-home x2"></i></a>
			{/if}
				<a href="{$root}logout/" class="btn-circle" tip-title="退出登录"><i class="icon icon-square x2"></i></a>
			{else}
				<a href="{$root}register/" class="btn-circle" tip-title="注册">R</a>
				<a href="{$root}login/" class="btn-circle" tip-title="登陆">L</a>
				<a href="{$root}qqlogin/" class="btn-circle" tip-title="使用QQ一键登录"><i class="icon icon-qq x2"></i></a>
			{/if}
		</div>
		<div class="clear"></div>
	</div>
</div>
