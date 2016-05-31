{$headerLayout}
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="ibox-content m-b-sm border-bottom">
            <div class="p-xs">
                <div class="pull-left m-r-md navy-bg" style="border-radius: 50%;">
                    <img src="{:'/'.Roc::get('system.views.path').'/'}img/logo.png" alt="" width="60">
                </div>
                <h2>欢迎来到{$seo.sitename}</h2>
                <span>{$seo.description}</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title fc">
                        <span class="fc-button sort-choice" data-sort="tid" v-bind:class="{'fc-state-active' : sort == 'tid'}">最新发表</span>
                        <span class="fc-button sort-choice" data-sort="last_time" v-bind:class="{'fc-state-active' : sort == 'last_time'}">最后回复</span>
                        <span class="fc-button sort-choice" data-sort="essence" v-bind:class="{'fc-state-active' : sort == 'essence'}">只看精华</span>
                        <span id="requesting" class="pull-right" style="color: #B18E6B; display: none;">
                            <i class="fa fa-spinner fa-spin"></i> 加载中
                        </span>
                        <div class="clearfix"></div>
                    </div>
                    <div id="topic-list" class="ibox-content" style="border-top: none;">
                        <div class="feed-activity-list" v-lazyload="topics">

                            <template v-for="topic in topics">
                                <div class="feed-element">
                                    <a href="/user/{{ topic.uid }}" class="pull-left">
                                        <img class="topic-avatar" src="/app/views/img/loading.gif" data-original="{{ topic.avatar }}">
                                    </a>
                                    <div class="media-body ">
                                        <div class="topic-status pull-right">
                                            <div class="c-ico">
                                                <i class="fa fa-commenting"></i>
                                            </div>
                                            <div class="c-num">
                                                {{ topic.comment_num }}
                                            </div>
                                        </div>
                                        <strong>
                                            <a href="/read/{{ topic.tid }}" class="topic-title">
                                                {{ topic.title }}
                                                <span class="text-danger" v-show="topic.is_top > 0">[置顶]</span>
                                            </a>
                                        </strong>
                                        <p class="topic-info">
                                            <small class="text-muted">
                                                <i v-if="topic.imageCount > 0" class="fa fa-camera"></i>
                                                <span class="topic-username">{{ topic.username }}</span>
                                                {{ topic.post_time }}
                                            </small>
                                            <template v-if="topic.location != ''">
                                                <small class="text-muted" style="margin-left: 10px;">
                                                    <i class="fa fa-map-marker"></i>
                                                    {{ topic.location }}
                                                </small>
                                            </template>
                                        </p>
                                    </div>
                                </div>
                            </template>

                            <div id="pagination" class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sidebar" class="col-md-4">
                <div class="panel blank-panel">

                    <div class="ibox-content profile-content">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content mailbox-content">
                                <div class="clubs">
                                    {if ($loginInfo['uid'] > 0)}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a class="btn btn-success" href="/newTopic" style="width: 100%">
                                                <i class="fa fa-pencil"></i> 发 帖
                                            </a>
                                        </div>
                                        <div class="clear clearfix"></div>
                                    </div>
                                    <div class="space-15"></div>
                                    {/if}
                                    <a class="btn btn-block{if ($active == 'index-0')} btn-primary{else} btn-white{/if}" href="/">全部</a>
                                    {loop $clubs $club}
                                    <a class="btn btn-block{if ($active == 'index-'.$club['cid'])} btn-primary{else} btn-white{/if}" href="/{$club.cid}/1">{$club.club_name}</a>
                                    {/loop}

                                    <div class="space-25"></div>
                                    <h5 class="statistics-title"># 本站统计</h5>
                                    <div class="row statistics">
                                        <div class="col-md-4" style="width: 33.3%;float: left;">
                                            <span class="bar">{:join(', ', str_split($statistic['user'], 1))}</span>
                                            <h5><strong>{$statistic.user}</strong> 会员</h5>
                                        </div>
                                        <div class="col-md-4" style="width: 33.3%;float: left;">
                                            <span class="line">{:join(', ', str_split($statistic['topic'], 1))}</span>
                                            <h5><strong>{$statistic.topic}</strong> 主题</h5>
                                        </div>
                                        <div class="col-md-4" style="width: 33.3%;float: left;">
                                            <span class="bar">{:join(', ', str_split($statistic['article'], 1))}</span>
                                            <h5><strong>{$statistic.article}</strong> 文章</h5>
                                        </div>
                                        <div class="clear clearfix"></div>
                                    </div>

                                    <div class="space-25"></div>
                                    <h5 class="statistics-title"># 本站邻居</h5>
                                    <ul class="tag-list" style="padding: 0">
                                        {loop $links $link}
                                            <li>
                                                <a href="{$link.url}" target="_blank">{$link.name}</a>
                                            </li>
                                        {/loop}
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {$footerLayout}
    <script type="text/javascript">
        var roc;
        // 加载入口模块
        seajs.use("index", function(index) {
            index.init({
                sort: '{$data.sort}',
                topics: {:json_encode($data['rows'])},
                page: {$data.page},
                cid: {$data.cid},
                per: {$data.per},
                total: {$data.total}
            });
        });
    </script>
</body>
</html>
