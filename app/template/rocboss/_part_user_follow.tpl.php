<?php die('Access Denied');?>
{if $RequestType == 'follow'}
<ul>
    {loop $followList $t}
    <li class="topic-list">
        <div class="topic">
            <div class="topic-head">
                <a href="{$root}user/{$t.uid}" class="topic-avatar">
                    <img src="{$t.avatar}" alt="{$t.username}">
                </a>
                <a class="nickname" href="{$root}user/{$t.uid}">
                    {$t.username}
                </a>
            </div>
            <p class="topic-content"> 
                {if $t['signature'] != ''}个性签名：{$t.signature}
                {else}这个家伙太懒了，还没有个性签名~
                {/if}
            </p>
        </div>
    </li>
    {/loop}
</ul>
<div id="pager">
    {if $followList == array() } 
        暂无数据
    {else} 
        {$page} 
    {/if}
</div>
{/if}
