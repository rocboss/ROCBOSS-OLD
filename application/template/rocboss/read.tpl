<!--{include _part_header.tpl}-->
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/rebox.css">
<script src="<!--{ROOT}-->application/template/rocboss/js/rebox.js"></script>
<script src="<!--{ROOT}-->application/template/rocboss/js/more.js"></script>
<!--{if $loginInfo['groupid'] == 9}-->
    <script src="<!--{ROOT}-->application/template/rocboss/js/manage.js"></script>
<!--{/if}-->
<script type ="" "text/javascript">
$(function(){
    $('.view-content').rebox({ selector: '.picPre' });
    $('#original').rebox({ selector: '.reply-content .picPre' });
    $('#more').rebox({ selector: '.reply-content .picPre' });
    $('#more').more({'tid':'<!--{$topicInfo['tid']}-->', 'amount':'30', 'address': '<!--{ROOT}-->home/getReplyList'});
});
</script>
<div id="container">
    <div class="main-outlet container">
        <div class="content left">
            <div class="nav-head">
                <!--{$topicInfo['title']}-->
            </div>
            <ul>
            <li class="topic-view">
                <div class="topic-left">
                    <a href="<!--{ROOT}-->user/index/uid/<!--{$topicInfo['uid']}-->" class="avatar">
                        <img src="<!--{$topicInfo['avatar']}-->">
                    </a>
                </div>
                <div class="topic-body">
                    <p>
                        <span class="floor">
                            <span class="time">
                                <!--{if $topicInfo['client'] != ''}-->
                                    <i class="icon icon-mobilefill"></i> <!--{$topicInfo['client']}--> 
                                <!--{/if}-->
                                <i class="icon icon-time"></i> <!--{$topicInfo['posttime']}-->
                            </span>
                        </span>
                        <a href="<!--{ROOT}-->user/index/uid/<!--{$topicInfo['uid']}-->" class="nickname">
                            <!--{$topicInfo['username']}-->
                        </a>
                    </p>
                    <div class="view-content">
                        <!--{$topicInfo['content']}-->
                        <div class="clear"></div>
                        <!--{if $topicInfo['tagArray'] != array()}-->
                            <p class="showTag">
                            <!--{loop $topicInfo['tagArray'] $tagName}-->
                                <a href="<!--{ROOT}-->home/tag/name/<!--{$tagName}-->/" class="tag"><!--{$tagName}--></a>
                            <!--{/loop}-->
                            </p>
                        <!--{/if}-->
                    </div>
                    <div class="topicBottom">
                        <div class="right-admin x1">
                            <!--{if $loginInfo['uid'] > 0}-->
                                <a class="praiseTopic" href="javascript:praiseTopic(<!--{$topicInfo['tid']}-->, <!--{$topicInfo['ispraise']}-->);">
                                    <!--{if $topicInfo['ispraise'] == 0}-->
                                        <i class="icon icon-appreciate x2"></i>点赞
                                    <!--{else}-->
                                        <i class="icon icon-appreciatefill x2"></i>取消赞
                                    <!--{/if}-->
                                </a>
                            <!--{/if}-->
                            <!--{if $loginInfo['uid'] > 0}-->
                                <a class="favorTopic" href="javascript:favorTopic(<!--{$topicInfo['tid']}-->, <!--{$topicInfo['isfavorite']}-->);">
                                    <!--{if $topicInfo['isfavorite'] == 0}-->
                                        <i class="icon icon-favor x2"></i>收藏
                                    <!--{else}-->
                                        <i class="icon icon-favorfill x2"></i>取消收藏
                                    <!--{/if}-->
                                </a>
                            <!--{/if}-->
                            <!--{if $loginInfo['groupid'] == 9}-->
                                <a class="topTopic" href="javascript:topTopic(<!--{$topicInfo['tid']}-->, <!--{$topicInfo['istop']}-->);">
                                    <!--{if $topicInfo['istop'] == 0}-->
                                        <i class="icon icon-location x2"></i>置顶
                                    <!--{else}-->
                                        <i class="icon icon-locationfill x2"></i>取消置顶
                                    <!--{/if}-->
                                </a>
                                <a class="lockTopic" href="javascript:lockTopic(<!--{$topicInfo['tid']}-->, <!--{$topicInfo['islock']}-->);">
                                    <!--{if $topicInfo['islock'] == 0}-->
                                        <i class="icon icon-unlock x2"></i>锁定
                                    <!--{else}-->
                                        <i class="icon icon-lock x2"></i>解锁
                                    <!--{/if}-->
                                </a>
                            <!--{/if}-->
                            <!--{if $loginInfo['uid'] == $topicInfo['uid'] || $loginInfo['groupid'] == 9}-->
                                <a class="deleteTopic" href="javascript:deleteTopic(<!--{$topicInfo['tid']}-->);">
                                    <i class="icon icon-delete x2"></i>删除
                                </a>
                            <!--{/if}-->
                        </div>
                        
                        <!--{if $topicInfo['praiseArray'] != array()}-->
                        <div class="topic-praise">
                            <!--{loop $topicInfo['praiseArray'] $c}-->
                            <a href="<!--{ROOT}-->user/index/uid/<!--{$c['praiseUid']}-->">
                                <img src="<!--{$c['praiseAvatar']}-->" title="<!--{$c['praiseUsername']}-->" alt="<!--{$c['praiseUsername']}-->" class="avatarC">
                            </a>
                            <!--{/loop}-->
                            <i class="icon icon-appreciatefill x2" title="觉得很赞"></i>
                        </div>
                        <!--{else}-->
                        <div class="topic-praise" style="display: none;">
                            <i class="icon icon-appreciatefill x2" title="觉得很赞"></i>
                        </div>
                        <!--{/if}-->
                    </div>
                    <div class="clear"></div>
                </div>
                </li>

                <div id="original">
                    <!--{loop $replyList $reply}-->
                    <div class="reply-list" id="d-reply-<!--{$reply['pid']}-->">
                        <span class="pid" id="reply-<!--{$reply['pid']}-->" data-username="<!--{$reply['username']}-->"></span>
                        <div class="reply-left">
                            <a href="<!--{ROOT}-->user/index/uid/<!--{$reply['uid']}-->" class="uid">
                                <img src="<!--{$reply['avatar']}-->" alt="<!--{$reply['username']}-->" class="avatar">
                            </a>
                        </div>
                        <div class="reply-content">
                            <div class="reply-detail">
                                <span class="content"><!--{$reply['content']}--></span>
                            </div>
                            <div class="reply-bottom">
                                <span class="reply-bottom-span">
                                    <a href="<!--{ROOT}-->user/index/uid/<!--{$reply['uid']}-->" class="uid">
                                        <span class="username"><!--{$reply['username']}--></span>
                                    </a>
                                </span>
                                <!--{if $reply['client'] != ''}-->
                                <span class="client reply-bottom-span">
                                    <i class="icon icon-mobilefill"></i> <!--{$reply['client']}-->
                                </span>
                                <!--{/if}-->
                                <span class="posttime reply-bottom-span">
                                    <i class="icon icon-time"></i> <!--{$reply['posttime']}-->
                                </span>
                                <!--{if $reply['uid'] == $loginInfo['uid'] || $loginInfo['groupid'] == 9}-->
                                <span class="reply-admin right">
                                    <a class="deleteReply" href="javascript:deleteReply(<!--{$reply['pid']}-->);">
                                        <i class="icon icon-delete x1"></i>删除
                                    </a>
                                </span>
                                <!--{/if}-->
                                <!--{if $loginInfo['uid'] > 0}-->
                                <a class="showFloorReply right" href="javascript:showFloorReply(<!--{$reply['pid']}-->, '@<!--{$reply['username']}--> ');">
                                    <i class="icon icon-forward x1"></i>评论
                                </a>
                                <!--{/if}-->
                                <span class="floor right" id="floor-more-<!--{$reply['pid']}-->">
                                <!--{if !empty($reply['floor'])}-->
                                    <!--{loop $reply['floor'] $floor}-->
                                        <div id="floor-list-<!--{$floor['floorId']}-->" class="floor-list">
                                            <span class="floor-avatar">
                                                <a href="<!--{ROOT}-->user/index/uid/<!--{$floor['floorUid']}-->/">
                                                    <img src="<!--{$floor['avatar']}-->">
                                                </a>
                                            </span>
                                            <span class="floor-username">
                                                <a href="<!--{ROOT}-->user/index/uid/<!--{$floor['floorUid']}-->/">
                                                    <!--{$floor['floorUser']}-->
                                                </a>
                                            </span>
                                            <span class="floor-admin right">
                                                <!--{if $floor['floorUid'] != $loginInfo['uid'] && $loginInfo['uid'] > 0}-->
                                                <a href="javascript:showFloorReply(<!--{$floor['floorPid']}-->,'@<!--{$floor['floorUser']}--> ');" title="回复TA">
                                                    <i class="icon icon-forward x1"></i>回复
                                                </a>
                                                <!--{/if}-->
                                                <!--{if $floor['floorUid'] == $loginInfo['uid'] || $loginInfo['groupid'] == 9}-->
                                                <a class="delete-btn" href="javascript:deleteFloor(<!--{$floor['floorId']}-->);">
                                                    <i class="icon icon-delete x1"></i>删除
                                                </a>
                                                <!--{/if}-->
                                            </span>
                                            <span class="floor-time right"><!--{$floor['floorTime']}--></span>
                                            <div class="clear"></div>
                                            <span class="floor-content"><!--{$floor['floorContent']}--></span>
                                        </div>
                                    <!--{/loop}-->
                                    <div class="floor-more">
                                        <!--{if count($reply['floor']) >= 5}-->
                                            <a href="javascript:getMoreFloor(<!--{$reply['pid']}-->, 1);">
                                                <i class="icon icon-unfold x1"></i> 点击加载更多评论
                                            </a>
                                        <!--{else}-->
                                            已加载全部评论
                                        <!--{/if}-->
                                    </div>
                                <!--{/if}-->
                                </span>
                            </div>
                        </div>
                    </div>
                    <!--{/loop}-->
                </div>
                
                <div id="more">
                    <a href="javascript:;" class="get_more"><i class="icon icon-unfold x2"></i> 点击加载更多回复</a>
                    <div class="reply-list" root-user-data="<!--{ROOT}-->user/index/uid/">
                        <span class="pid"></span>
                        <div class="reply-left">
                            <a href="" class="uid">
                                <img src="" alt="" class="avatar"/>
                            </a>
                        </div>
                        <div class="reply-content">
                            <div class="reply-detail">
                                <span class="content"></span>
                            </div>
                            <div class="reply-bottom">
                                <span class="reply-bottom-span">
                                    <a href="" class="uid">
                                        <span class="username"></span>
                                    </a>
                                </span>
                                <span class="client reply-bottom-span"></span>
                                <span class="posttime reply-bottom-span"></span>
                                <span class="reply-admin right"></span>
                                <!--{if $loginInfo['uid'] > 0}-->
                                    <a class="showFloorReply right"><i class="icon icon-forward x1"></i>评论</a>
                                <!--{/if}-->
                                <span class="floor right"></span>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="clear"></div>
                <!--{include _part_reply_post.tpl}-->
            </ul>
        </div>
        <div class="side left">
            <!--{include _part_left.tpl}-->
        </div>
        <div class="clear"></div>
    </div>
</div>
<!--{include _part_footer.tpl}-->