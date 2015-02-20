<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>ROCBOSS后台管理中心</title>
<meta http-equiv="pragma" content="no-cache"/>
<meta http-equiv="cache-control" content="no-cache"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<!--IE使用本身版本渲染 -->
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0"/>
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/admin.css" media="all"/>
<!--[if lt IE 9]>
    <script src="<!--{ROOT}-->application/template/rocboss/js/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="<!--{ROOT}-->application/template/rocboss/js/jquery.js"></script>
<script type="text/javascript" src="<!--{ROOT}-->application/template/rocboss/js/manage.js"></script>
<script type="text/javascript">
    var root='<!--{ROOT}-->';
</script>
</head>
<body>
<header>
<nav>
您好, <a href="<!--{ROOT}-->user/t/<!--{$loginInfo['username']}-->"><!--{$loginInfo['username']}--></a>
</nav>
<a href="<!--{ROOT}-->" title="控制台">返回首页</a>
</header>
<menu>
<ul>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/system/">
            <div class="admin-menu-name">
                <i class="icon icon-yingpan fa"></i>服务器信息
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/common/">
            <div class="admin-menu-name">
                <i class="icon icon-shezhi fa"></i>通用设置
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/topic/">
            <div class="admin-menu-name">
                <i class="icon icon-wenben fa"></i>帖子管理
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/reply/">
            <div class="admin-menu-name">
                <i class="icon icon-wenben fa"></i>回复管理
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/tag/">
            <div class="admin-menu-name">
                <i class="icon icon-wenben fa"></i>标签管理
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/user/">
            <div class="admin-menu-name">
                <i class="icon icon-yonghu fa"></i>会员管理
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/link/">
            <div class="admin-menu-name">
                <i class="icon icon-fujian fa"></i>链接管理
            </div>
        </a>
    </li>
    <li>
        <a class="admin-menu" href="<!--{ROOT}-->admin/index/type/clear/">
            <div class="admin-menu-name">
                <i class="icon icon-qingchu fa"></i>系统清理
            </div>
        </a>
    </li>
</ul>
</menu>