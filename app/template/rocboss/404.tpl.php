<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$sitename}</title>
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$description}">
<link rel="stylesheet" type="text/css" href="{$css}common.css">
<link rel="stylesheet" type="text/css" href="{$css}icon.css">
</head>
<body>
<div class="header fixed">
    <div class="main-outlet">
        <h1>
            <a class="logo" href="{$root}">{$sitename}</a>
        </h1>
        <div class="nav">
            <span class="search-logo">
                <i class="icon icon-search x2"></i>
                <input id="searchWord" type="text" placeholder="回车以搜索主题"/>
                <input onclick="javascript:search();" id="searchWord_submit" type="button" style="display:none;"/>
            </span>
            
        </div>
        <div class="clear"></div>
    </div>
</div>

<div id="tip"></div>
<div id="container">
    <div class="main-outlet container">
        <div class="content" style="width: 100%;">
            <h4 class="nav-head" style="text-align: center; font-size: 22px;">
                哎呀，发生了一个 404 错误 
            </h4>
            <div style="padding: 100px 0; min-height:300px; display: block; font-size: 20px; text-align: center;">
                <p>抱歉！您查看的页面可能不存在或者已经永久性迁移了</p>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
{include('_part_footer.tpl.php')}