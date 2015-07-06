<?php die('Access Denied');?>
{include('_part_header.tpl.php')}
<link rel="stylesheet" type="text/css" href="{$css}rebox.css">
<script src="{$js}rebox.js"></script>
<div id="container">
  <div class="main-outlet container">
    <div class="side">
        <div class="box">
          <ul class="list-topic">
            <li{if $notifyStatus == '0'} class="active"{/if}><a href="{$root}notification/0/">未读提醒</a></li>
            <li{if $notifyStatus == '1'} class="active"{/if}><a href="{$root}notification/1/">已读提醒</a></li>
          </ul>
        </div>
    </div>
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li class="active">{if $notifyStatus == '0'}未读提醒{else}已读提醒{/if}</li>
    </ol>
    <div class="content">
        {if $notifyStatus == '0'}
          <ul>
            {loop $notificationList $t}
            <li class="topic-list" id="notification-{$t.nid}">
                <div class="topic">
                    <div class="topic-head">
                        <a href="{$root}user/{$t.uid}" class="topic-avatar">
                            <img src="{$t.avatar}" alt="{$t.username}">
                        </a>
                        <a class="nickname" href="{$root}user/{$t.uid}">
                            {$t.username}
                        </a>
                        <span class="time">
                            {$t.posttime}
                        </span>
                        {if $t['fid'] !=0 }
                        （评论了你）
                        {elseif $t['pid'] !=0 }
                        （回复了你）
                        {elseif $t['tid'] !=0 }
                        （提到了你）
                        {/if}
                    </div>
                    <span class="topic-content">
                        <a href="{$root}do/readNotification/{$t.nid}/" class="btn-circle btn-right" tip-title="标记为已读并查看" style="right: 28px;">
                            <i class="icon icon-squarecheck x2"></i>
                        </a>
                        <a href="javascript:deleteNotification({$t.nid});" class="delNotification-btn-{$t.nid} btn-circle btn-right" tip-title="删除">
                            <i class="icon icon-delete x2"></i>
                        </a>
                        {$t.content}
                    </span>
                    <div class="clear"></div>
                </div>
            </li>
            {/loop}
          </ul>
        {/if}

        {if $notifyStatus == '1'}
          <ul>
            {loop $notificationList $t}
            <li class="topic-list" id="notification-{$t.nid}">
                <div class="topic">
                    <div class="topic-head">
                        <a href="{$root}user/{$t.uid}" class="topic-avatar">
                            <img src="{$t.avatar}" alt="{$t.username}">
                        </a>
                        <a class="nickname" href="{$root}user/{$t.uid}">
                            {$t.username}
                        </a>
                        <span class="time">
                            {$t.posttime}
                        </span>
                        {if $t['fid'] !=0 }
                        （评论了你）
                        {elseif $t['pid'] !=0 }
                        （回复了你）
                        {elseif $t['tid'] !=0 }
                        （提到了你）
                        {/if}
                    </div>
                    <span class="topic-content">
                        <a href="{$root}do/readNotification/{$t.nid}/" class="btn-circle btn-right" tip-title="查看" style="right: 28px;">
                            <i class="icon icon-squarecheck x2"></i>
                        </a>
                        <a href="javascript:deleteNotification({$t.nid});" class="delNotification-btn-{$t.nid} btn-circle btn-right" tip-title="删除">
                            <i class="icon icon-delete x2"></i>
                        </a>
                        {$t.content}
                    </span>
                    <div class="clear"></div>
                </div>
            </li>
            {/loop}
          </ul>
        {/if}

        <div id="pager">
          {if $notificationList == array() } 
            暂无提醒 
          {else} 
            {$page} 
          {/if}
        </div>
    </div>
    <div class="clear"></div>
  </div>
</div>
{include('_part_footer.tpl.php')}