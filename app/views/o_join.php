{$headerLayout}
<style type="text/css">
    #page-wrapper {
        min-height: 300px;
    }
    .o-join span {
      border: none;
    }
    .form-group {
      margin: 0;
    }
    .fc-button {
      background: #eee;
    }
    .fc-button.fc-state-active {
      background: #4B9DBD;
    }
</style>
<div class="middle-box text-center loginscreen  animated fadeInDown" style="min-height: 400px; padding: 50px 0 60px 0;">
  <div class="login-form">
    <div style="margin: 15px 0; font-size: 14px; color: #aaa;">
        <i class="fa fa-info-circle"></i> 请先绑定或注册账号
    </div>

    <div style="width: 100px; margin: 5px auto 20px;">
        <img src="{$avatar}" width="100" height="100" style="border-radius: 50%;"/>
    </div>

    <div class="o-join fc" style="margin: 20px auto; width: 150px;">
        <span class="fc-button fc-state-active join-type" data-join-type="new">注册新账号</span>
        <span class="fc-button join-type" data-join-type="bind">绑定账号</span>
        <div class="clearfix"></div>
    </div>

    <div id="new-join">
        <div class="form-group">
          <input type="hidden" name="newAccount" value="1"/> 
          <input type="hidden" name="avatar" value="{$avatar}"/> 
          <input type="text" class="form-control login-field" placeholder="请输入您的用户名" name="username" value="{$username}">
          <label class="login-field-icon fui-user" for="username"></label>
        </div>
        <div class="form-group">
          <input type="text" class="form-control login-field" placeholder="请输入您的邮箱" name="email">
          <label class="login-field-icon fui-mail" for="email"></label>
        </div>
        <div class="form-group">
          <input type="password" class="form-control login-field" placeholder="请输入不少于八位的密码" name="password">
          <label class="login-field-icon fui-lock" for="password"></label>
        </div>
        <a class="btn btn-primary btn-block join-btn">
          <i class="fa fa-paper-plane"></i> 注 册
        </a>
    </div>

    <div id="bind-join" style="display: none;">
        <div class="form-group">
          <input type="hidden" name="newAccount" value="0"/> 
          <input type="hidden" name="avatar" value="{$avatar}"/>
          <input type="hidden" name="email" value=""/>
          <input type="text" class="form-control login-field" placeholder="请输入用户名/邮箱" name="username"/>
          <label class="login-field-icon fui-user" for="username"></label>
        </div>
        <div class="form-group">
          <input type="password" class="form-control login-field" placeholder="请输入您的密码" name="password"/>
          <label class="login-field-icon fui-lock" for="password"></label>
        </div>
        <a class="btn btn-primary btn-block join-btn">
          <i class="fa fa-paper-plane"></i> 绑 定
        </a>
    </div>
    <div style="clear: both;"></div>
  </div>
</div>
{$footerLayout}
<script type="text/javascript">
    seajs.use("login", function(login) {
        login.o_join('{:Roc::request()->url}');
    });
</script>