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
                    <span class="pull-right" style="margin-right: 15px;">共有 {$count} 文章</span>
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
                    <div class="table-body articles">
                        {loop $articles['rows'] $article}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <img src="{$article.poster}" class="poster"/>
                                <span>{$article.username}</span>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                <span style="color: #86999e; margin-right: 10px;">{$article.post_time}</span>
                                <a href="javascript:previewArticle({$article.id})" target="_blank">{$article.title}</a>
                                <br>
                                {if $article['is_open'] == 1}
                                  <span class="label label-success">已审核</span>
                                {else}
                                  <span class="label label-danger">待审核</span>
                                {/if}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <a href="javascript:doReview({$article.id});" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> 审核</a>
                                <a href="javascript:doDelete({$article.id});" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
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
            var page = {$articles.page},
            per = {$articles.per},
            pages = Math.ceil({$articles.total} / per),
            href = '/admin/articles/';
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
        // 文章删除啊
        function doDelete(id) {
            layer.confirm('确定删除该文章么？', {
                title: '提醒',
                btn: ['确定','取消']
            }, function() {
                $.post('/delete/article/' + id, {
                    _csrf: _csrf
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
            }, function() {
            });
        }
        // 预览文章
        function previewArticle(id) {
            var load = layer.load(2);
            $.get('/admin/preview/article/'+id, function(data) {
                if (data.status == 'success') {
                    layer.open({
                      type: 1,
                      title: '文章详情',
                      skin: 'layui-layer-rim',
                      area: ['500px', '550px'],
                      content: '<div style="padding: 20px; overflow-x: wrap; width: 100%;word-wrap:break-word;word-break:break-all;">'+data.data+'</div>',
                      success: function() {
                          setTimeout(function() {
                            layer.close(load);
                          }, 300);
                      }
                    });
                }
            }, 'json');
        }
        // 审核文章
        function doReview(id) {
            //询问框
            layer.confirm('是否通过审核？', {
                btn: ['通过','拒绝','取消'], //按钮
                btn1: function() {
                    $.post('/admin/review/article', {
                        id: id,
                        is_open: 1,
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
                    $.post('/admin/review/article', {
                        id: id,
                        is_open: 0,
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
                btn3: function(index, layero) {
                    layer.close(index);
                }
            });
        }
    </script>
