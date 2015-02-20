<!--{include _part_header.tpl}-->
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/rebox.css">
<script src="<!--{ROOT}-->application/template/rocboss/js/rebox.js"></script>
<div id="container">
	<div class="main-outlet container">
    <div class="content left">
    	<!--{if $notifyStatus == '0'}-->
          <div class="nav-head">未读提醒</div>
          <ul>
            <!--{loop $notificationList $t}-->
            <li class="topic-list" id="notification-<!--{$t['nid']}-->">
				<div class="topic">
                    <div class="topic-head">
                        <a href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->" class="topic-avatar">
                            <img src="<!--{$t['avatar']}-->" alt="<!--{$t['username']}-->">
                        </a>
                        <a class="nickname" href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->">
                            <!--{$t['username']}-->
                        </a>
                        <span class="time">
                            <!--{$t['posttime']}-->
                        </span>
                        <!--{if $t['fid'] !=0 }-->
                            [楼中楼评论]
                        <!--{elseif $t['pid'] !=0 }-->
                            [回复]
                        <!--{elseif $t['tid'] !=0 }-->
                            [主题]
                        <!--{/if}-->
                    </div>
					<span class="topic-content">
                        <a href="<!--{ROOT}-->home/read/<!--{$t['tid']}-->/#reply-<!--{$t['pid']}-->" class="go-reply" title="查看">
                            <i class="icon icon-link x2"></i>
                        </a>
                        <!--{$t['content']}-->
                    </span>
                    <div class="clear"></div>
				</div>
            </li>
            <!--{/loop}-->
          </ul>
        <!--{/if}-->

        <!--{if $notifyStatus == '1'}-->
          <div class="nav-head">已读提醒</div>
          <ul>
            <!--{loop $notificationList $t}-->
            <li class="topic-list" id="notification-<!--{$t['nid']}-->">
				<div class="topic">
                    <div class="topic-head">
                        <a href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->" class="topic-avatar">
                            <img src="<!--{$t['avatar']}-->" alt="<!--{$t['username']}-->">
                        </a>
                        <a class="nickname" href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->">
                            <!--{$t['username']}-->
                        </a>
                        <span class="time">
                            <!--{$t['posttime']}-->
                        </span>
                        <!--{if $t['fid'] !=0 }-->
                            [楼中楼评论]
                        <!--{elseif $t['pid'] !=0 }-->
                            [回复]
                        <!--{elseif $t['tid'] !=0 }-->
                            [主题]
                        <!--{/if}-->
                    </div>
                    <span class="topic-content">
                        <a href="<!--{ROOT}-->home/read/<!--{$t['tid']}-->/#reply-<!--{$t['pid']}-->" class="go-reply" title="查看">
                            <i class="icon icon-link x2"></i>
                        </a>
                        <!--{$t['content']}-->
                    </span>
                    <div class="clear"></div>
				</div>
            </li>
            <!--{/loop}-->
          </ul>
        <!--{/if}-->

		<div id="pager">
		  <!--{if $notificationList == array() }--> 
		  	暂无提醒 
		  <!--{else}--> 
		  	<!--{$page}--> 
		  <!--{/if}-->
		</div>
	</div>

	<div class="side left">
	    <div class="box">
	      <ul class="list-topic">
			<li<!--{if $notifyStatus == '0'}--> class="active"<!--{/if}-->><a href="<!--{ROOT}-->user/notification/status/0/">未读提醒</a></li>
			<li<!--{if $notifyStatus == '1'}--> class="active"<!--{/if}-->><a href="<!--{ROOT}-->user/notification/status/1/">已读提醒</a></li>
	      </ul>
	    </div>
  	</div>
  	<div class="clear"></div>
  	</div>
</div>
<!--{include _part_footer.tpl}-->