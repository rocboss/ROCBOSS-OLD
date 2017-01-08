{$headerLayout}
    <div id="wrap">
        <!-- 左侧菜单栏目 -->
        {$sidebarLayout}
        <!-- 右侧内容 -->
        <div id="right-content">
            <a class="toggle-btn">
                <i class="fa fa-navicon"></i>
            </a>
            <div class="tab-content">
                <!-- 资源管理模块 -->
                <div class="check-div form-inline">
                    <span class="pull-right" style="margin-right: 15px;">共有 {$count} 回复</span>
                </div>
                <div class="data-div">
                    <div class="feed-activity-list">
                        {loop $replys $reply}
                        <div id="reply-{$reply.pid}" class="feed-element" style="margin: 10px; background: #fff; padding: 20px 15px;">
                            <div class="media-body ">
                                <small class="pull-right">
                                    <a href="/read/{$reply.tid}#reply-{$reply.pid}" target="_blank" class="btn btn-success btn-xs"><i class="fa fa-mail-forward"></i> 查看</a>
                                </small>
                                <img src="{$reply.avatar}" onerror="javascript:this.src='https://dn-roc.qbox.me/avatar/0-avatar.png';" style="width: 30px; height: 30px; border-radius: 50%;"/>
                                <span style="color: #5593a1">{$reply.username}</span>
                                回复话题 “<strong><a href="/read/{$reply.tid}" target="_blank">{$reply.topic_title}</a></strong>”
                                <div style="color: #999; margin-top: 6px;">
                                    {$reply.content}
                                </div>
                                <small class="text-muted">{$reply.post_time} 来自 {$reply.client}</small>
                                <a class="delete-reply btn btn-danger btn-xs pull-right" data-pid="{$reply.pid}"><i class="fa fa-trash"></i> 删除</a>
                                {if $reply['at_pid'] > 0}
                                <div class="well">
                                    <p>引用 <strong>{$reply.at_reply.username}</strong> 的评论</p>
                                    <p class="ellipsis">
                                    {$reply.at_reply.content}
                                    </p>
                                </div>
                                {/if}
                            </div>
                        </div>
                        {/loop}
                    </div>

                    <div id="pagination" class="pagination" style="margin: 8px;"></div>
                </div>
                <!-- 底部 -->
                {$footerLayout}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            var _csrf = $('meta[name=_csrf]').attr('content');
            var page = {$page},
            per = {$per},
            pages = Math.ceil({$count} / per),
            href = '/admin/replys/';
            laypage.dir = '/dist/css/laypage.css';
            laypage({
                dir: '/dist/css/laypage.css',
                cont: 'pagination',
                pages: pages,
                curr: page,
                href: href + '(?)',
                first: 1,
                last: pages,
                skin: 'molv',
                prev: '<',
                next: '>',
                groups: 15,
                jump: function(e, first) {
                    if (!first) {
                        window.location.href = href + e.curr;
                    }
                }
            });
            $('.delete-reply').on('click', function(event) {
                var that = this;
                layer.confirm('确定删除该回复么？', {
                    title: '提醒',
                    btn: ['确定','取消']
                }, function() {
                    $(that).attr('disabled', 'disabled');
                    $.post('/delete/reply/' + $(that).data('pid'), {
                        _csrf: _csrf
                    }, function(data) {
                        if (data.status == 'success') {
                            layer.msg(data.data, {icon: 1});
                            setTimeout(function() {
                                $("#reply-"+$(that).data('pid')).hide('fast');
                            }, 300);
                        } else {
                            layer.msg(data.data, {icon: 2});
                        }
                        $(that).removeAttr('disabled');
                    });
                }, function() {
                });
            });
        });
    </script>
