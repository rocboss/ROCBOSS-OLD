{$headerLayout}
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                    <a href="/">主页</a>
                </li>
                <li>
                    <strong>我的提醒</strong>
                </li>
            </ol>
        </div>
    </div>
    <div id="notice-list" class="wrapper wrapper-content" style="display: none;">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="panel blank-panel">
                    <div class="panel-heading ibox-title">
                        <div class="panel-options">
                            <ul class="nav nav-tabs profile-type">
                                <li class="active">
                                    <a data-toggle="tab" href="#tab-unread" aria-expanded="true">
                                        未读
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#tab-notification" aria-expanded="false">
                                        通知
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#tab-whisper" aria-expanded="false">
                                        私信
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel-body ibox-content ibox float-e-margins">

                        <div class="tab-content">
                            <div id="tab-unread" class="tab-pane active">
                                {if empty($unread['unread_notification'])}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}

                                <div class="feed-activity-list" v-for="notice in unread_notification">
                                    <div id="notification-{{notice.id}}" class="feed-element">
                                        <div class="media-body ">
                                            {{ notice.title }}
                                            <br>
                                            <small class="text-muted"><i class="fa fa-bell-o"></i> {{ notice.time }}</small>
                                            <div class="pull-right">
                                                <a class="do-read btn btn-xs btn-white" data-id="{{notice.id}}" data-type="notification"><i class="fa fa-thumb-tack"></i></a>
                                                <a href="/read/{{notice.tid}}#reply-{{notice.pid}}" data-id="{{notice.id}}" data-type="notification" class="do-read btn btn-xs btn-white"><i class="fa fa-mail-forward"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="feed-activity-list" v-for="whisper in unread_whisper">
                                    <div id="whisper-{{whisper.id}}" class="feed-element">
                                        <div class="media-body ">
                                            {{ whisper.title }}
                                            <br>
                                            <small class="text-muted"><i class="fa fa-envelope-o"></i> {{ whisper.time }}</small>
                                            <div class="pull-right">
                                                <a class="do-read btn btn-xs btn-primary" data-id="{{whisper.id}}" data-type="whisper"><i class="fa fa-thumb-tack"></i></a>
                                                <a href="/user/{{whisper.uid}}" class="do-read btn btn-xs btn-primary" data-id="{{whisper.id}}" data-type="whisper"><i class="fa fa-envelope-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="tab-notification" class="tab-pane">
                                {if empty($notification)}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}

                                <div class="feed-activity-list" v-for="notice in notification">
                                    <div id="notification-{{notice.id}}" class="feed-element">
                                        <div class="media-body ">
                                            {{ notice.title }}
                                            <br>
                                            <small class="text-muted"><i class="fa fa-bell-o"></i> {{ notice.time }}</small>
                                            <div class="pull-right">
                                                <a href="/read/{{notice.tid}}#reply-{{notice.pid}}" data-id="{{notice.id}}" data-type="notification" class="btn btn-xs btn-white"><i class="fa fa-mail-forward"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a class="load-more-reply btn btn-primary btn-block" v-show="{:!empty($replys)}"><i class="fa fa-arrow-down"></i> 显示更多</a>
                            </div>

                            <div id="tab-whisper" class="tab-pane">
                                <div class="btn-group">
                                    <a class="switch-whisper btn btn-white" data-type="0" v-bind:class="{'active': isForMe}">发给我的</a>
                                    <a class="switch-whisper btn btn-white" data-type="1" v-bind:class="{'active': !isForMe}">我发送的</a>
                                </div>
                                {if empty($whisper)}
                                <div style="margin: 50px; text-align: center; color: #ccc;">
                                    暂无数据
                                </div>
                                {/if}

                                <div class="feed-activity-list" v-for="w in whisper">
                                    <div id="whisper-{{w.id}}" class="feed-element">
                                        <div class="media-body ">
                                            <p style="color: #999; border-bottom: 1px dashed #ccc;"><span class="text-warning" v-show="w.is_read != undefined && w.is_read != ''">[{{w.is_read}}]</span> {{ w.title }}</p>
                                            <p>{{ w.content }}</p>
                                            <small class="text-muted"><i class="fa fa-envelope-o"></i> {{ w.time }}</small>
                                            <div class="pull-right">
                                                <a href="/user/{{w.uid}}" class="btn btn-xs btn-primary" data-id="{{w.id}}" data-type="whisper"><i class="fa fa-envelope-o"></i></a>
                                                <a class="delete-whisper btn btn-xs btn-white" data-id="{{w.id}}"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
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
        seajs.use("notice", function(notice) {
            notice.init({
                unread_notification: {:json_encode($unread['unread_notification'])},
                unread_whisper: {:json_encode($unread['unread_whisper'])},
                notification: {:json_encode($notification)},
                whisper: {:json_encode($whisper)},
            });
        });
    </script>
</body>
</html>