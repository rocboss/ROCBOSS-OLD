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
                <div class="col-md-6 col-sm-12 col-xs-12">
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
                              <div class="form-group margin-t-15">
                                  {if Roc::get('qq.switch')}
                                  <a href="/login/qq" class="btn btn-block btn-social btn-linkedin"><i class="fa fa-qq"></i> 腾讯QQ一键登录</a>
                                  {/if}
                                  {if Roc::get('weibo.switch')}
                                  <a href="/login/weibo" class="btn btn-block btn-social btn-google"><i class="fa fa-weibo"></i>新浪微博一键登录</a>
                                  {/if}
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
