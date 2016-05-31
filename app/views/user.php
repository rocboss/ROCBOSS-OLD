{$headerLayout}
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                    <a href="/">主页</a>
                </li>
                <li>
                    <strong>会员资料</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div id="profile-info" class="row profile-info" style="display: none;">
            <div class="col-md-8">
                <div class="panel blank-panel">
                    <div class="panel-heading ibox-title">
                        <div class="panel-title m-b-md">
                            <h4>{if $user['uid'] != $loginInfo['uid']}TA{else}我{/if}的动态</h4>
                        </div>
                        <div class="panel-options">

                            <ul class="nav nav-tabs profile-type">
                                <li class="active">
                                    <a data-toggle="tab" href="#tab-topic" aria-expanded="true">
                                        话题
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#tab-reply" aria-expanded="false">
                                        回复
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#tab-article" aria-expanded="false">
                                        文章
                                    </a>
                                </li>
                                {if $user['uid'] == $loginInfo['uid']}
                                <li>
                                    <a data-toggle="tab" href="#tab-collection">
                                        收藏
                                    </a>
                                </li>
                                {/if}
                                <li>
                                    <a data-toggle="tab" href="#tab-fans">
                                        粉丝
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel-body ibox-content ibox float-e-margins">

                        <div class="tab-content">
                            <div id="tab-topic" class="tab-pane active">
                                {if empty($topics)}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}

                                <div class="feed-activity-list" v-for="topic in topics">
                                    <div class="feed-element">
                                        <div class="media-body ">
                                            <small class="pull-right text-navy">
                                                <small class="pull-right">
                                                    <span class="praise-num">
                                                       <span class="ico">
                                                           <i class="fa fa-thumbs-o-up"></i>
                                                       </span>
                                                       {{ topic.praise_num }} &nbsp;
                                                    </span>
                                                </small>
                                            </small>
                                            发布了话题 “<strong><a href="/read/{{ topic.tid }}">{{ topic.title }}</a></strong>”
                                            <br>
                                            <small class="text-muted">{{ topic.post_time }} 来自 {{ topic.client }}</small>
                                        </div>
                                    </div>
                                </div>
                                <a class="load-more-topic btn btn-primary btn-block" v-show="{:!empty($topics)}"><i class="fa fa-arrow-down"></i> 显示更多</a>
                            </div>

                            <div id="tab-reply" class="tab-pane">
                                {if empty($replys)}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}

                                <div class="feed-activity-list" v-for="reply in replys">
                                    <div class="feed-element">
                                        <div class="media-body ">
                                            <small class="pull-right">
                                                <a href="/read/{{ reply.tid }}#reply-{{ reply.pid }}"><i class="fa fa-mail-forward"></i> 查看</a>
                                            </small>
                                            回复话题 “<strong><a href="/read/{{ reply.tid }}">{{ reply.topic_title }}</a></strong>”
                                            <div style="color: #999; margin-top: 6px;">
                                                {{{ reply.content }}}
                                            </div>
                                            <small class="text-muted">{{ reply.post_time }} 来自 {{{ reply.client }}}</small>
                                            <div class="well" v-show="reply.at_pid > 0">
                                                <p>引用 <strong>{{ reply.at_reply.username }}</strong> 的评论</p>
                                                <p class="ellipsis">
                                                {{{ reply.at_reply.content }}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a class="load-more-reply btn btn-primary btn-block" v-show="{:!empty($replys)}"><i class="fa fa-arrow-down"></i> 显示更多</a>
                            </div>

                            <div id="tab-article" class="tab-pane">
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                            </div>

                            <div id="tab-collection" class="tab-pane">
                                {if empty($collections)}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}

                                <div class="feed-activity-list" v-for="topic in collections">
                                    <div class="feed-element">
                                        <div class="media-body ">
                                            <small class="pull-right text-navy">
                                                <small class="pull-right">
                                                    <span class="praise-num">
                                                       <span class="ico">
                                                           <i class="fa fa-thumbs-o-up"></i>
                                                       </span>
                                                       {{ topic.praise_num }} &nbsp;
                                                    </span>
                                                </small>
                                            </small>
                                            <strong><a href="/read/{{ topic.tid }}">{{ topic.title }}</a></strong>
                                            <br>
                                            <small class="text-muted">{{ topic.post_time }} 来自 {{ topic.client }}</small>
                                        </div>
                                    </div>
                                </div>
                                <a class="load-more-collection btn btn-primary btn-block" v-show="{:!empty($collections)}"><i class="fa fa-arrow-down"></i> 显示更多</a>
                            </div>

                            <div id="tab-fans" class="tab-pane">
                                {if empty($fans)}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}
                                <div class="col-lg-6" v-for="fan in fans">
                                    <div class="contact-box">
                                        <a href="/user/{{ fan.uid }}">
                                            <div class="col-sm-4">
                                                <div class="text-center">
                                                    <img alt="image" class="img-circle m-t-xs img-responsive" :src="fan.avatar">
                                                </div>
                                            </div>
                                            </a>
                                            <div class="col-sm-8">
                                                <a href="/user/{{ fan.uid }}">
                                                    <h3 class="ellipsis ar-mg"><strong>{{ fan.username }}</strong></h3>
                                                </a>
                                            </div>
                                            <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <a class="load-more-fans btn btn-primary btn-block" v-show="{:!empty($fans)}"><i class="fa fa-arrow-down"></i> 显示更多</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>会员资料</h5>
                        {if $user['uid'] == $loginInfo['uid']}
                        <div class="pull-right">
                            <a href="/profile"><i class="fa fa-edit"></i> 编辑</a>
                        </div>
                        {/if}
                    </div>
                    <div>
                        <div class="ibox-content no-padding border-left-right avatar-banner">
                            <img alt="image" class="img-responsive" src="/app/views/img/pbg.jpg">
                            <div class="profile-shade">
                                <img class="img-circle profile-big-avatar" src="{$avatar}" alt="image">
                            </div>
                        </div>
                        <div class="ibox-content profile-content">
                            <h4>
                                <strong>{$user.username}</strong>
                                <span class="text-warning" style="margin-top: -20px;">
                                    {if $user['groupid'] != 99}
                                        <i class="iconfont icon-vdengji"></i><span class="sm-text">{$user.groupid}</span>
                                    {else}
                                        <i class="iconfont icon-guanliyuan"></i><span class="sm-text">管理员</span>
                                    {/if}
                                </span>
                            </h4>
                            <p><a href="/scores"><strong>{$user.score}</strong></a> 积分 {if $user['uid'] == $loginInfo['uid']}<a class="doRecharge btn btn-xs btn-danger">充值</a> <a class="doUpgrade btn btn-xs btn-success" data-v2="{$v2Price}" data-v3="{$v3Price}"><i class="fa fa-credit-card"></i> 升级VIP</a>{/if}</p>
                            <div class="row m-t-lg" style="border-top: 1px solid #EFEFEF; padding: 30px 0;">
                                <div style="width: 33.3%; float: left;">
                                    <span class="bar"><strong>{$statistic.topic}</strong></span>
                                    <h5>主题</h5>
                                </div>
                                <div style="width: 33.3%; float: left;">
                                    <span class="line"><strong>{$statistic.article}</strong></span>
                                    <h5>文章</h5>
                                </div>
                                <div style="width: 33.3%; float: left;">
                                    <span class="bar"><strong>{$statistic.fans}</strong></span>
                                    <h5>粉丝</h5>
                                </div>
                            </div>
                            {if $user['uid'] != $loginInfo['uid']}
                            <div class="user-button">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="do-whisper btn btn-primary btn-sm btn-block" data-at_uid="{$user.uid}" data-whisper="{:Roc::get('system.score.whisper')}"><i class="fa fa-envelope"></i> 发送消息</a>
                                    </div>
                                    <div class="col-md-6">
                                        {if $is_fans > 0}
                                            <a class="do-follow btn btn-danger btn-sm btn-block" data-fuid="{$user.uid}"><i class="fa fa-heart"></i> 取消关注TA</a>
                                        {else}
                                            <a class="do-follow btn btn-danger btn-sm btn-block" data-fuid="{$user.uid}"><i class="fa fa-heart"></i> 关注TA</a>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {$footerLayout}
    <script type="text/javascript">
        seajs.use("user", function(user) {
            user.init({
                uid: {$user.uid},
                topics: {:json_encode($topics)},
                replys: {:json_encode($replys)},
                collections: {:json_encode($collections)},
                fans: {:json_encode($fans)},
            });
        });
    </script>
</body>
</html>
