<!--{include _part_header.tpl}-->

<div id="container">
	<div class="main-outlet container">
	<div class="content left">
	<div class="nav-head">会员中心</div>
		<!--{include _part_user_topic.tpl}-->

		<!--{include _part_user_reply.tpl}-->

        <!--{include _part_user_follow.tpl}-->

        <!--{include _part_user_fans.tpl}-->

		<!--{include _part_user_favorite.tpl}-->

        <!--{include _part_user_score.tpl}-->
	</div>
	<!--{include _part_user_left.tpl}-->
	<div class="clear"></div>
</div>

<div id="LoginBox">
    <div class="headRow">
        传送私信给 <!--{$userInfo['username']}--> (<!--{$GLOBALS['sys_config']['scores']['whisper']}--> 积分/条)
    </div>
    <form class="add-post">
    <div class="input-group">
        <input type="text"  value="发给：<!--{$userInfo['nickname']}-->" style="display:none;">
        <div class="textarea">
            <textarea id="content" name="content" rows="5" placeholder="请输入私信内容，最多允许250字哦~"></textarea>
        </div>
        <input type="hidden" name="touid" id="touid" value="<!--{$userInfo['uid']}-->">
        <a class="btn btn-default mt10" href="javascript:whisper('new');" id="whisper-btn">发送</a>
        <a class="btn mt10" href="javascript:;" id="cancel">取消</a>
    </div>
	</form>
</div>
</div>
<script>
	$(function ($) {
		$("#whisper").on('click', function () {
			$("body").append("<div id='mask'></div>");
			$("#mask").addClass("mask").fadeIn("slow");
			$("#LoginBox").fadeIn("slow");
			$("textarea[name=content]").focus();
		});
		$("#cancel").on('click', function () {
			$("#LoginBox").fadeOut("fast");
			$("#mask").css({ display: 'none' });
		});
	});
</script>
<!--{include _part_footer.tpl}-->