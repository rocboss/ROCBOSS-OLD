{$headerLayout}
<style media="screen">
    .users .col-sm-2 {
        border-left: 1px dashed #ddd;
        height: 40px;
    }
</style>
    <div id="wrap">
        <!-- 左侧菜单栏目 -->
        {$sidebarLayout}
        <!-- 右侧内容 -->
        <div id="right-content">
            <a class="toggle-btn">
                <i class="fa fa-navicon"></i>
            </a>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="check-div form-inline">
                    <div class="col-lg-4 col-xs-5">
                        <form action="/admin/users">
                            <input type="text" name="username" class="form-control input-sm" id="search-username" value="{:Roc::request()->query->username}" placeholder="用户名" style="width: 100px!important;"> OR
                            <input type="text" name="uid" class="form-control input-sm" id="search-uid" value="{:Roc::request()->query->uid}" placeholder="用户UID" style="width: 80px!important;">
                            <button class="btn btn-white btn-sm do-search"><i class="fa fa-search margin-r-5"></i> 搜 索</button>
                        </form>
                    </div>
                    <span class="pull-right" style="margin-right: 15px;">共有 {$count} 用户</span>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-sm-3">
                            用户
                        </div>
                        <div class="col-sm-2">
                            邮箱
                        </div>
                        <div class="col-sm-2">
                            手机
                        </div>
                        <div class="col-sm-2">
                            注册时间
                        </div>
                        <div class="col-sm-2">
                            最后活跃
                        </div>
                        <div class="col-sm-1">
                            操作
                        </div>
                    </div>
                    <div class="table-body users">
                        {loop $users $user}
                        <div class="row">
                            <div class="col-sm-3">
                                <img class="avatar" src="{$user.avatar}" onerror="javascript:this.src='https://dn-roc.qbox.me/avatar/0-avatar.png';"/>
                                <span style="font-weight: bold; color: #f8ac59">{$user.group_name}</span>
                                <a href="/user/{$user.uid}" target="_blank" data-placement="top" title="{$user.score} 积分">{$user.username}</a>
                            </div>
                            <div class="col-sm-2">
                                {$user.email}
                            </div>
                            <div class="col-sm-2">
                                {$user.phone}
                            </div>
                            <div class="col-sm-2">
                                {:date('Y-m-d H:i:s', $user['reg_time'])}
                            </div>
                            <div class="col-sm-2">
                                {:date('Y-m-d H:i:s', $user['last_time'])}
                            </div>
                            <div class="col-sm-1">
                                <a class="btn btn-success btn-xs" data-toggle="modal" data-target="#editUser" data-info='{:json_encode($user)}' data-placement="top" title="编辑用户">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="/admin/user-score-records/{$user.uid}" target="_blank" class="btn btn-warning btn-xs" data-placement="top" title="积分详情">
                                    <i class="fa fa-inbox"></i>
                                </a>
                            </div>
                        </div>
                        {/loop}

                        <div id="pagination" class="pagination"></div>
                    </div>
                </div>

                <!--修改资源弹出窗口-->
                <div class="modal fade" id="editUser" role="dialog" aria-labelledby="modal-title">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="modal-title">编辑用户（UID <span id="uid"></span>，积分 <span id="score"></span>）</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <form class="form-horizontal">
                                        <input type="hidden" name="_csrf" value="{:md5(Roc::request()->cookies->roc_secure)}"/>
                                        <input type="hidden" name="uid" id="uid-val" value=""/>
                                        <div class="form-group ">
                                            <label for="username" class="col-xs-3 control-label">用户名：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="username" name="username">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="col-xs-3 control-label">邮箱：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="email" name="email">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone" class="col-xs-3 control-label">手机：</label>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm duiqi" id="phone" name="phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" class="col-xs-3 control-label">等级：</label>
                                            <div class="col-xs-8" style="margin-left: -25px;">
                                                <label class="control-label" for="v0">
                                                    <input type="radio" name="groupid" class="groupid" id="v0" value="0"> 禁言
                                                </label> &nbsp;
                                                <label class="control-label" for="v1">
                                                    <input type="radio" name="groupid" class="groupid" id="v1" value="1"> v1
                                                </label> &nbsp;
                                                <label class="control-label" for="v2">
                                                    <input type="radio" name="groupid" class="groupid" id="v2" value="2"> v2
                                                </label> &nbsp;
                                                <label class="control-label" for="v3">
                                                    <input type="radio" name="groupid" class="groupid" id="v3" value="3"> v3
                                                </label> &nbsp;
                                                <label class="control-label" for="v99">
                                                    <input type="radio" name="groupid" class="groupid" id="v99" value="99"> 管理员
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="col-xs-3 control-label">密码：</label>
                                            <div class="col-xs-8">
                                                <input type="password" class="form-control input-sm duiqi" id="password" name="password" placeholder="不修改密码请留空">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-xs btn-white" data-dismiss="modal">取 消</button>
                                <button type="button" class="btn btn-xs btn-success save-user">保 存</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- 底部 -->
                {$footerLayout}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('[data-placement="top"]').tooltip();
            var _csrf = $('meta[name=_csrf]').attr('content');
            var page = {$page},
            per = {$per},
            pages = Math.ceil({$count} / per),
            href = '/admin/users/(?)';
            {if !empty(Roc::request()->query->username)}
                href += '?username={:Roc::filter()->topicInWeb(Roc::request()->query->username)}';
            {else}
                {if !empty(Roc::request()->query->uid)}
                    href += '?uid={:intval(Roc::request()->query->uid)}';
                {/if}
            {/if}
            laypage.dir = '/dist/css/laypage.css';
            laypage({
                dir: '/dist/css/laypage.css',
                cont: 'pagination',
                pages: pages,
                curr: page,
                href: href,
                first: 1,
                last: pages,
                skin: 'molv',
                prev: '<',
                next: '>',
                groups: 15,
                jump: function(e, first) {
                    if (!first) {
                        window.location.href = href.replace('(?)', e.curr);
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
            });
            $('#editUser').on('show.bs.modal', function (event) {
                var target = $(event.relatedTarget);
                var info = target.data('info');
                var modal = $(this);
                modal.find('#uid').text(info.uid);
                modal.find('#score').text(info.score);
                modal.find('#uid-val').val(info.uid);
                modal.find('#username').val(info.username);
                modal.find('#email').val(info.email);
                modal.find('#phone').val(info.phone);
                modal.find('.groupid[value="'+info.groupid+'"]').attr('checked', 'checked');
                modal.find('.save-user').on('click', function(e) {
                    $.post('/backend/user/edit-user', modal.find('form').serialize(), function(data) {
                        if (data.status == 'success') {
                            modal.modal('toggle');
                            window.location.reload();
                        } else {
                            layer.msg(data.data);
                        }
                    }, 'json');
                });
            });
            $('.do-search').on('click', function(event) {
                var uid = parseInt($('#search-uid').val());
                if (uid > 0) {
                    window.location.href = "/admin/users?uid="+uid;
                } else {
                    window.location.href = "/admin/users";
                }
            });
        });
    </script>
