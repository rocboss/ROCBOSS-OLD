<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{if (!empty($pageTitle))}{$pageTitle} - {/if}{$seo.sitename}</title>
    <meta name="keywords" content="{$seo.keywords}">
    <meta name="description" content="{$seo.description}">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta name="_csrf" content="{:md5(Roc::request()->cookies->roc_secure)}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="icon" href="{:'/'.Roc::get('system.views.path').'/'}img/favicon.ico" mce_href="{:'/'.Roc::get('system.views.path').'/'}img/favicon.ico" type="image/x-icon">
    <link href="{:'/'.Roc::get('system.views.path').'/'}css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}font-awesome/css/font-awesome.css?v=4.3.0" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}iconfont/iconfont.css" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}css/wangEditor.css?v=1.3.12" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}css/jquery.fancybox.css?v=2.1.5" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}css/github-gist.css" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}css/animate.css" rel="stylesheet">
    <link href="{:'/'.Roc::get('system.views.path').'/'}css/base.css?v=2.2.0" rel="stylesheet">
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
    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 text-navy " style="font-size: 20px;" href="/">
                            {$seo.sitename}
                        </a>
                        <form role="search" class="navbar-form-custom" action="/search">
                            <div class="form-group">
                                <input type="text" placeholder="请输入您需要查找的主题..." class="form-control" name="q" id="top-search"/>
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right pull-right count-info">

                        {if ($loginInfo['uid'] > 0)}
                        <li>
                            <a href="/user" title="{$loginInfo.username}" style="padding: 15px 5px; line-height: 30px;">
                                <img alt="image" id="my-avatar" class="img-circle profile-small-avatar" src="{$loginInfo.avatar}">
                                <span class="mobile-hide">{$loginInfo.username}</span>
                            </a>
                        </li>
                        <li>
                            <a href="/notice" class="dropdown-toggle">
                                <i class="fa fa-bell-o header-ico"></i>
                                {if ($loginInfo['whisper_num']+$loginInfo['notice_num'] > 0)}
                                    <span>{:$loginInfo['whisper_num']+$loginInfo['notice_num']}</span>
                                {/if}
                            </a>
                        </li>
                        <li>
                            <a href="/logout">
                                <i class="fa fa-sign-out header-ico"></i>
                            </a>
                        </li>
                        {else}
                        <li>
                            <div class="user-login">
                                <a class="btn btn-primary btn-xs" href="/login">登录</a>
                                    <span class="or">or</span>
                                <a class="btn btn-success btn-xs" href="/register">注册</a>
                            </div>
                        </li>
                        {/if}
                    </ul>
                </nav>
            </div>
