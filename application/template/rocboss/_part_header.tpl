<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><!--{$seo['title']}--></title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta name="keywords" content="<!--{$seo['keywords']}-->">
<meta name="description" content="<!--{$seo['description']}-->">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><!--IE使用本身版本渲染 -->
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="renderer" content="webkit|ie-comp|ie-stand"><!--第三方webkit浏览器优先使用webkit -->
<meta name="apple-mobile-web-app-capable" content="yes"><!--全屏模式运行 -->
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<!--{$seo['title']}-->"> <!-- WEBAPP标题 -->
<link rel="apple-touch-icon-precomposed" href="<!--{ROOT}-->application/template/rocboss/apple-touch-icon-60x60.jpg" />
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/common.css">
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/icon.css">
<script src="<!--{ROOT}-->application/template/rocboss/js/jquery.js"></script>
<script src="<!--{ROOT}-->application/template/rocboss/js/common.js"></script>
<!--{if $loginInfo['uid'] > 0}-->
	<script src="<!--{ROOT}-->application/template/rocboss/js/LocalResizeIMG.js"></script>
	<script src="<!--{ROOT}-->application/template/rocboss/js/mobileBUGFix.mini.js"></script>
<!--{/if}-->
<!--[if lt IE 9]>
	<script src="<!--{ROOT}-->application/template/rocboss/js/html5.js"></script>
	<script src="<!--{ROOT}-->application/template/rocboss/js/css3.js"></script>
<![endif]-->
<script type="text/javascript">
	var root = "<!--{ROOT}-->";
	var login_uid = "<!--{$loginInfo['uid']}-->";
	var login_groupid = "<!--{$loginInfo['groupid']}-->";
</script>
</head>
<body>
<div class="header fixed">
	<div class="main-outlet">
		<h1>
			<a class="logo" href="<!--{ROOT}-->"><!--{$GLOBALS['sys_config']['sitename']}--></a>
		</h1>
		<div class="mobile-m" onclick="javascript:$('.nav').toggle(300);">
			<i class="icon icon-my x4"></i>
		</div>
		<div class="nav">
			<span class="search-logo">
				<i class="icon icon-search x2"></i>
				<input id="searchWord" type="text" placeholder="回车以搜索主题"  onKeypress= "javascript:if(event.keyCode==13) $('#searchWord_submit').click();"/>
				<input onclick="javascript:search();" id="searchWord_submit" type="button" style="display:none;"/>
			</span>
			<!--{if $loginInfo['uid'] > 0}-->
				<a href="<!--{ROOT}-->user"><i class="icon icon-my x2"></i> 会员中心</a>
				<a href="<!--{ROOT}-->user/notification/"><i class="icon icon-notice x2"></i> 提醒</a>
				<a href="<!--{ROOT}-->user/whisper/"><i class="icon icon-comment x2"></i> 私信</a>
				<a href="<!--{ROOT}-->setting/"><i class="icon icon-settings x2"></i> 设置</a>
			<!--{if $loginInfo['groupid'] == 9}-->
				<a href="<!--{ROOT}-->admin/">管理中心</a>
			<!--{/if}-->
				<a href="<!--{ROOT}-->user/logout/">退出</a>
			<!--{else}-->
				<a href="<!--{ROOT}-->user/register/">注册</a>
				<a href="<!--{ROOT}-->user/login/">登录</a>
				<a href="<!--{ROOT}-->user/qqlogin/" class="btn btn-default qq xs-hid"><i class="icon icon-qq x2"></i> QQ登录</a>
				<a href="<!--{ROOT}-->user/qqlogin/" class="xs-show"><i class="icon icon-qq x2"></i> QQ登录</a>
			<!--{/if}-->
		</div>
		<div class="clear"></div>
	</div>
</div>

<div id="tip"></div>