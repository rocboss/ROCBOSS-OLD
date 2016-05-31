{$headerLayout}
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
                    <a class="btn btn-success btn-sm pull-right add-club" style="margin: 10px 15px;" data-toggle="modal" data-target="#clubForm" data-cid="0">
                        <i class="fa fa-plus"></i> 新增分类
                    </a>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-sm-5">
                            分类名称
                        </div>
                        <div class="col-sm-2">
                            排序
                        </div>
                        <div class="col-sm-5">
                            操作
                        </div>
                    </div>
                    <div class="table-body users">
                        {loop $clubs $club}
                        <div class="row">
                            <div class="col-sm-5">
                                {$club.club_name}
                            </div>
                            <div class="col-sm-2">
                                {$club.sort}
                            </div>
                            <div class="col-sm-5">
                                <a class="btn btn-success btn-xs add-club" data-toggle="modal" data-target="#clubForm" data-cid="{$club.cid}" data-club-name="{$club.club_name}" data-sort="{$club.sort}">
                                    <i class="fa fa-edit"></i> 编辑分类
                                </a>
                                <a class="btn btn-danger btn-xs del-club" data-cid="{$club.cid}">
                                    <i class="fa fa-trash"></i> 删除分类
                                </a>
                            </div>
                        </div>
                        {/loop}
                    </div>
                </div>
                <!--修改资源弹出窗口-->
                <div class="modal fade" id="clubForm" role="dialog" aria-labelledby="modal-title">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <form class="form-horizontal">
                                        <input type="hidden" name="_csrf" value="{:md5(Roc::request()->cookies->roc_secure)}"/>
                                        <input type="hidden" name="cid" id="cid-val" value=""/>
                                        <div class="form-group ">
                                            <label for="clubName" class="col-xs-3 control-label">分类名：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="clubName" name="club_name">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="clubName" class="col-xs-3 control-label">排序：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="sort" name="sort" value="50">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-xs btn-white" data-dismiss="modal">取 消</button>
                                <button type="button" class="btn btn-xs btn-success save-club">保 存</button>
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
            $('#clubForm').on('show.bs.modal', function (event) {
                var target = $(event.relatedTarget);
                var cid = target.data('cid');
                var modal = $(this);
                modal.find('#cid-val').val(cid);
                modal.find('#modal-title').text(cid > 0 ? '编辑分类名' : '新增分类');
                if (cid > 0) {
                    modal.find('#clubName').val(target.data('clubName'));
                    modal.find('#sort').val(target.data('sort'));
                } else {
                    modal.find('#clubName').val('');
                    modal.find('#sort').val(50);
                }
                modal.find('.save-club').on('click', function(e) {
                    $.post('/backend/admin/post-club', modal.find('form').serialize(), function(data) {
                        if (data.status == 'success') {
                            modal.modal('toggle');
                            window.location.reload();
                        } else {
                            layer.msg(data.msg);
                        }
                    }, 'json');
                });
            });
            $(".del-club").on('click', function(event) {
                event.preventDefault();
                var cid = $(this).data(cid);
                var self = this;
                $.post('/backend/admin/del-club', {
                    _csrf: $('meta[name=_csrf]').attr('content'),
                    cid: cid
                }, function(data) {
                    if (data.status == 'success') {
                        $(self).parent().parent().hide('fast');
                    } else {
                        layer.msg(data.msg, {icon: 2});
                    }
                }, 'json');
            })
        });
    </script>
