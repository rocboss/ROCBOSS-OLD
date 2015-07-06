<?php die('Access Denied');?>
{if $RequestType == 'favorite'}
<ul>
    {loop $topicArray $t}
    <li class="topic-item">
        <div class="posts-group cf">
            <div class="upvote ">
                <a class="upvote-link vote-up" href="{$root}read/{$t.tid}" title="{$t.praiseArray.count}人觉得很赞">
                    <i class="icon icon-appreciatefill x2{if $t['praiseArray']['myPraise'] == 1} vote-selected{/if}"></i>
                    <span class="vote-count">
                        {$t.praiseArray.count}
                    </span>
                </a>
            </div>
            <div class="topic-url">
                <a class="post-url" href="{$root}read/{$t.tid}" title="{$t.title}">
                    {$t.title}
                </a>
                <span class="post-tagline">
                    {if isset($t['istop']) && $t['istop'] > 0}
                        <span class="toping" title="置顶"><i class="icon icon-locationfill x1"></i></span>
                    {/if}
                    {if isset($t['pictures']) && $t['pictures'] != array() }
                        <span class="picturing" title="图片"><i class="icon icon-camerafill x1"></i></span>
                    {/if}
                    {if isset($t['tagArray']) && $t['tagArray'] != array()}
                        {loop $t['tagArray'] $tagName}
                            <a href="{$root}tag/{$tagName}" class="tag">{$tagName}</a>
                        {/loop}
                    {/if}
                </span>
            </div>
            <ul class="topic-meta right">
                <li class="topic-avatar">
                    <div class="user-image">
                        <a href="{$root}user/{$t.uid}">
                            <img class="avatar" height="60" src="{$t.avatar}" width="60" alt="{$t.username}">
                        </a>
                    </div>
                    <div class="user-tooltip">
                        <a href="{$root}user/{$t.uid}">
                            <img alt="{$t.username}" class="avatar avatar-big" src="{$t.avatar}">
                        </a>
                        <h3 class="user-nickname">{$t.username}</h3>
                        <h4 class="user-title">{$t.signature}</h4>
                        <p class="user-bio">{$t.posttime} 发布</p>
                        {if $t['client'] != ''}
                        <p class="user-client">
                            （来自 {$t.client}）
                        </p>
                        {/if}
                    </div>
                </li>
                <li class="topic-comment">
                    <div class="topic-comment-detail">
                        <a class="topic-comments" href="{$root}read/{$t.tid}#original">
                            <i class="icon icon-comment x2"></i> {$t.comments}</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
        <a class="topic-link" href="{$root}read/{$t.tid}" target="_blank"></a>
    </li>
    {/loop}
</ul>
<div id="pager">
    {if $topicArray == array() } 
        暂无数据 
    {else} 
        {$page} 
    {/if}
</div>
{/if}
