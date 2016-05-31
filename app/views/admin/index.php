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
                <!-- 资源管理模块 -->
                <div class="check-div form-inline">
                    <div class="pull-right m-r15">
                        <button class="btn btn-white btn-xs clear-cache">清理缓存</button>
                    </div>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            名称
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            状态
                        </div>
                    </div>
                    <div class="table-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                服务器时间
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.time}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                服务器端口
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.port}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                服务器根域名
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.name}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                服务器系统
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.os}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                服务器引擎
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.software}/PHP {$server.version}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                数据库版本
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                MYSQL {:@mysql_get_server_info()}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                网站根目录
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.root}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                最大上传值
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.upload}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                当前占用内存
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                {$server.memory_usage}
                            </div>
                        </div>
                    </div>
                </div>

                <!--修改资源弹出窗口-->
                <div class="modal fade" id="changeSource" role="dialog" aria-labelledby="gridSystemModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="gridSystemModalLabel">修改资源</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <form class="form-horizontal">
                                        <div class="form-group ">
                                            <label for="sName" class="col-xs-3 control-label">名称：</label>
                                            <div class="col-xs-8 ">
                                                <input type="email" class="form-control input-sm duiqi" id="sName" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="sLink" class="col-xs-3 control-label">链接：</label>
                                            <div class="col-xs-8 ">
                                                <input type="" class="form-control input-sm duiqi" id="sLink" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="sOrd" class="col-xs-3 control-label">排序：</label>
                                            <div class="col-xs-8">
                                                <input type="" class="form-control input-sm duiqi" id="sOrd" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="sKnot" class="col-xs-3 control-label">父节点：</label>
                                            <div class="col-xs-8">
                                                <input type="" class="form-control input-sm duiqi" id="sKnot" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput1" class="col-xs-3 control-label">资源类型：</label>
                                            <div class="col-xs-8">
                                                <label class="control-label" for="anniu">
                                                    <input type="radio" name="leixing" id="anniu">菜单</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="control-label" for="meun">
                                                    <input type="radio" name="leixing" id="meun"> 按钮</label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-xs btn-white" data-dismiss="modal">取 消</button>
                                <button type="button" class="btn btn-xs btn-green">保 存</button>
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
