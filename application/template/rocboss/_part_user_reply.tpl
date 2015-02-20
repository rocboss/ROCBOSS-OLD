<!--{if $RequestType == 'reply'}--> 
<ul>
    <!--{loop $replyArray $t}-->
        <li class="topic-list" id="reply-<!--{$t['pid']}-->">
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
                </div>
                <span class="topic-content">
                    <a href="<!--{ROOT}-->home/read/<!--{$t['tid']}-->/#reply-<!--{$t['pid']}-->" class="go-reply" title="查看主题">
                        <i class="icon icon-link x2"></i>
                    </a>
                    <!--{$t['content']}-->
                </span>
                <div class="topic-info">
                    <!--{if isset($t['pictures']) && $t['pictures'] != '' }-->
                        <i class="icon icon-locationfill"></i>
                    <!--{/if}-->
                    <!--{if $t['client'] != ''}-->
                        <i class="icon icon-mobilefill"></i><!--{$t['client']}-->
                    <!--{/if}-->
                </div>
                <div class="clear"></div>
            </div>
        </li>
    <!--{/loop}-->
</ul>
<div id="pager">
    <!--{if $replyArray == array() }--> 
        暂无数据 
    <!--{else}--> 
        <!--{$page}--> 
    <!--{/if}-->
</div>
<!--{/if}-->