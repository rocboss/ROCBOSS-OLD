{$headerLayout}
<div class="content-wrapper">
  <section class="content-header">
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
      <li class="active">消息提醒</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
        <div class="wrapper wrapper-content">
            <div class="col-lg-12 col-md-12">
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">
                    <li v-bind:class="{'active': nowTab == 'unread'}"><a href="#unread" v-on:click="changeTab($event, 'unread')">所有未读</a></li>
                    <li v-bind:class="{'active': nowTab == 'notice'}"><a href="#notice" v-on:click="changeTab($event, 'notice')">已读通知</a></li>
                    <li v-bind:class="{'active': nowTab == 'whisper'}"><a href="#whisper" v-on:click="changeTab($event, 'whisper')">私信总览</a></li>
                  </ul>
                  <div class="tab-content">
                      <!-- 所有未读 -->
                      <div class="tab-pane" id="unread" v-bind:class="{'active': nowTab == 'unread'}" v-tooltip="unread">
                          <div class="no-data" v-if="unread.length == 0">
                                暂无数据
                          </div>

                          <template v-for="u in unread">
                              <dl id="{{u.type}}-{{u.id}}" class="dl-horizontal">
                                <dt>
                                    <a v-on:click="doRead(u.type, u.id)" class="do-read btn btn-xs bg-olive" title="标记为已读" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumb-tack"></i></a>
                                    <a v-on:click="doRead(u.type, u.id, u.tid, u.pid, u.uid)" class="do-read btn btn-xs btn-primary" title="查看" data-toggle="tooltip" data-placement="top"><i class="fa fa-mail-forward"></i></a>
                                </dt>
                                <dd>
                                    <span class="margin-r-10"></span>
                                    <span class="text-olive margin-r-5" v-if="u.type == 'notice'"><i class="fa fa-comments"></i></span>
                                    <span class="text-warning margin-r-5" v-if="u.type == 'whisper'"><i class="fa fa-envelope"></i></span>
                                    <span class="text-muted margin-r-5">{{u.time}}</span>
                                    <span>{{{u.title}}}</span>
                                </dd>
                              </dl>
                          </template>
                      </div>
                      <!-- 所有已读提醒 -->
                      <div class="tab-pane" id="notice" v-bind:class="{'active': nowTab == 'notice'}" v-tooltip="notice">
                          <div class="no-data" v-if="notice.length == 0">
                                暂无数据
                          </div>
                          <template v-for="n in notice">
                              <dl id="notice-{{n.id}}" class="dl-horizontal">
                                <dt>
                                    <a href="/read/{{n.tid}}#reply-{{n.pid}}" target="_blank" class="do-read btn btn-xs btn-primary" title="查看" data-toggle="tooltip" data-placement="top"><i class="fa fa-mail-forward"></i></a>
                                </dt>
                                <dd>
                                    <span class="margin-r-10"></span>
                                    <span class="text-olive margin-r-5"><i class="fa fa-comments"></i></span>
                                    <span class="text-muted margin-r-5">{{n.time}}</span>
                                    <span>{{{n.title}}}</span>
                                </dd>
                              </dl>
                          </template>
                          <div class="timeline-item" v-if="notice.length > 0">
                            <button type="button" v-on:click="loadMoreNotice($event)" class="btn bg-gray btn-block">
                                <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多提醒
                            </button>
                          </div>
                      </div>
                      <!-- 私信列表 -->
                      <div class="tab-pane" id="whisper" v-bind:class="{'active': nowTab == 'whisper'}" v-tooltip="whisper">
                          <div class="no-data" v-if="whisper.length == 0">
                                暂无数据
                          </div>
                          <ul class="msgs-list" v-if="whisper.length > 0">
                              <template v-for="w in whisper">
                                <li class="item">
                                  <div class="msg-status">
                                      <span class="label label-primary pull-right" v-if="w.at_uid != {$loginInfo.uid}">
                                        <i class="fa fa-arrow-up"></i>
                                        <span class="label" v-if="w.is_read == 0">对方未读</span>
                                        <span class="label" v-if="w.is_read == 1">对方已读</span>
                                      </span>
                                      <span class="label label-success pull-right" v-if="w.at_uid == {$loginInfo.uid}">
                                        <i class="fa fa-arrow-down"></i>
                                        <span class="label" v-if="w.is_read == 0">我未读</span>
                                        <span class="label" v-if="w.is_read == 1">我已读</span>
                                      </span>
                                  </div>
                                  <div class="msg-info">
                                    <a href="/chat-with-{{ w.at_uid != {$loginInfo.uid} ? w.at_uid : w.uid }}" class="msg-title" target="_blank">
                                        {{ w.title }}
                                        <span class="pull-right">{{w.time}}</span>
                                    </a>
                                        <span class="msg-description">
                                          {{ w.content }}
                                        </span>
                                  </div>
                                </li>
                              </template>
                              <div class="timeline-item" v-if="whisper.length > 0">
                                <button type="button" v-on:click="loadMoreWhisper($event)" class="btn bg-gray btn-block">
                                    <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多私信
                                </button>
                              </div>
                          </ul>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
  </section>
  <script type="text/javascript">
    var config = {
      unread: {:json_encode($unread)}
    };
  </script>
  {$footerLayout}
</body>
</html>
