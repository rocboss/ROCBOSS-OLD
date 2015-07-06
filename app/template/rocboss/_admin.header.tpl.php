<?php die('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}{$sitename}</title>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta http-equiv="Cache-control" content="no-cache, must-revalidate"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0"/>
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="{$tpl}admin/css/base.css">
    <link rel="stylesheet" type="text/css" href="{$tpl}admin/css/iconfont.css">
    <!--[if lt IE 9]>
        <script src="{$js}html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="{$js}jquery.js"></script>

    <script type="text/javascript" src="{$tpl}assets/layer/layer.js"></script>
    <script type="text/javascript" src="{$js}manage.js"></script>
    <script type="text/javascript">
        var root = '{$root}';
    </script>
</head>
<body>
<nav class="top-head">
    <ul class="bz-nav bz-nav-pills container">
        <div class="bz-logo">
            <a href="{$root}" title="回到首页"><i class="iconfont icon-yingyongzhongxin x4"></i></a> ROCBOSS
        </div>
        <li{if $type == 'system'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/system"><i class="iconfont icon-style"></i> 系统信息</a>
        </li>
        <li{if $type == 'common'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/common"><i class="iconfont icon-iconfontxitongshezhi"></i> 系统设置</a>
        </li>
        <li{if $type == 'topic'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/topic"><i class="iconfont icon-up-posts"></i> 帖子管理</a>
        </li>
        <li{if $type == 'reply'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/reply"><i class="iconfont icon-pinglunhuifu"></i> 回复管理</a>
        </li>
        <li{if $type == 'user'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/user"><i class="iconfont icon-login"></i> 用户管理</a>
        </li>
        <li{if $type == 'tag'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/tag"><i class="iconfont icon-biaoqian"></i> 标签管理</a>
        </li>
        <li{if $type == 'link'} class="bz-nav-active"{/if}>
            <a href="{$root}admin/link"><i class="iconfont icon-lianjie"></i> 链接管理</a>
        </li>
        <li class="bz-nav-has-children bz-nav-allow-hover">
            <a href="" class="bz-nav-link"><i class="iconfont icon-qingchuhuancun"></i> 系统清理</a>
            <ul class="bz-nav-children bz-nav">
                <li><a href="{$root}admin/ClearCache/template">清除模板缓存</a></li>
                <li><a href="{$root}admin/ClearCache/attachment">清除过期附件</a></li>
                <li><a href="{$root}admin/ClearCache/score">清除冗余数据</a></li>
            </ul>
        </li>
    </ul>
</nav>
{if isset($code)}
    <script type="text/javascript">
        layer.msg('{$code}');
    </script>
{/if}
<div class="container">
