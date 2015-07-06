<?php die('Access Denied');?>
<section>
    <ol class="bz-breadcrumb">
        <li><a href="{$root}admin">管理中心</a></li>
        <li class="bz-active">系统设置</li>
    </ol>
    <div class="bz-panel bz-panel-default">
        <div class="bz-panel-hd">
            <h3 class="bz-panel-title">系统设置</h3>
        </div>
        <div class="bz-panel-bd">
            <form method="post" class="bz-form bz-form-aligned">
                <fieldset>
                    <div class="bz-control-group">
                        <label>站点标题：</label>
                        <input type="text" name="sitename" placeholder="请填写您的站点标题" value="{$sys.sitename}" size="40">
                    </div>
                    <div class="bz-control-group">
                        <label>站点关键字：</label>
                        <input type="text" name="keywords" placeholder="请填写的您的站点关键字，空格隔开" value="{$sys.keywords}" size="40">
                    </div>
                    <div class="bz-control-group">
                        <label>网站描述：</label>
                        <textarea name="description" rows="4" cols="60">{$sys.description}</textarea>
                    </div>
                    <div class="bz-control-group">
                        <label>系统秘钥：</label>
                        <input type="text" name="rockey" value="{$sys.rockey}" size="40"/> <em>请定期修改，不少于14位</em>
                    </div>
                    <div class="bz-control-group">
                        <label>注册开关：</label>
                        <select id="join_switch" name="join_switch">
                            <option value="1"{if $sys['join_switch'] == 1} selected{/if}>允许注册</option>
                            <option value="0"{if $sys['join_switch'] == 0} selected{/if}>禁止注册</option>
                        </select>
                    </div>
                    <div class="bz-control-group">
                        <label>主题模板：</label>
                        <select id="theme" name="theme">
                            {loop $tplName $name}
                            <option value="{$name}"{if $sys['theme'] == $name} selected{/if}>{$name}</option>
                            {/loop}
                        </select>
                    </div>

                    <div class="bz-control-group">
                        <label>注册积分：</label>
                        <input type="text" name="register" value="{$sys.scores_register}" size="4"/>
                            （正整数）
                    </div>
                    <div class="bz-control-group">
                        <label>创建主题：</label>
                        <input type="text" name="topic" value="{$sys.scores_topic}" size="4"/>
                            （正整数）
                    </div>
                    <div class="bz-control-group">
                        <label>创建回复：</label>
                        <input type="text" name="reply" value="{$sys.scores_reply}" size="4"/>
                           （正整数）
                    </div>
                    <div class="bz-control-group">
                        <label>主题被赞：</label>
                        <input type="text" name="praise" value="{$sys.scores_praise}" size="4"/>
                            （正整数）
                    </div>
                    <div class="bz-control-group">
                        <label>私信费用：</label>
                        <input type="text" name="whisper" value="{$sys.scores_whisper}" size="4"/>
                            （正整数）
                    </div>
                    <div class="bz-control-group">
                        <label>QQ登录APPID：</label>
                        <input type="text" name="appid" value="{$sys.appid}" size="40"/> <em>请去 <a href="http://connect.qq.com" target="_blank">http://connect.qq.com</a> 申请</em>
                    </div>
                    <div class="bz-control-group">
                        <label>QQ登录APPKEY：</label>
                        <input type="text" name="appkey" value="{$sys.appkey}" size="40"/>
                    </div>
                    <div class="bz-control-group">
                        <label>网站广告代码：</label>
                        <textarea name="ad" rows="6" cols="60">{$sys.ad}</textarea>
                    </div>
                    <input type="hidden" name="hash" value="{:md5($_COOKIE['roc_secure'])}"/>
                    <div class="bz-controls">
                        <button type="submit" class="bz-button bz-button-primary"><i class="iconfont icon-queren2"></i> 更新设置</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</section>
<div class="bz-panel bz-panel-default">
    <div class="bz-panel-hd">
        <h3 class="bz-panel-title">邮箱服务器相关设置 <em>（用于用户找回密码）</em></h3>
    </div>
    <div class="bz-panel-bd">
        <form method="post" class="bz-form bz-form-stacked">
            <fieldset>
                <label>邮件服务器smtp主机</label>
                <input type="text" name="smtp_server" placeholder="" class="bz-input-1" value="{$sys.smtp_server}">

                <label>邮件服务器端口 <em>（默认 25）</em></label>
                <input type="text" name="smtp_port" placeholder="" class="bz-input-1" value="{$sys.smtp_port}">

                <label>邮件服务器用户 <em>（你的发件邮箱）</em></label>
                <input type="text" name="smtp_user" placeholder="" class="bz-input-1" value="{$sys.smtp_user}">

                <label>邮件服务器密码</label>
                <input type="text" name="smtp_password" placeholder="" class="bz-input-1" value="{$sys.smtp_password}">

                <input type="hidden" name="hash" value="{:md5($_COOKIE['roc_secure'])}">
            </fieldset>
            <button type="submit" class="bz-button bz-button-primary"><i class="iconfont icon-queren2"></i> 更新设置</button>
        </form>
    </div>
</div>