<section>
<div class="body">
    <h4>回复管理</h4>
    <form method="post" id="form">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
            <th class="text-center" width="30">PID</th>
            <th class="text-center">内容</td>
            <th class="text-center">用户</td>
            <th class="text-center" width="110">发布时间</td>
            <th class="text-center" width="80">操作</td>
            </tr>
            </thead>
            <tbody>
            <!--{loop $replyArray $reply}-->
                <tr id="reply-<!--{$reply['pid']}-->">
                <td align="center">
                    <input type="checkbox" class="checkbox" name="pid[]" value="<!--{$reply['pid']}-->" />
                </td>
                <td align="center">
                    <a href="<!--{ROOT}-->home/read/<!--{$reply['tid']}-->#reply-<!--{$reply['pid']}-->" target="_blank">
                        <!--{Filter::topicOut($reply['content'])}-->
                    </a>
                </td>
                <td align="center">
                    <!--{$reply['username']}-->
                </td>
                <td align="center">
                    <!--{Utils::formatTime($reply['posttime'])}-->
                </td>
                <td align="center">
                    <div class="btn btn-warning btn-sm" id="delReply-<!--{$reply['pid']}-->" onclick="delReply(<!--{$reply['pid']}-->);">删除</a>
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
            <th class="text-center"><a class="btn btn-warning" href="javascript:void(delAllReply())">删除所选</a></th>
            </tr>
            </tfoot>
        </table>
    </form>
    <div class="pagination">
        <!--{if empty($replyArray)}--> 
            暂无数据 
        <!--{else}--> 
            <!--{$page}--> 
        <!--{/if}-->
    </div>
</div>
<footer>&copy; ROCBOSS 后台管理中心</footer>
</section>