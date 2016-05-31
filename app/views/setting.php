{$headerLayout}
    <link rel="stylesheet" type="text/css" href="/app/views/js/vendor/webuploader/webuploader.css" charset="utf-8">
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                    <a href="/">主页</a>
                </li>
                <li>
                    <strong>个人设置</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>个人设置</h5>
                </div>
                <div class="ibox-content">
                    <form method="get" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">头像</label>
                            <div class="col-sm-10">
                                <img class="my-avatar" src="{$loginInfo.avatar}" style="width: 100px; height: 100px; float: left;"/>
                                <div id="uploader-demo" style="float: left; margin: 30px 10px;">
                                    <span id="u-tips" class="text-default hide"><i class="fa fa-spinner fa-spin"></i> 上传中...</span>
                                    <div id="avatarPicker">更换头像</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">邮箱</label>

                            <div class="col-sm-10">
                                <input type="text" id="email" class="form-control" value="{$user.email}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">手机号</label>
                            <div class="col-sm-10">
                                <input type="text" id="phone" class="form-control" placeholder="请填写您真实的手机号" value="{$user.phone}">
                                <small class="help-block m-b-none">当用户给您发送私信时，您将会受到短信提醒</small>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">新密码</label>
                            <div class="col-sm-10">
                                <input type="password" id="new-password" name="new-password" class="form-control" placeholder="不修改请留空">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">重复</label>
                            <div class="col-sm-10">
                                <input type="password" id="re-password" name="re-password" class="form-control" placeholder="重复新密码">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="save-profile btn btn-primary" data-uid="{$user.uid}">保存信息</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {$footerLayout}
    <script type="text/javascript">
        seajs.use("user", function(user) {
            user.setting('{$data.avatarUploadToken}', '{$data.saveKey}');
        });
    </script>
</body>
</html>
