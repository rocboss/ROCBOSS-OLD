<!--{if $RequestType == 'score'}-->
<ul>
    <h2 class="nav-head">我30天内的积分明细</h2>
    <!--{loop $scoreList $t}-->
    <li class="topic-list">
    <p class="score">
        <strong class="text-green"><!--{$t['detail']}--></strong> <!--{if $t['changed'] < 0}-->扣除<!--{else}-->收入<!--{/if}--> <strong class="text-green"><!--{abs($t['changed'])}--></strong> 积分，余额 <strong class="text-green"><!--{$t['remain']}--></strong>，时间 <!--{$t['time']}--></p>
    </li>
    <!--{/loop}-->
</ul>
<div id="pager">
    <!--{if $scoreList == array() }--> 
        暂无数据 
    <!--{else}--> 
        <!--{$page}--> 
    <!--{/if}-->
</div>
<!--{/if}-->
