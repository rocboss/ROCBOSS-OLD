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
                <div class="check-div form-inline">
                    <span class="pull-right" style="margin-right: 15px;">共有 {$count} 主题</span>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            用户
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                            标题
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            操作
                        </div>
                    </div>
                    <div class="table-body topics">
                        {loop $topics['rows'] $topic}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <img src="{$topic.avatar}" onerror="javascript:this.src='https://dn-roc.qbox.me/avatar/0-avatar.png';"/>
                                <span>{$topic.username}</span>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                <span style="background: #eee; color: #6ea2be; padding: 3px 5px; border-radius: 3px;">{$topic.club_name}</span>
                                <span style="color: #86999e; margin-right: 10px;">{$topic.post_time}</span>
                                <a href="/read/{$topic.tid}" target="_blank">{if $topic['is_top'] == 1}<span style="color: #d9534f;">【置顶】</span>{/if}{$topic.title}</a>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <a href="/edit/topic/{$topic.tid}" target="_blank" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                <a data-tid="{$topic.tid}" class="delete-topic btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
                            </div>
                        </div>
                        {/loop}
                    </div>
                    <div id="pagination" class="pagination" style="margin: 0 10px;"></div>
                </div>
                <!-- 底部 -->
                {$footerLayout}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            var _csrf = $('meta[name=_csrf]').attr('content');
            var page = {$topics.page},
            sort = '{$topics.sort}',
            cid = {$topics.cid},
            per = {$topics.per},
            pages = Math.ceil({$topics.total} / per),
            href = '/admin/topics/';
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
            $('.delete-topic').on('click', function(event) {
                var tid = $(this).data('tid');
                var self = this;
                layer.confirm('确定删除该主题么？', {
                    title: '提醒',
                    btn: ['确定','取消']
                }, function() {
                    $.post('/delete/topic/' + tid, {
                        _csrf: _csrf
                    }, function(data) {
                        if (data.status == 'success') {
                            layer.msg(data.data, {icon: 1});
                            setTimeout(function () {
                                $(self).parent().parent().hide('fast');
                            }, 100);
                        } else {
                            layer.msg(data.data, {icon: 2});
                        }
                    }, 'json');
                }, function() {
                });
            })
        });
    </script>
