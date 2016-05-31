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
                    <a class="btn btn-success btn-sm pull-right add-link" style="margin: 10px 15px;" data-toggle="modal" data-target="#linkForm" data-id="0">
                        <i class="fa fa-plus"></i> 新增链接
                    </a>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-sm-2">
                            链接名称
                        </div>
                        <div class="col-sm-6">
                            URL地址
                        </div>
                        <div class="col-sm-1">
                            排序
                        </div>
                        <div class="col-sm-3">
                            操作
                        </div>
                    </div>
                    <div class="table-body users">
                        {loop $links $link}
                        <div class="row">
                            <div class="col-sm-2">
                                {$link.name}
                            </div>
                            <div class="col-sm-6">
                                {$link.url}
                            </div>
                            <div class="col-sm-1">
                                {$link.sort}
                            </div>
                            <div class="col-sm-3">
                                <a class="btn btn-success btn-xs add-link" data-toggle="modal" data-target="#linkForm" data-id="{$link.id}" data-name="{$link.name}" data-url="{$link.url}" data-sort="{$link.sort}">
                                    <i class="fa fa-edit"></i> 编辑链接
                                </a>
                                <a class="btn btn-danger btn-xs del-link" data-id="{$link.id}">
                                    <i class="fa fa-trash"></i> 删除链接
                                </a>
                            </div>
                        </div>
                        {/loop}
                    </div>
                </div>
                <!--修改资源弹出窗口-->
                <div class="modal fade" id="linkForm" role="dialog" aria-labelledby="modal-title">
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
                                        <input type="hidden" name="id" id="id-val" value=""/>
                                        <div class="form-group ">
                                            <label for="name" class="col-xs-3 control-label">链接名：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="name" name="name">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="url" class="col-xs-3 control-label">链接URL：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="url" name="url">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="sort" class="col-xs-3 control-label">排序：</label>
                                            <div class="col-xs-8 ">
                                                <input type="text" class="form-control input-sm duiqi" id="sort" name="sort" value="50">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-xs btn-white" data-dismiss="modal">取 消</button>
                                <button type="button" class="btn btn-xs btn-success save-link">保 存</button>
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
            $('#linkForm').on('show.bs.modal', function (event) {
                var target = $(event.relatedTarget);
                var id = target.data('id');
                var modal = $(this);
                modal.find('#id-val').val(id);
                modal.find('#modal-title').text(id > 0 ? '编辑链接' : '新增链接');
                if (id > 0) {
                    modal.find('#name').val(target.data('name'));
                    modal.find('#url').val(target.data('url'));
                    modal.find('#sort').val(target.data('sort'));
                } else {
                    modal.find('#name').val('');
                    modal.find('#url').val('');
                    modal.find('#sort').val(50);
                }
                modal.find('.save-link').on('click', function(e) {
                    $.post('/backend/admin/post-link', modal.find('form').serialize(), function(data) {
                        if (data.status == 'success') {
                            modal.modal('toggle');
                            window.location.reload();
                        } else {
                            layer.msg(data.msg);
                        }
                    }, 'json');
                });
            });
            $(".del-link").on('click', function(event) {
                event.preventDefault();
                var id = $(this).data(id);
                var self = this;
                $.post('/backend/admin/del-link', {
                    _csrf: $('meta[name=_csrf]').attr('content'),
                    id: id
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
