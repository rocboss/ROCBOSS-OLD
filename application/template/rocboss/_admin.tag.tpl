<section>
    <div class="body">
        <h2>标签管理</h2>
        <!--{loop $tagArray $tag}-->
            <div style="border: 1px solid #cecece;padding:10px;width:13%;float:left;margin:5px;">
                <div style="color:#017e66; font-weight:bold; text-align:center;">
                    <!--{$tag['tagname']}-->
                    <span style="background: #017e66; padding: 1px 3px; color:#fff; border-radius:8px; font-size:12px;">
                        <!--{$tag['used']}-->
                    </span>
                </div>
                <div>
                    <a href="<!--{ROOT}-->manage/del_tag/<!--{$tag['tagid']}-->/" class="btn btn-warning" style="width: 100%; padding: 1px;">
                        删除
                    </a>
                </div>
            </div>
        <!--{/loop}-->
        <div class="clear"></div>
        <div class="pagination">
            <!--{if empty($tagArray)}--> 
                暂无数据 
            <!--{else}--> 
                <!--{$page}--> 
            <!--{/if}-->
        </div>
    </div>
</section>