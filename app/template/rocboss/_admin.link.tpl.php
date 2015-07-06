<?php die('Access Denied');?>
<section>
    <ol class="bz-breadcrumb">
        <li><a href="{$root}admin">管理中心</a></li>
        <li class="bz-active">链接管理</li>
        <a href="javascript:editLink('', '', '');" class="bz-button bz-button-primary right">新增链接</a>
    </ol>
    
    <table class="bz-table">
        <thead>
        <tr>
        <th width="80">排序</th>
        <th>链接名称</td>
        <th>链接地址</td>
        <th width="180">操作</td>
        </tr>
        </thead>
        <tbody>
        {loop $LinksList $link}
            <tr>
                <td align="center">{$link.position}</td>
                <td align="center">
                    {$link.text}
                </td>
                <td align="center">
                    <a href="{$link.url}" target="_blank">
                        {$link.url}
                    </a>
                </td>
                <td align="center">
                    <a href="javascript:editLink({$link.position}, '{$link.text}', '{$link.url}');" title="编辑链接" class="bz-button bz-button-primary"><i class="iconfont icon-edit x2"></i></a>
                    <a href="{$root}manage/del_link/{$link.position}/" onclick="if(!(confirm('确定要删除吗？'))) return false;" title="删除链接" class="bz-button"><i class="iconfont icon-shanchu x2"></i></a>
                </td>
            </tr>
        {/loop}
        </tbody>
    </table>
</section>