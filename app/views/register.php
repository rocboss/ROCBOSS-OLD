    {$headerLayout}
    <script src="//api.geetest.com/get.php?callback=initCaptcha"></script>
    <div class="content-wrapper">
      <section class="content-header">
        <ol class="breadcrumb">
          <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
          <li class="active">注册</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="nav-tabs-custom login-form text-center">
                  <div class="nav">
                      <h4>
                          注册新账号
                      </h4>
                  </div>
                  <div class="tab-content">

                    <form class="m-t">
                        <div class="form-group">
                          <input type="text" class="form-control login-field" placeholder="请输入您的用户名" id="register-username" name="username">
                        </div>

                        <div class="form-group">
                          <input type="text" class="form-control login-field" placeholder="请输入您的邮箱" id="register-email" name="email">
                        </div>

                        <div class="form-group">
                          <input type="password" class="form-control login-field" placeholder="请输入不少于八位的密码" id="register-password" name="password">
                        </div>

                        <div class="form-group">
                          <input type="password" class="form-control login-field" placeholder="请再次输入密码" id="register-repassword" name="repassword">
                        </div>

                        <div class="form-group" id="geetest-captcha"></div>
                        <div class="form-group">
                            {if Roc::get('system.register.switch')}
                            <button id="register-btn" class="btn bg-olive btn-block">注 册</button>
                            {else}
                            <button id="register-btn" class="btn bg-olive btn-block" disabled="disabled">暂不开放注册</button>
                            {/if}
                        </div>

                        <p class="text-muted text-center">
                            <a href="/forget-password"><small>忘记密码？</small></a> |
                            <a href="/register"><small>立即登录</small></a>
                        </p>

                        <div class="form-group margin-t-15">
                            <a href="/login/qq" class="btn btn-block btn-social btn-linkedin"><i class="fa fa-qq"></i> 腾讯QQ一键登录</a>
                            <a href="/login/weibo" class="btn btn-block btn-social btn-google"><i class="fa fa-weibo"></i>新浪微博一键登录</a>
                        </div>
                    </form>
                    <p style="margin-top: 30px; color: #aaa;">
                        <small><i class="fa fa-info-circle"></i> 由于普通Geetest账号不支持https请求，若在浏览器下无法正常显示滑动验证码，请切换回http浏览。</small>
                    </p>
                </div>
            </div>
          </div>
        </div>
       </section>
    </div>
    {$footerLayout}
    <script type="text/javascript">
        seajs.use("js/login", function(login) {
            login.register({
                isOpen: {:(Roc::get('geetest.switch') ? 1 : 0)},
                success: {$captcha.success},
                geetest: "{$captcha.geetest}",
                challenge: "{$captcha.challenge}",
            });
        });
    </script>
</body>
</html>
