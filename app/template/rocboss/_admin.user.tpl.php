<?php die('Access Denied');?>
<section>
    <ol class="bz-breadcrumb">
        <li><a href="{$root}admin">管理中心</a></li>
        <li class="bz-active">用户管理</li>
    </ol>

    <form method="post" id="form">
        <table class="bz-table">
            <thead>
            <tr>
            <th class="text-center">头像</th>
            <th class="text-center">昵称</th>
            <th class="text-center">邮箱</th>
            <th class="text-center">积分</th>
            <th class="text-center">余额</th>
            <th class="text-center">最后活跃</th>
            <th class="text-center">操作</th>
            </tr>
            </thead>
            <tbody>
            {loop $userArray $user}
            <tr id="user-{$user.uid}">
            <td align="center">
                <img src="{$user.avatar}" width="30" height="30" />
            </td>
            <td align="center">
                <a href="{$root}user/{$user.uid}/" target="_blank">
                    {$user.username}
                </a>
            </td>
            <td align="center">
                {if empty($user['email'])}
                    暂未设定
                {else}
                    {$user.email}
                {/if}
            </td>
            <td align="center">
                {$user.scores}
            </td>
            <td align="center">
                {$user.money}
            </td>
            <td align="center">
                {$user.lasttime}
            </td>
            <td align="center">
                {if $user['groupid'] == 1}
                <a class="bz-button bz-button-primary" onclick="javascript:ban({$user.uid}, this, 0);" title="禁止发言">禁言</a>
                {elseif $user['groupid'] == 0}
                <a class="bz-button danger" onclick="javascript:ban({$user.uid}, this, 1);" title="解除禁言">解禁</a>
                {/if}
                {if $user['groupid'] == 9}
                <a class="bz-button bz-button-primary" onclick="javascript:ban({$user.uid}, this, 1);" title="降级为普通会员">设为普通会员</a>
                {else}
                <a class="bz-button bz-button-sm" onclick="javascript:ban({$user.uid}, this, 9);" title="升级为管理员">升级</a>
                {/if}
            </td>
            </tr>
            {/loop}
            </tbody>
        </table>
    </form>
    <div class="pagination">
        {if empty($userArray)} 
            暂无数据 
        {else} 
            {$page} 
        {/if}
    </div>
</section>