<section>
<div class="body">
    <h4>主题管理</h4>
    <form method="post" id="form">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
            <th class="text-center" width="30">TID</th>
            <th class="text-center">主题</td>
            <th class="text-center">用户</td>
            <th class="text-center" width="110">发布时间</td>
            <th class="text-center" width="80">操作</td>
            </tr>
            </thead>
            <tbody>
            <!--{loop $topicArray $topic}-->
                <tr id="topic-<!--{$topic['tid']}-->">
                <td align="center">
                    <input type="checkbox" class="checkbox" name="tid[]" value="<!--{$topic['tid']}-->" />
                </td>
                <td align="center">
                    <a href="<!--{ROOT}-->home/read/<!--{$topic['tid']}-->" target="_blank">
                        <!--{Filter::topicOut($topic['title'])}-->
                    </a>
                </td>
                <td align="center">
                    <!--{$topic['username']}-->
                </td>
                <td align="center">
                    <!--{Utils::formatTime($topic['posttime'])}-->
                </td>
                <td align="center">
                    <div class="btn btn-warning btn-sm" id="delTopic-<!--{$topic['tid']}-->" onclick="delTopic(<!--{$topic['tid']}-->);">删除</a>
                </td>
                </tr>
            <!--{/loop}-->
            </tbody>
            <tfoot>
            <tr>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"><a class="btn btn-warning" href="javascript:void(delAllTopic())">删除所选</a></th>
            </tr>
            </tfoot>
        </table>
    </form>
    <div class="pagination">
        <!--{if empty($topicArray)}--> 
            暂无数据 
        <!--{else}--> 
            <!--{$page}--> 
        <!--{/if}-->
    </div>
</div>
<footer>&copy; ROCBOSS 后台管理中心</footer>
</section>