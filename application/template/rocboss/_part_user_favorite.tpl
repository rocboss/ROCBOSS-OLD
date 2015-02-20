<!--{if $RequestType == 'favorite'}-->
<ul>
    <!--{loop $topicArray $t}-->
        <li class="topic-list" id="topic-<!--{$t['tid']}-->">
            <div class="topic">
                <div class="topic-head">
                    <a href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->" class="topic-avatar">
                        <img src="<!--{$t['avatar']}-->" alt="<!--{$t['username']}-->">
                    </a>
                    <span class="other">
                        <!--{if isset($t['istop']) && $t['istop'] > 0 }-->
                            <span><i class="icon icon-locationfill"></i> TOPING</span>
                        <!--{/if}-->
                        <!--{if $t['comments'] > 0 }-->
                            <span><i class="icon icon-commentfill"></i> <!--{$t['comments']}--></span>
                        <!--{/if}--> 
                    </span>
                    <a class="nickname" href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->">
                        <!--{$t['username']}-->
                    </a>
                    <span class="time">
                        <!--{$t['posttime']}-->
                    </span>
                </div>
                <a class="topic-content" id="topicContent" href="<!--{ROOT}-->home/read/<!--{$t['tid']}-->">
                    <!--{$t['title']}-->
                </a>
                <div class="topic-info">
                    <!--{if isset($t['tagArray']) && $t['tagArray'] != array()}-->
                        <!--{loop $t['tagArray'] $tagName}-->
                            <a href="#" class="tag"><!--{$tagName}--></a>
                        <!--{/loop}-->
                    <!--{/if}--> 
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
    <!--{if $topicArray == array() }--> 
        暂无数据 
    <!--{else}--> 
        <!--{$page}--> 
    <!--{/if}-->
</div>
<!--{/if}-->
