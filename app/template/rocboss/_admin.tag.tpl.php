<?php die('Access Denied');?>
<section>
    <ol class="bz-breadcrumb">
        <li><a href="{$root}admin">管理中心</a></li>
        <li class="bz-active">标签管理</li>
    </ol>

    <div class="bz-panel bz-panel-default">
        <div class="bz-panel-hd">
            <h3 class="bz-panel-title">标签管理</h3>
        </div>
        <div class="bz-panel-bd">
            {loop $tagArray $tag}
                <div style="border: 1px solid #cecece;padding:10px;width:13%;float:left;margin:5px;">
                    <div style="color:#017e66; font-weight:bold; text-align:center;">
                        {$tag.tagname}
                        <span style="background: #017e66; padding: 1px 3px; color:#fff; border-radius:8px; font-size:12px;">
                            {$tag.used}
                        </span>
                    </div>
                    <div style="text-align: center;">
                        <a href="{$root}manage/del_tag/{$tag.tagid}/"  onclick="if(!(confirm('确定要删除吗？'))) return false;" class="bz-button bz-button-sm">
                            <i class="iconfont icon-shanchu"></i> 删除
                        </a>
                    </div>
                </div>
            {/loop}
            <div class="clear"></div>
            <div class="pagination">
                {if empty($tagArray)} 
                    暂无数据 
                {else} 
                    {$page} 
                {/if}
            </div>
        </div>
    </div>
</section>