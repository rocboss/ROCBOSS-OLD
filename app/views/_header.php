<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{if (!empty($pageTitle))}{$pageTitle} - {/if}{$seo.sitename}</title>
  <meta name="keywords" content="{if (!empty($keywords))}{$keywords}{else}{$seo.keywords}{/if}">
  <meta name="description" content="{if (!empty($description))}{$description}{else}{$seo.description}{/if}">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="_csrf" content="{:md5(Roc::request()->cookies->roc_secure)}">
  <meta name="google-site-verification" content="8fNfY32dXDtsbgiPjp2qYhEXT9I0ADhB68VUi5G8Ync" />
  <link rel="icon" href="/dist/img/favicon.ico" mce_href="/dist/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">
  {if Roc::get('system.webpack_debug')}
  <link rel="stylesheet" href="/vendor/iconfont/iconfont.css">
  <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="/src/css/jquery.fancybox.css?v=2.1.5">
  <link rel="stylesheet" href="/src/css/wangEditor.css?v=1.3.12">
  <link rel="stylesheet" href="/src/css/github-gist.css">
  <link rel="stylesheet" href="/src/css/animate.css">
  {else}
  <link rel="stylesheet" href="/dist/css/{$asset}.min.css">
  {/if}
  <link rel="stylesheet" href="/src/css/base_{$theme}.css?v=2210057">

  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body id="rocboss-app" class="hold-transition layout-boxed">
<div class="wrapper">
    <header class="main-header">
      <nav class="navbar navbar-static-top">
        <div class="container">
          <div class="navbar-header">
            <a href="/" class="navbar-brand"><b>{$seo.sitename}</b></a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
              <i class="fa fa-bars"></i>
            </button>
          </div>

          <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
              <li {if substr($active, 0, 5) == 'index'}class="active"{/if}><a href="/">社区</a></li>
              <li {if $active == 'article'}class="active"{/if}><a href="/article">文章</a></li>
              <li {if $active == 'ask'}class="active"{/if}><a href="https://ask.luoke.io/" target="_blank">语音问答</a></li>
            </ul>
            <form class="navbar-form navbar-left" role="search" action="/search">
              <div class="form-group">
                <input type="text" class="form-control" id="navbar-search-input" placeholder="请输入您需要查找的主题..." name="q">
              </div>
            </form>
          </div>

          <ul class="nav navbar-top-links navbar-right pull-right profile-info">
                {if ($loginInfo['uid'] > 0)}
                <li class="dropdown{if $active == 'user'} active{/if}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img alt="image" id="my-avatar" class="img-circle img-responsive profile-small-avatar header-avatar" src="{$loginInfo.avatar}" title="{$loginInfo.username}" data-toggle="tooltip" data-placement="bottom">
                        {if ($loginInfo['whisper_num']+$loginInfo['notice_num'] > 0)}
                            <span class="label label-danger unread-msg">{:$loginInfo['whisper_num']+$loginInfo['notice_num']}</span>
                        {/if}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="/user">个人中心</a></li>
                      <li><a href="/notice">未读消息<span class="label label-danger pull-right">{:$loginInfo['whisper_num']+$loginInfo['notice_num']}</span></a></li>
                      <li><a href="/setting">设置</a></li>
                      {if ($loginInfo['groupid'] == 99)}
                      <li><a href="/admin">后台</a></li>
                      {/if}
                      <li class="divider"></li>
                      <li><a href="/logout">退出</a></li>
                    </ul>
                </li>
                {else}
                <li>
                    <div class="user-login">
                        <a class="btn btn-gray btn-sm" href="/login">登录</a>
                        <span class="or">or</span>
                        <a class="btn btn-gray btn-sm" href="/register">注册</a>
                    </div>
                </li>
                {/if}
          </ul>
        </div>
      </nav>
    </header>
