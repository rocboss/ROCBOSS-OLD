<?php die('Access Denied');?>
<section>
    <ol class="bz-breadcrumb">
        <li><a href="{$root}admin">管理中心</a></li>
        <li class="bz-active">系统信息</li>
    </ol>
    <div class="bz-panel bz-panel-default">
        <div class="bz-panel-hd">
            <h3 class="bz-panel-title">欢迎来到管理中心</h3>
        </div>
        <div class="bz-panel-bd">
            您当前的身份 <code>{$loginInfo.username} ({$loginInfo.groupname})</code>
            <a href="{$root}">回到首页</a> | <a href="{$root}logout">注销</a>
        </div>
    </div>

    <div class="bz-panel bz-panel-default">
        <div class="bz-panel-hd">
            <h3 class="bz-panel-title">系统信息</h3>
        </div>
        <ul class="bz-listUI">
            <li style="border-top:none">服务器时间 : {$server.time}</li>
            <li>服务器端口 : {$server.port}</li>
            <li>服务器根域名 : {$server.name}</li>
            <li>服务器系统 : {$server.os}</li>
            <li>服务器引擎 : {$server.software}/PHP {$server.version}</li>
            <li>数据库版本 : MYSQL {:@mysql_get_server_info()}</li>
            <li>网站根目录 : {$server.root}</li>
            <li>最大上传值 : {$server.upload}</li>
            <li>当前占用内存 : {$server.memory_usage}</li>
            <li></li>
            <li>会员总数 : {$server.user_count}</li>
            <li>今日签到 : {$server.sign_count}</li>
            <li>签到会员 : {loop $signList $s}@{$s.username} {/loop}</li>
        </ul>
    </div>
</section>