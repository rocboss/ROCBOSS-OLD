<?php die('Access Denied');?>
{include('_part_header.tpl.php')}
<link rel="stylesheet" type="text/css" href="{$css}rebox.css">
<script src="{$js}rebox.js"></script>
<script src="{$js}more.js"></script>
{if $loginInfo['groupid'] == 9}
    <script src="{$js}manage.js"></script>
{/if}
<script type="text/javascript">
    $(document).ready(function(){
        $('.view-content').rebox({ selector: '.picPre' });
        $('#original').rebox({ selector: '.reply-content .picPre' });
        $('#more').rebox({ selector: '.reply-content .picPre' });
        $('#more').more({'tid':'{$topicInfo.tid}', 'amount':'30', 'address': '{$root}getReplyList'});
    });
</script>
<div id="container">
    <div class="main-outlet container">
        <div class="content left">
            <div class="nav-head">
                {$topicInfo.title}
            </div>
            <ul>
            <li class="topic-view">
                <div class="topic-left">
                    <a href="{$root}user/{$topicInfo.uid}" class="avatar">
                        <img src="{$topicInfo.avatar}">
                    </a>
                </div>
                <div class="topic-body">
                    <p>
                        <span class="floor">
                            <span class="time">
                                {if $topicInfo['client'] != ''}
                                    <i class="icon icon-location"></i> {$topicInfo.client} &nbsp;&nbsp;
                                {/if}
                                <i class="icon icon-time"></i> {$topicInfo.posttime}
                            </span>
                        </span>
                        <a href="{$root}user/{$topicInfo.uid}" class="nickname">
                            {$topicInfo.username}
                        </a>
                    </p>
                    <div class="view-content">
                        {$topicInfo.content}
                        <div class="clear"></div>
                        {if $topicInfo['tagArray'] != array()}
                            <p class="showTag">
                            {loop $topicInfo['tagArray'] $tagName}
                                <a href="{$root}tag/{$tagName}" class="tag">{$tagName}</a>
                            {/loop}
                            </p>
                        {/if}
                    </div>
                    <div class="topicBottom">
                        <div class="right-admin x1">
                            {if $loginInfo['uid'] > 0}
                                <a class="praiseTopic btn-circle right" href="javascript:praiseTopic({$topicInfo.tid}, {$topicInfo.ispraise});" tip-title="{if $topicInfo['ispraise'] == 0}点赞{else}取消赞{/if}">
                                    {if $topicInfo['ispraise'] == 0}
                                        <i class="icon icon-appreciate x2"></i>
                                    {else}
                                        <i class="icon icon-appreciatefill x2"></i>
                                    {/if}
                                </a>
                            {/if}
                            {if $loginInfo['uid'] > 0}
                                <a class="favorTopic btn-circle right" href="javascript:favorTopic({$topicInfo.tid}, {$topicInfo.isfavorite});" tip-title="{if $topicInfo['isfavorite'] == 0}收藏{else}取消收藏{/if}">
                                    {if $topicInfo['isfavorite'] == 0}
                                        <i class="icon icon-favor x2"></i>
                                    {else}
                                        <i class="icon icon-favorfill x2"></i>
                                    {/if}
                                </a>
                            {/if}
                            {if $loginInfo['groupid'] == 9}
                                <a class="topTopic btn-circle right" href="javascript:topTopic({$topicInfo.tid}, {$topicInfo.istop});" tip-title="{if $topicInfo['istop'] == 0}置顶{else}取消置顶{/if}">
                                    {if $topicInfo['istop'] == 0}
                                        <i class="icon icon-location x2"></i>
                                    {else}
                                        <i class="icon icon-locationfill x2"></i>
                                    {/if}
                                </a>
                                <a class="lockTopic btn-circle right" href="javascript:lockTopic({$topicInfo.tid}, {$topicInfo.islock});" tip-title="{if $topicInfo['islock'] == 0}锁定{else}解锁{/if}">
                                    {if $topicInfo['islock'] == 0}
                                        <i class="icon icon-unlock x2"></i>
                                    {else}
                                        <i class="icon icon-lock x2"></i>
                                    {/if}
                                </a>
                            {/if}
                            {if $loginInfo['uid'] == $topicInfo['uid'] || $loginInfo['groupid'] == 9}
                                <a class="deleteTopic btn-circle right" href="javascript:deleteTopic({$topicInfo.tid});" tip-title="连续点击删除帖子">
                                    <i class="icon icon-delete x2"></i>
                                </a>
                            {/if}
                        </div>
                        <div class="clear"></div>
                        
                        {if $topicInfo['praiseArray'] != array()}
                        <div class="topic-praise">
                            <span class="p-tips">以下用户赞了本帖</span>
                            {loop $topicInfo['praiseArray'] $c}
                            <a href="{$root}user/{$c.praiseUid}">
                                <img src="{$c.praiseAvatar}" title="{$c.praiseUsername}觉得很赞" alt="{$c.praiseUsername}" class="avatarC">
                            </a>
                            {/loop}
                        </div>
                        {else}
                        <div class="topic-praise" style="display: none;">
                            <span class="p-tips">以下用户赞了本帖</span>
                        </div>
                        {/if}
                    </div>
                    <div class="clear"></div>
                </div>
                </li>


                <div id="original">
                    {loop $replyList $reply}
                    <div class="reply-list" id="d-reply-{$reply.pid}">
                        <span class="pid" id="reply-{$reply.pid}" data-username="{$reply.username}"></span>
                        <div class="reply-left">
                            <a href="{$root}user/{$reply.uid}" class="uid">
                                <img src="{$reply.avatar}" alt="{$reply.username}" class="avatar">
                            </a>
                        </div>
                        <div class="reply-content">
                            <div class="reply-detail">
                                <span class="content">{$reply.content}</span>
                            </div>
                            <div class="reply-bottom">
                                <span class="reply-bottom-span">
                                    <a href="{$root}user/{$reply.uid}" class="uid">
                                        <span class="username">{$reply.username}</span>
                                    </a>
                                </span>
                                {if $reply['client'] != ''}
                                <span class="client reply-bottom-span">
                                    <i class="icon icon-location"></i> {$reply.client}
                                </span>
                                {/if}
                                <span class="posttime reply-bottom-span">
                                    <i class="icon icon-time"></i> {$reply.posttime}
                                </span>
                                {if $reply['uid'] == $loginInfo['uid'] || $loginInfo['groupid'] == 9}
                                <span class="reply-admin right">
                                    <a class="deleteReply" href="javascript:deleteReply({$reply.pid});">
                                        <i class="icon icon-delete x1"></i>删除
                                    </a>
                                </span>
                                {/if}
                                {if $loginInfo['uid'] > 0}
                                <a class="showFloorReply right" href="javascript:showFloorReply({$reply.pid}, '@{$reply.username} ');">
                                    <i class="icon icon-forward x1"></i>评论
                                </a>
                                {/if}
                                <span class="floor right" id="floor-more-{$reply.pid}">
                                {if !empty($reply['floor'])}
                                    {loop $reply['floor'] $floor}
                                        <div id="floor-list-{$floor.floorId}" class="floor-list">
                                            <span class="floor-avatar">
                                                <a href="{$root}user/{$floor.floorUid}">
                                                    <img src="{$floor.avatar}">
                                                </a>
                                            </span>
                                            <span class="floor-username">
                                                <a href="{$root}user/{$floor.floorUid}">
                                                    {$floor.floorUser}
                                                </a>
                                            </span>
                                            <span class="floor-admin right">
                                                {if $floor['floorUid'] != $loginInfo['uid'] && $loginInfo['uid'] > 0}
                                                <a href="javascript:showFloorReply({$floor.floorPid},'@{$floor.floorUser} ');" title="回复TA">
                                                    <i class="icon icon-forward x1"></i>回复
                                                </a>
                                                {/if}
                                                {if $floor['floorUid'] == $loginInfo['uid'] || $loginInfo['groupid'] == 9}
                                                <a class="delete-btn" href="javascript:deleteFloor({$floor.floorId});">
                                                    <i class="icon icon-delete x1"></i>删除
                                                </a>
                                                {/if}
                                            </span>
                                            <span class="floor-time right">{$floor.floorTime}</span>
                                            <div class="clear"></div>
                                            <span class="floor-content">{$floor.floorContent}</span>
                                        </div>
                                    {/loop}
                                    <div class="floor-more">
                                        {if count($reply['floor']) >= 5}
                                            <a href="javascript:getMoreFloor({$reply.pid}, 1);">
                                                <i class="icon icon-unfold x1"></i> 点击加载更多评论
                                            </a>
                                        {else}
                                            已加载全部评论
                                        {/if}
                                    </div>
                                {/if}
                                </span>
                            </div>
                        </div>
                    </div>
                    {/loop}
                </div>
                
                <div id="more">
                    <a href="javascript:;" class="get_more"><i class="icon icon-unfold x2"></i> 点击加载更多回复</a>
                    <div class="reply-list" root-user-data="{$root}user/">
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
                                {if $loginInfo['uid'] > 0}
                                    <a class="showFloorReply right"><i class="icon icon-forward x1"></i>评论</a>
                                {/if}
                                <span class="floor right"></span>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="clear"></div>
                {include('_part_reply_post.tpl.php')}
            </ul>
        </div>
        <div class="side">
            {include('_part_side.tpl.php')}
        </div>
        <div class="clear"></div>
    </div>
</div>
{include('_part_footer.tpl.php')}