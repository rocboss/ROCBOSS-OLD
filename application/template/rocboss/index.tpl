<!--{include _part_header.tpl}-->
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/rebox.css">
<script src="<!--{ROOT}-->application/template/rocboss/js/rebox.js"></script>
<script type ="" "text/javascript">
	$(document).ready(function() {
		$("#post-newtopic").hide();
	});
</script>
<div id="container">
	<div class="main-outlet container">
		<div class="content left">
			<h2 class="nav-head">
			<!--{if $loginInfo['uid'] > 0 }-->
				<a href="javascript:showTopicForm();" title="发表新话题" class="right">
					<i class="icon icon-edit x2"></i> NEW
				</a>
			<!--{/if}-->
				<i class="icon icon-order x2"></i> 

				<!--{if $_COOKIE['type'] == 'lasttime'}-->
					<a href="<!--{ROOT}-->do/posttime/">最新发表</a>
				<!--{else}-->
					最新发表
				<!--{/if}--> 
				<!--{if $_COOKIE['type'] != 'lasttime'}-->
					<a href="<!--{ROOT}-->do/lasttime/">最后回复</a>
				<!--{else}-->
					最后回复
				<!--{/if}-->
			</h2>
			<!--{include _part_topic_post.tpl}-->
			<!--{include _part_topic_list.tpl}-->
		</div>
		<div class="side left">
			<!--{include _part_left.tpl}-->
		</div>
		<div class="clear"></div>
	</div>
</div>
<!--{include _part_footer.tpl}-->