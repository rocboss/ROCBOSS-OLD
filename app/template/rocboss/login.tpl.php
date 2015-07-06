<?php die('Access Denied');?>
{include('_part_header.tpl.php')}
<div class="member-bg">
{if $currentStatus == 'login'}
<div id="container">
    <form id="loginform" class="mem">
        <div class="mem-put">
            <div class="mem-t">
                <h3 class="mem-t-head">登录</h3>
                <label>昵称或邮箱</label>
                <input type="text" name="email" id="email" class="input" id="email"/>
                <label>密码</label>
                <input type="password" name="password" class="input" id="password"/>
                <div class="mem-put-bottom">
                <input type="button" name="submit" value="立即登录" id="login-submit" class="right btn btn-default"/>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="mem-tab">
            <div class="mem-t">
                <p>没有帐号？<a href="{$root}register">立即注册</a></p>
                <br>
                <p>忘记密码？<a href="{$root}resetPassword">立即找回</a></p>
                <br></br>
                <p>或使用QQ帐号登录</p><br>
                <a href="{$root}qqlogin" class="btn btn-default"><i class="icon icon-qq x2"></i> QQ帐号登录</a>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
{/if}

{if $currentStatus == 'register'}
<div id="container">
    <form id="joinform" class="mem">
        <div class="mem-put">
            <div class="mem-t">
                <h3 class="mem-t-head">注册帐号{if $join_switch == 0}<em>（暂不开放注册）</em>{/if}</h3>
                <label>邮箱</label>
                <input type="text" name="email" id="email" class="input" />
                <label>昵称</label>
                <input type="text" name="nickname" id="nickname" class="input" />
                <label>密码</label>
                <input type="password" name="password" class="input" id="password" />
                <label>确认密码</label>
                <input type="password" name="repassword" class="input" id="repassword" />
                <label>验证码</label>
                <input type="text" name="verify" id="verify" class="input" />
                <div class="mem-put-bottom">
                <input type="button" name="submit" value="注册" id="reg-submit" class="right btn btn-default"/>
                <img src="#" alt="" id="verify_image" title="点击更换">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="mem-tab">
            <div class="mem-t">
                <p>已有帐号？<a href="{$root}login">立即登录</a></p></br>
                <p>忘记密码？<a href="{$root}resetPassword">立即找回</a></p>
                <br></br>
                <p>或使用QQ帐号登录</p><br>
                <a href="{$root}qqlogin" class="btn btn-default"><i class="fa fa-qq"></i> QQ帐号登录</a>
            </div>
            
        </div>
        <div class="clear"></div>
    </form>
</div>
{/if}

{if $currentStatus == 'qqjoin'}
<div id="container">
    <form id="qqjoinform" class="mem">
        <div class="mem-put">
            <div class="mem-t">
                <h3 class="mem-t-head">QQ互联</h3>
                <div class="text-center avatar-layout">
                    <img src="{$QQArray.avatar}">
                </div>
                <label>用户名</label>
                <input type="text" class="input" id="username" name="username" autocomplete="off" value="{$QQArray.username}">
                <div class="mem-put-bottom">
                <input type="button" id="qqjoin_submit" class="right btn btn-default" value="确定用户名">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
{/if}

{if $currentStatus == 'resetPassword'}
<div id="container">
    <form id="resetform" class="mem">
        <div class="mem-put">
            <div class="mem-t">
                <h3 class="mem-t-head">找回密码 - {$sitename}</h3>
                <label>你的邮箱（请确保正确设置过）</label>
                <input type="text" name="email" id="email" class="input" id="email"/>
                <label>验证码</label>
                <input type="text" name="verify" id="verify" class="input" />
                <div class="mem-put-bottom">
                <input type="button" name="submit" value="立即找回" id="reset-submit" class="right btn btn-default"/>
                <img src="#" alt="" id="verify_image" title="点击更换">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="mem-tab">
            <div class="mem-t">
                <p>已有验证码？<a href="{$root}doReset">立即重置</a></p>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
{/if}

{if $currentStatus == 'doReset'}
<div id="container">
    <form id="resetform" class="mem">
        <div class="mem-put">
            <div class="mem-t">
                <h3 class="mem-t-head">重置密码 - {$sitename}</h3>
                <label>你的邮箱</label>
                <input type="text" name="email" class="input" id="email">
                <label>新密码</label>
                <input type="password" name="password" class="input" id="password" />
                <label>确认密码</label>
                <input type="password" name="repassword" class="input" id="repassword" />
                <label>验证码</label>
                <input type="text" name="code" class="input" id="code-reset">
                <div class="mem-put-bottom">
                <input type="button" name="submit" value="立即重置" id="doreset-submit" class="right btn btn-default"/>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="mem-tab">
            <div class="mem-t">
                <p>还木有收到验证码？<a href="{$root}resetPassword">立即找回</a></p>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
{/if}
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#verify_image").attr("src", root+"identifyImage/"+Math.random()).click(function(){
        $(this).attr("src", root+"identifyImage/"+Math.random());
    });
        
    $("#reg-submit").click(function() {
        if( $('#joinform #password').val() != $('#joinform #repassword').val() ){
            layer.msg("两次密码不一样");
            $("#joinform #repassword").focus();
            return false;
        }
        $('#reg-submit').attr("disabled", "disabled");
        $.post(root+"register", {
                "do": "register",
                "email": $('#joinform #email').val(),
                "nickname": $('#joinform #nickname').val(),
                "password": $('#joinform #password').val(),
                "verify": $('#joinform #verify').val(),
            }, function(data) {
                data = eval("(" + data + ")");
                if (data.result == "success") {
                    layer.msg("注册成功！即将转跳到登陆界面...");
                    window.setTimeout("window.location='"+root+"login'",1500); 
                } else {
                    layer.msg(data.message);
                    $('#reg-submit').removeAttr("disabled");
                    if( data.position == 1 ){
                        $("#joinform #email").focus();
                    }
                    if( data.position == 2 ){
                        $("#joinform #nickname").focus();
                    }
                    if( data.position == 3 ){
                        $("#joinform #password").focus();
                    }
                    if( data.position == 4 ){
                        $("#joinform #verify").focus();
                        $("#joinform #verify").val('');
                        $("#joinform #verify_image").click();
                    }
                }
            });
    });
    
    $("#joinform").keyup(function(event){
       if(event.keyCode == 13){
         $("#reg-submit").trigger("click");
       }
    });
    
    $("#loginform").keyup(function(event){
       if(event.keyCode == 13){
         $("#login-submit").trigger("click");
       }
    });
    $("#resetform").keyup(function(event){
       if(event.keyCode == 13){
         $("#reset-submit").trigger("click");
       }
    });
    $("#login-submit").click(function() {
        var as = ($.trim($("#loginform input[name=email]").val()).length >= 2) ? true : false;
        var ps = ($("#loginform input[name=password]").val().length >= 6) ? true : false;
        
        if( as && ps ){
            $('#login-submit').attr("disabled", "disabled");
            $.post(root+"login", {
                    "do": "login",
                    "email": $('#loginform #email').val(),
                    "password": $('#loginform #password').val(),
                }, function(data) {
                    data = eval("(" + data + ")");
                    if (data.result == "success") {
                        layer.msg("登录成功！即将转跳到首页...");
                        window.setTimeout("window.location='"+root+"'",1200); 
                    } else {
                        layer.msg(data.message);
                        $('#login-submit').removeAttr("disabled");
                        if( data.position == 1 ){
                            $("#email").focus();
                        }
                        if( data.position == 2 ){
                            $("#password").focus();
                        }
                    }
            });
        }else{
            if(!as){
                layer.msg("账号未填或无效");
                $("#email").focus();
            } else if(!ps){
                layer.msg("密码未填或无效");
                $("#password").focus();
            }
        }
        
    });
    $("#reset-submit").click(function() {
        $('#reset-submit').attr("disabled", "disabled");
        $.post(root+"resetPassword", {
                "do": "resetPassword",
                "email":  $('#resetform #email').val(),
                "verify": $('#resetform #verify').val(),
            }, function(data) {
                data = eval("(" + data + ")");
                if (data.result == "success") {
                    layer.msg(data.message);
                    window.setTimeout("window.location='"+root+"doReset';",1000);
                } else {
                    layer.msg(data.message);
                    $('#reset-submit').removeAttr("disabled");
                    if( data.position == 1 ){
                        $("#email").focus();
                    }
                    if( data.position == 2 ){
                        $("#verify").focus();
                    }
                }
        });     
    });
    $("#doreset-submit").click(function() {
        $('#doreset-submit').attr("disabled", "disabled");
        $.post(root+"doReset", {
                "email": $('#resetform #email').val(),
                "code": $('#resetform #code-reset').val(),
                "password":  $('#resetform #password').val(),
                "repassword": $('#resetform #repassword').val(),
            }, function(data) {
                data = eval("(" + data + ")");
                if (data.result == "success") {
                    layer.msg(data.message);
                    window.setTimeout("window.location='"+root+"login';",1000);
                } else {
                    layer.msg(data.message);
                    $('#doreset-submit').removeAttr("disabled");
                }
        });     
    });
    $("#qqjoin_submit").click(function (){
        $("#qqjoin_submit").val('正在提交...');
        $("#qqjoin_submit").attr("disabled", "disabled");
        $.post(root+"qqjoin", {
            "username": $("#qqjoinform #username").val(),
        }, function(data) {
            data = eval("(" + data + ")");
            if (data.result == "success") {
                layer.msg(data.message);
                window.setTimeout("window.location='"+root+"';",1000); 
            } else {
                layer.msg(data.message);
                $("#qqjoinform #username").focus();
                $("#qqjoin_submit").val('确定用户名');
                $("#qqjoin_submit").removeAttr("disabled");
            }
        });
    });

});
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
</script>
{include('_part_footer.tpl.php')}
