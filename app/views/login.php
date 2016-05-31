    {$headerLayout}
    <style type="text/css">
        #page-wrapper {
            min-height: 300px;
        }
    </style>
    <script src="//api.geetest.com/get.php?callback=initCaptcha"></script>
    <div class="middle-box text-center loginscreen  animated fadeInDown" style="min-height: 400px; padding: 50px 0 60px 0;">
        <div style="width: 260px; margin-left: 25px;">
            <div style="height: 120px; background: #4B9DBD; padding: 20px;">
                <img src="{:'/'.Roc::get('system.views.path').'/'}img/logo.png" width="80" height="80"/>
            </div>

            <form class="m-t">
                <div class="form-group">
                    <input id="account" type="account" class="form-control" placeholder="用户名\邮箱">
                </div>
                <div class="form-group">
                    <input id="password" type="password" class="form-control" placeholder="密码" onkeypress="javascript:if(event.keyCode==13) $('#login-btn').click();">
                </div>
                {if Roc::get('geetest.switch')}
                <div class="form-group" id="geetest-captcha"></div>
                {/if}
                <a id="login-btn" class="btn btn-primary block full-width m-b">登 录</a>

                <p class="text-muted text-center">
                    <a href="/forget-password"><small>忘记密码？</small></a> |
                    <a href="/register">注册一个新账号</a>
                </p>
            </form>
            <p style="margin-top: 30px;">
                <a href="/login/qq" class="oauth-login qq"><i class="fa fa-qq"></i></a>
                <a href="/login/weibo" class="oauth-login weibo"><i class="fa fa-weibo"></i></a>
            </p>
            <p style="margin-top: 30px; color: #ccc;">
                <small><i class="fa fa-info-circle"></i> 由于Geetest在https请求下存在缺陷，若在Chrome等浏览器下无法正常显示滑动验证码，请切换回http浏览。</small>
            </p>
        </div>
    </div>
    {$footerLayout}
    <script type="text/javascript">
        seajs.use("login", function(login) {
            login.login({
                isOpen: {:(Roc::get('geetest.switch') ? 1 : 0)},
                success: {$captcha.success},
                geetest: "{$captcha.geetest}",
                challenge: "{$captcha.challenge}",
            });
        });
    </script>
</body>
</html>
