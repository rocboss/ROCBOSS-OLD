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
                <div class="col-md-6 col-sm-12 col-xs-12">
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
                            <div class="form-group margin-t-15">
                                <a href="/login/qq" class="btn btn-block btn-social btn-linkedin"><i class="fa fa-qq"></i> 腾讯QQ一键登录</a>
                                <a href="/login/weibo" class="btn btn-block btn-social btn-google"><i class="fa fa-weibo"></i>新浪微博一键登录</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {if Roc::get('wx.switch')}
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div id="wx-login"></div>
            </div>
            {/if}
          </div>
        </div>
       </section>
    </div>
    <script type="text/javascript">
        var captcha = {
            isOpen: {:(Roc::get('geetest.switch') ? 1 : 0)},
            success: {$captcha.success},
            geetest: "{$captcha.geetest}",
            challenge: "{$captcha.challenge}",
        };
        var params = {
            wxSwitch: {:(Roc::get('wx.switch') ? 1 : 0)},
            wxAppId: '{:Roc::get('wx.appId')}',
            wxRedirectUri: '{:(Roc::request()->secure ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/register/weixin'}',
            theme: '{:$theme == 'black' ? 'white' : 'black'}',
        };
    </script>
    <script src="http://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
    {$footerLayout}
</body>
</html>
