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
                    <span class="pull-right" style="margin-right: 15px;">共有 {$count} 提现申请</span>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            用户
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            提现
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            备注
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            操作
                        </div>
                    </div>
                    <div class="table-body articles">
                        {loop $withdraws $row}
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <img src="{$row.avatar}" class="avatar"/>
                                <a href="/user/{$row.uid}" target="_blank">{$row.username}</a><br />
                                {if $row['status'] == 0}
                                <span class="label label-danger">{$row.statusText}</span>
                                {else}
                                    {if $row['status'] == 1}
                                    <span class="label label-success">{$row.statusText}</span>
                                    {else}
                                    <span class="label label-default">{$row.statusText}</span>
                                    {/if}
                                {/if}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <span>提现 <b>{$row.score}</b> 积分（含手续费 200 积分）</span><br />
                                <span>应该支付 <b>{$row.should_pay}</b> 元</span><br />
                                <span>支付宝账号：<b>{$row.pay_account}</b></span>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                {if empty($row['remark'])}
                                <span class="remark">（暂无备注）</span>
                                {else}
                                <span>{$row.remark}</span>
                                {/if}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                {if $row['status'] == 0}
                                    <a href="javascript:doReview({$row.id});" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> 审核</a>
                                {else}
                                    <a disabled="disabled" class="btn btn-default btn-xs">已处理</a>
                                {/if}
                                <a href="/admin/user-score-records/{$row.uid}" target="_blank" class="btn btn-warning btn-xs" data-placement="top" title="该用户积分详情"><i class="fa fa-inbox"></i> 详情</a>
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
        var _csrf = $('meta[name=_csrf]').attr('content');
        $(document).ready(function() {
            $('[data-placement="top"]').tooltip();
            layer.config({
                path: '/app/views/vendor/layer/',
                extend: 'extend/layer.ext.js'
            });
            var page = {$page},
            per = {$per},
            pages = Math.ceil({$count} / per),
            href = '/admin/withdraws/';
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
        });
        // 审核提现
        function doReview(id) {
            //询问框
            layer.confirm('是否通过提现审核？', {
                title: '提醒',
                btn: ['通过且已转账','拒绝','取消'], //按钮
                btn1: function() {
                    $.post('/admin/review/withdraw', {
                        id: id,
                        status: 1,
                        _csrf: _csrf,
                    }, function(data) {
                        if (data.status == 'success') {
                            layer.msg(data.data, {icon: 1});
                            setTimeout(function () {
                                window.location.reload();
                            }, 800);
                        } else {
                            layer.msg(data.data, {icon: 2});
                        }
                    }, 'json');
                },
                btn2: function() {
                    layer.prompt({
                        title: '拒绝原因',
                        formType: 0,
                    }, function(remark){
                        $.post('/admin/review/withdraw', {
                            id: id,
                            status: 2,
                            remark: remark,
                            _csrf: _csrf,
                        }, function(data) {
                            if (data.status == 'success') {
                                layer.msg(data.data, {icon: 1});
                                setTimeout(function () {
                                    window.location.reload();
                                }, 800);
                            } else {
                                layer.msg(data.data, {icon: 2});
                            }
                        }, 'json');
                    }, '请输入拒绝原因或备注');
                },
                btn3: function(index, layero) {
                    layer.close(index);
                }
            });
        }
    </script>
