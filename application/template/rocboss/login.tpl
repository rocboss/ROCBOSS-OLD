<!--{include _part_header.tpl}-->
<div class="member-bg">
<!--{if $currentStatus == 'login'}-->
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
				没有帐号？<a href="<!--{ROOT}-->user/register">立即注册</a><br></br><br></br>
				<p>或使用QQ帐号登录</p><br>
				<a href="<!--{ROOT}-->user/qqlogin" class="btn btn-default"><i class="icon icon-qq x2"></i> QQ帐号登录</a>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
<!--{/if}-->

<!--{if $currentStatus == 'register'}-->
<div id="container">
	<form id="joinform" class="mem">
		<div class="mem-put">
			<div class="mem-t">
				<h3 class="mem-t-head">注册帐号 [暂不开放]</h3>
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
				已有帐号？<a href="<!--{ROOT}-->user/login">立即登录</a><br></br><br></br>
				<p>或使用QQ帐号登录</p><br>
				<a href="<!--{ROOT}-->user/qqlogin" rel="nofollow" class="btn btn-default"><i class="fa fa-qq"></i> QQ帐号登录</a>
			</div>
			
		</div>
		<div class="clear"></div>
	</form>
</div>
<!--{/if}-->

<!--{if $currentStatus == 'qqjoin'}-->
<div id="container">
	<form id="qqjoinform" class="mem">
		<div class="mem-put">
			<div class="mem-t">
				<h3 class="mem-t-head">QQ互联</h3>
				<div class="text-center avatar-layout">
					<img src="<!--{$QQArray['avatar']}-->">
				</div>
				<label>用户名</label>
				<input type="text" class="input" id="username" name="username" autocomplete="off" value="<!--{$QQArray['username']}-->">
				<div class="mem-put-bottom">
				<input type="button" id="qqjoin_submit" class="right btn btn-default" value="确定用户名">
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
<!--{/if}-->

<!--{if $currentStatus == 'resetPassword'}-->
<div id="container">
	<form id="resetform" class="mem">
		<div class="mem-put">
			<div class="mem-t">
				<h3 class="mem-t-head">找回密码-<!--{SITENAME}--></h3>
				<label>你的邮箱（请确保设置过）</label>
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
		</div>
		<div class="clear"></div>
	</form>
</div>
<!--{/if}-->

<!--{if $currentStatus == 'doReset'}-->
<div id="container">
	<form id="resetform" class="mem">
		<div class="mem-put">
			<div class="mem-t">
				<h3 class="mem-t-head">重置密码-<!--{SITENAME}--></h3>
				<label>新密码</label>
				<input type="password" name="password" class="input" id="password" />
				<label>确认密码</label>
				<input type="password" name="repassword" class="input" id="repassword" />
				<div class="mem-put-bottom">
				<input type="button" name="submit" value="立即重置" id="doreset-submit"class="right btn btn-default"/>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="mem-tab">
		</div>
		<div class="clear"></div>
	</form>
</div>
<!--{/if}-->
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#verify_image").attr("src",root +"user/identifyImage/"+Math.random()).click(function(){
		$(this).attr("src", root+"user/identifyImage/"+Math.random());
	});
		
	$("#reg-submit").click(function() {
		if( $('#joinform #password').val() != $('#joinform #repassword').val() ){
			alertMessage("两次密码不一样");
			$("#joinform #repassword").focus();
			return false;
		}
		$('#reg-submit').attr("disabled", "disabled");
		$.post(root+"user/register", {
				"do": "register",
				"email": $('#joinform #email').val(),
				"nickname": $('#joinform #nickname').val(),
				"password": $('#joinform #password').val(),
				"verify": $('#joinform #verify').val(),
			}, function(data) {
				data = eval("(" + data + ")");
				if (data.result == "success") {
					alertMessage("注册成功！即将转跳到登陆界面...");
					window.setTimeout("window.location='"+root+"user/login'",1500); 
				} else {
					alertMessage(data.message);
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
			$.post(root+"user/login", {
					"do": "login",
					"email": $('#loginform #email').val(),
					"password": $('#loginform #password').val(),
				}, function(data) {
					data = eval("(" + data + ")");
					if (data.result == "success") {
						alertMessage("登录成功！即将转跳到首页...");
						window.setTimeout("window.location='"+root+"'",1200); 
					} else {
						alertMessage(data.message);
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
				alertMessage("账号未填或无效");
				$("#email").focus();
			} else if(!ps){
				alertMessage("密码未填或无效");
				$("#password").focus();
			}
		}
		
	});
	$("#reset-submit").click(function() {
		$('#reset-submit').attr("disabled", "disabled");
		$.post(root+"user/resetPassword", {
				"do": "resetPassword",
				"email":  $('#resetform #email').val(),
				"verify": $('#resetform #verify').val(),
			}, function(data) {
				data = eval("(" + data + ")");
				if (data.result == "success") {
					alertMessage(data.message);
					window.setTimeout("window.location='"+root+"';",1000);
				} else {
					alertMessage(data.message);
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
		$.post(root+"user/doReset/", {
				"code": getUrlParam('code'),
				"password":  $('#doresetform #password').val(),
				"repassword": $('#doresetform #repassword').val(),
			}, function(data) {
				data = eval("(" + data + ")");
				if (data.result == "success") {
					alertMessage(data.message);
					window.setTimeout("window.location='"+root+"user/login';",1000);
				} else {
					alertMessage(data.message);
					$('#doreset-submit').removeAttr("disabled");
					$("#password").focus();
				}
		});		
	});
	$("#qqjoin_submit").click(function (){
		$("#qqjoin_submit").val('正在提交...');
		$("#qqjoin_submit").attr("disabled", "disabled");
		$.post(root+"user/qqjoin/", {
			"username": $("#qqjoinform #username").val(),
		}, function(data) {
			data = eval("(" + data + ")");
			if (data.result == "success") {
				alertMessage(data.message);
				window.setTimeout("window.location='"+root+"';",1000); 
			} else {
				alertMessage(data.message);
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
<!--{include _part_footer.tpl}-->
