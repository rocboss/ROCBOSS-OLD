    {$headerLayout}
    <script src="//api.geetest.com/get.php?callback=initCaptcha"></script>
    <div class="content-wrapper">
      <section class="content-header">
        <ol class="breadcrumb">
          <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
          <li class="active">登录</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="nav-tabs-custom login-form text-center">
                  <div class="nav">
                      <h4>
                          用户登录
                      </h4>
                  </div>
                  <div class="tab-content">
                      <form class="m-t">
                          <div class="form-group">
                              <input id="account" type="account" class="form-control" placeholder="用户名\邮箱">
                          </div>
                          <div class="form-group">
                              <input id="password" type="password" class="form-control" placeholder="密码" onkeypress="javascript:if(event.keyCode==13) $('#login-btn').click();">
                          </div>
                          <div class="form-group" id="geetest-captcha"></div>
                          <div class="form-group">
                              <button id="login-btn" class="btn bg-olive btn-block">登 录</button>
                          </div>
                          <div class="text-muted text-center margin-t-5">
                              <a href="/forget-password"><small>忘记密码？</small></a> |
                              <a href="/register"><small>注册新账号</small></a>
                          </div>
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
