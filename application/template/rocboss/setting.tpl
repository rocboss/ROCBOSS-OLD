<!--{include _part_header.tpl}-->
<div id="container">
<div class="main-outlet container">
    <div class="content left">

    <!--{if $settingType == 'password'}-->
    <h2 class="nav-head">密码安全</h2>
    <form id="password-form">
    <dl class="set-form">
      <dt class="input-group">
        <p class="input">当前密码:<input type="password"  id="password" name="password" placeholder="若无请留空"></p>
        <p class="input">新密码:<input type="password"  id="newPassword" name="newPassword" placeholder=""></p>
        <p class="input">确认密码:<input type="password"  id="reNewPassword" name="reNewPassword" placeholder=""></p>
        <input type="button" id="password-setting-button" class="btn btn-default mt10" onclick="javascrpit:setPassword();" value="保存">
      </dt>
    </dl>
    </form>
    <!--{/if}--> 

    <!--{if $settingType == 'signature'}-->
    <h2 class="nav-head">个性签名</h2>
    <form id="signature-form">
    <dl class="set-form">
      <dt class="input-group">
        <p class="form-control">
          <textarea id="signature" name="signature"><!--{$userInfo['signature']}--></textarea>
        </p>
        <input type="button" id="signature-setting-button" class="btn btn-default mt10" onclick="javascrpit:setSignature();" value="保存">
      </dt>
    </dl>
    </form>
    <!--{/if}--> 

    <!--{if $settingType == 'email'}-->
    <h2 class="nav-head">邮箱设置</h2>
    <form id="email-form">
    <dl class="set-form">
      <dt class="input-group">
        <!--{if $userInfo['password'] == ""}-->
        <p class="set-tip">请先设置<a href="<!--{ROOT}-->setting/index/type/password/">个人密码</a>!</p>
        <!--{else}-->
        <p class="input">邮箱地址:
        <input type="text" id="email" name="email" placeholder="为登录账号，不会公开显示"  value="<!--{$userInfo['email']}-->">
        </p>
        <p class="input">当前密码:
        <input type="password" id="password" name="password" placeholder="">
        </p>
        <p>
        <input type="button" id="email-setting-button" class="btn btn-default mt10" onclick="javascrpit:setEmail();" value="保存">
        </p>
        <!--{/if}-->
      </dt>
    </dl>
    </form>
    <!--{/if}-->

    <!--{if $settingType == 'avatar'}-->
    <h2 class="nav-head">修改头像 (推荐尺寸 200 x 200)</h2>
    <form id="avatar-form" style="text-align:center">
    <dl class="set-form">
      <dt class="input-group">
        <p>
          <img src="<!--{$userInfo['avatar']}-->" class="avatar-now" width="100" height="100">
        </p>
        <div style="position: relative;cursor: pointer; overflow: hidden; width: 35px; margin: 0 auto;">
          <i class="icon icon-camerafill x6"></i>
          <input type="file" name="upfile" id="post-avatar" accept="image/*" style="opacity: 0; left: 0;top: 0;bottom: 0;margin: 0; position: absolute; width: 35px;" />
        </div>
      </dt>
    </dl>
    </form>
    <!--{/if}-->

  </div>
  <div class="side left">
    <div class="box">
      <ul class="list-topic">
        <li<!--{if $settingType == 'avatar'}--> class="active"<!--{/if}-->>
          <a href="<!--{ROOT}-->setting/index/type/avatar/">修改头像</a>
        </li>

        <li<!--{if $settingType == 'signature'}--> class="active"<!--{/if}-->>
          <a href="<!--{ROOT}-->setting/index/type/signature/">个性签名</a>
        </li>

        <li<!--{if $settingType == 'email'}--> class="active"<!--{/if}-->>
          <a href="<!--{ROOT}-->setting/index/type/email/">邮箱设置</a>
        </li>

        <li<!--{if $settingType == 'password'}--> class="active"<!--{/if}-->>
          <a href="<!--{ROOT}-->setting/index/type/password/">密码安全</a>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="clear"></div>
</div>
<script src="<!--{ROOT}-->application/template/rocboss/js/post.js"></script>
<!--{include _part_footer.tpl}--> 
