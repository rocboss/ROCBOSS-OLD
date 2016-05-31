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
                    <form class="form-horizontal" style="margin: 100px auto; width: 600px;">
                        <input type="hidden" name="_csrf" value="{:md5(Roc::request()->cookies->roc_secure)}"/>
                        <div class="form-group">
                            <label for="sitename" class="col-sm-2 control-label">网站名称</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control" id="sitename" name="sitename" placeholder="网站站点名" value="{$system.sitename}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="keywords" class="col-sm-2 control-label">关键词</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control" id="keywords" name="keywords" placeholder="SEO关键词" value="{$system.keywords}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description" class="col-sm-2 control-label">网站描述</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control" id="description" name="description" placeholder="网站描述" value="{$system.description}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="rockey" class="col-sm-2 control-label">安全秘钥</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control" id="rockey" name="rockey" placeholder="不少于12位的随机字符串" value="{$system.rockey}">
                              <span class="help-block" style="color: #6b0a12; font-size: 12px;">从安全角度考虑，建议定期更换。更改该项会强制当前登录账户退出</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              <button type="button" class="btn btn-success save-system-setting">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- 底部 -->
                {$footerLayout}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".save-system-setting").on('click', function(event) {
                $.post('/backend/admin/save-system-setting', $('form').serialize(), function(data) {
                    if (data.status == 'success') {
                        layer.msg(data.msg, {icon: 1});
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    } else {
                        layer.msg(data.msg);
                    }
                }, 'json');
            });
        });
    </script>
