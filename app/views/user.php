{$headerLayout}
  <div class="content-wrapper">
    <section class="content-header">
      <ol class="breadcrumb">
        <li><a href="/topic"><i class="fa fa-home"></i> 首页</a></li>
        <li class="active">{if $user['uid'] != $loginInfo['uid']}TA{else}我{/if}的主页</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <!-- 内容区 -->
        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li v-bind:class="{'active': nowTab == 'topics'}"><a href="#topics" v-on:click="changeTab($event, 'topics')">主题</a></li>
              <li v-bind:class="{'active': nowTab == 'replys'}"><a href="#replys" v-on:click="changeTab($event, 'replys')">回复</a></li>
              <li v-bind:class="{'active': nowTab == 'articles'}"><a href="#articles" v-on:click="changeTab($event, 'articles')">文章</a></li>
              <li v-bind:class="{'active': nowTab == 'fans'}"><a href="#fans" v-on:click="changeTab($event, 'fans')">粉丝</a></li>
              {if $user['uid'] == $loginInfo['uid']}
              <li v-bind:class="{'active': nowTab == 'follows'}"><a href="#follows" v-on:click="changeTab($event, 'follows')">关注</a></li>
              <li v-bind:class="{'active': nowTab == 'collections'}"><a href="#collections" v-on:click="changeTab($event, 'collections')">收藏</a></li>
              {/if}
            </ul>
            <div class="tab-content">
              <!-- 主题 -->
              <div class="tab-pane" id="topics" v-bind:class="{'active': nowTab == 'topics'}">
                  <div class="no-data" v-if="topics.length == 0">
                        暂无数据
                  </div>
                  <ul class="timeline timeline-inverse" v-if="topics.length > 0">
                    <template v-for="topic in topics">
                        <li class="time-label">
                            <span class="bg-gray">
                                {{ topic.post_time }}
                            </span>
                        </li>
                        <li>
                          <i class="fa fa-pencil bg-olive"></i>

                          <div class="timeline-item">
                            <span class="time hidden-xs">
                                <i class="fa fa-comments"></i> 评论 ({{ topic.comment_num }})
                            </span>
                            <span class="time hidden-xs">
                                <i class="fa fa-thumbs-o-up"></i> 点赞 ({{ topic.praise_num }})
                            </span>
                            <h3 class="timeline-header">发布了主题 <small class="margin-l-5" v-if="topic.client != ''">[来自 {{{ topic.client }}}]</small></h3>
                            <div class="timeline-body">
                                <a href="/read/{{ topic.tid }}" target="_blank"><h5>{{ topic.title }}</h5></a>
                            </div>
                          </div>
                        </li>
                    </template>

                    <li class="load-more-topic">
                        <i class="fa fa-angle-double-down bg-olive"></i>
                        <div class="timeline-item">
                          <button type="button" v-on:click="loadMoreTopic($event)" class="btn bg-olive btn-block">
                              <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多主题
                          </button>
                        </div>
                    </li>
                  </ul>
              </div>

              <!-- 回复 -->
              <div class="tab-pane" id="replys" v-bind:class="{'active': nowTab == 'replys'}">
                  <div class="no-data" v-if="replys.length == 0">
                        暂无数据
                  </div>
                  <ul class="timeline timeline-inverse" v-if="replys.length > 0">
                    <template v-for="reply in replys">
                        <li class="time-label">
                            <span class="bg-gray">
                                {{ reply.post_time }}
                            </span>
                        </li>
                        <li>
                          <i class="fa fa-comment bg-orange"></i>
                          <div class="timeline-item">
                            <h3 class="timeline-header">回复了主题 “<a href="/read/{{ reply.tid }}#reply-{{ reply.pid }}" target="_blank">{{ reply.topic_title }}</a>” <small class="margin-l-5" v-if="reply.client != ''">[来自 {{{ reply.client }}}]</small></h3>
                            <div class="timeline-body">
                                <div class="well" v-show="reply.at_pid > 0">
                                    <p>引用 <a href="/user/{{ reply.at_reply.uid }}"><strong>{{ reply.at_reply.username }}</strong></a> 的评论</p>
                                    <p class="ellipsis">
                                    {{{ reply.at_reply.content }}}
                                    </p>
                                </div>
                                {{{ reply.content }}}
                            </div>
                          </div>
                        </li>
                    </template>

                    <li class="load-more-reply">
                        <i class="fa fa-angle-double-down bg-orange"></i>
                        <div class="timeline-item">
                          <button type="button" v-on:click="loadMoreReply($event)" class="btn bg-orange btn-block">
                              <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多回复
                          </button>
                        </div>
                    </li>
                  </ul>
              </div>

              <!-- 文章 -->
              <div class="tab-pane" id="articles" v-bind:class="{'active': nowTab == 'articles'}">
                  <div class="no-data" v-if="articles.length == 0">
                        暂无数据
                  </div>
                  <template v-if="articles.length > 0">
                    <template v-for="row in articles">
                        <div class="article-list">
                          <div class="attachment-block clearfix">
                            <img class="attachment-img" v-bind:src="row.poster != '' ? row.poster : '/app/views/img/404.png'" alt="{{row.title}}">
                            <div class="attachment-pushed">
                              <h4 class="attachment-heading">
                                  <a href="/read/article-{{row.id}}">{{row.title}}</a>
                                  <small class="post-info pull-right"><a href="/user/{{row.uid}}">{{row.username}}</a> {{row.post_time}}</small>
                              </h4>
                              <div class="attachment-text">
                                {{row.content}}<a href="/read/article-{{row.id}}">[查看详情]</a>
                              </div>
                            </div>
                          </div>
                        </div>
                    </template>

                    <div class="timeline-item">
                      <button type="button" v-on:click="loadMoreArticle($event)" class="btn bg-olive btn-block">
                          <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多文章
                      </button>
                    </div>
                  </template>
              </div>

              <!-- 粉丝 -->
              <div class="tab-pane" id="fans" v-bind:class="{'active': nowTab == 'fans'}">
                  <div class="no-data" v-if="fans.length == 0">
                        暂无数据
                  </div>
                  <ul class="users-list row clearfix" v-if="fans.length > 0" v-lazyload="fans">
                    <template v-for="fan in fans">
                        <li class="fan col-md-3 col-sm-6 col-xs-12">
                          <img class="u-avatar" alt="{{ fan.username }}" src="/dist/img/loading.gif" data-original="{{ fan.avatar }}">
                          <a class="users-list-name" href="/user/{{ fan.uid }}">{{ fan.username }}</a>
                        </li>
                    </template>
                  </ul>
                  <div class="timeline-item" v-if="fans.length > 0">
                    <button type="button" v-on:click="loadMoreFans($event)" class="btn bg-gray btn-block">
                        <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多粉丝
                    </button>
                  </div>
              </div>

              <!-- 关注 -->
              <div class="tab-pane" id="follows" v-bind:class="{'active': nowTab == 'follows'}">
                  <div class="no-data" v-if="follows.length == 0">
                        暂无数据
                  </div>
                  <ul class="users-list row clearfix" v-if="follows.length > 0" v-lazyload="follows">
                    <template v-for="follow in follows">
                        <li class="fan col-md-3 col-sm-6 col-xs-12">
                          <img class="u-avatar" alt="{{ follow.username }}" src="/dist/img/loading.gif" data-original="{{ follow.avatar }}">
                          <a class="users-list-name" href="/user/{{ follow.fuid }}">{{ follow.username }}</a>
                        </li>
                    </template>
                  </ul>
                  <div class="timeline-item" v-if="follows.length > 0">
                    <button type="button" v-on:click="loadMoreFollows($event)" class="btn bg-gray btn-block">
                        <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多关注
                    </button>
                  </div>
              </div>

              <!-- 收藏 -->
              <div class="tab-pane" id="collections" v-bind:class="{'active': nowTab == 'collections'}">
                  <div class="no-data" v-if="collections.length == 0">
                        暂无数据
                  </div>
                  <div class="active" v-lazyload="collections" v-if="collections.length > 0">
                      <template v-for="row in collections">
                          <!-- 主题 -->
                          <div class="post" v-if="row.type == 'topic'">
                            <div class="user-block">
                              <a href="/user/{{ row.uid }}" class="user-link">
                                  <img class="topic-avatar u-avatar" src="/dist/img/loading.gif" data-original="{{ row.avatar }}" alt="{{ row.username }}">
                                  <span class="comment_num" v-if="row.comment_num > 0">{{ row.comment_num }}</span>
                              </a>
                              <span class="username">
                                <a href="/user/{{ row.uid }}">{{ row.username }}</a>
                                <div class="topic-status">
                                  <div class="c-ico">
                                      <i class="fa fa-thumbs-up"></i>
                                  </div>
                                  <div class="c-num">{{ row.praise_num }}</div>
                                </div>
                              </span>
                              <span class="description">
                                  <a href="/read/{{ row.tid }}" class="post-title">
                                    <h5>
                                        <span class="label label-success margin-r-5" v-show="row.is_top > 0">置顶</span>
                                        <span class="label label-warning margin-r-5" v-show="row.is_essence > 0">精华</span>
                                        {{ row.title }}
                                    </h5>
                                  </a>
                              </span>
                            </div>
                          </div>

                          <!-- 文章 -->
                          <div class="post" v-if="row.type == 'article'">
                              <div class="article-list">
                                <div class="attachment-block clearfix">
                                  <img class="attachment-img" v-bind:src="row.poster != '' ? row.poster : '/app/views/img/404.png'" alt="{{row.title}}">
                                  <div class="attachment-pushed">
                                    <h4 class="attachment-heading">
                                        <a href="/read/article-{{row.id}}">{{row.title}}</a>
                                        <small class="post-info pull-right"><a href="/user/{{row.uid}}">{{row.username}}</a> {{row.post_time}}</small>
                                    </h4>
                                    <div class="attachment-text">
                                      {{row.content}} <a href="/read/article-{{row.id}}">[查看详情]</a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          </div>
                      </template>
                  </div>
                  <div class="timeline-item" v-if="collections.length > 0">
                    <button type="button" v-on:click="loadMoreFollows($event)" class="btn bg-gray btn-block">
                        <i class="fa fa-angle-double-down margin-r-5"></i> 加载更多收藏
                    </button>
                  </div>
              </div>

            </div>
            <!-- /.tab-content -->
          </div>
        </div>

        <!-- 会员相关信息 -->
        <div class="col-md-4">
          <div class="box box-success">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="{$avatar}" alt="{$user.username}" onerror="javascript:this.src='https://dn-roc.qbox.me/avatar/0-avatar.png';">
              <h3 class="profile-username text-center">{$user.username}</h3>
              <p class="text-muted text-center">Join {:date('d/m, Y', $user['reg_time'])}</p>
              <ul class="list-group list-group-unbordered">
                  <li class="list-group-item">
                      <b><i class="fa fa-gift margin-r-5"></i> 等级</b>
                      <span class="text-warning pull-right">
                        {if $user['groupid'] != 99}
                            <i class="iconfont icon-vdengji"></i><span class="sm-text">{$user.groupid}</span>
                        {else}
                            <i class="iconfont icon-guanliyuan"></i><span class="sm-text">管理员</span>
                        {/if}
                      </span>
                  </li>
                  <li class="list-group-item">
                      <b><i class="fa fa-btc margin-r-5"></i> 积分</b> <a class="pull-right">{$user.score}</a>
                  </li>
              </ul>
              <!-- OPTIONS -->
              {if $user['uid'] == $loginInfo['uid']}
              <div style="text-align: center">
                <a href="/scores" class="btn btn-sm btn-danger"><i class="fa fa-file"></i> 积分明细</a>
                <a class="btn btn-sm btn-success" v-on:click="doRecharge($event)" title="支付宝充值" data-toggle="tooltip" data-placement="top">充值</a>
                <a class="btn btn-sm btn-primary" v-on:click="doWithdraw($event)" title="积分提现" data-toggle="tooltip" data-placement="top">提现</a>
                <a class="btn btn-sm bg-purple" v-on:click="doUpgrade($event, {$v2Price}, {$v3Price})">升级VIP</a>
              </div>
              {else}
              <div style="text-align: center">
                {if $is_fans > 0}
                    <a class="btn btn-sm btn-danger" v-on:click="doFollow($event, {$user.uid})"><i class="fa fa-heart margin-r-5"></i> 取消关注</a>
                {else}
                    <a class="btn btn-sm btn-danger" v-on:click="doFollow($event, {$user.uid})"><i class="fa fa-heart-o margin-r-5"></i> 关注TA</a>
                {/if}
                <a href="/chat-with-{$user.uid}" class="btn btn-sm btn-primary"><i class="fa fa-envelope-o margin-r-5"></i> 私信</a>
                <a v-on:click="doTransfer($event, {$user.uid})" class="btn btn-sm btn-success"><i class="fa fa-money margin-r-5"></i> 转账</a>
              </div>
              {/if}
            </div>
          </div>
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">More</h3>
            </div>
            <div class="box-body">
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item" style="border: none;">
                      <b>主题</b> <a class="pull-right">{$statistic.topic}</a>
                    </li>
                    <li class="list-group-item">
                      <b>回复</b> <a class="pull-right">{$statistic.reply}</a>
                    </li>
                    <li class="list-group-item">
                      <b>文章</b> <a class="pull-right">{$statistic.article}</a>
                    </li>
                    <li class="list-group-item">
                      <b>关注</b> <a class="pull-right">{$statistic.follows}</a>
                    </li>
                    <li class="list-group-item" style="border-bottom: none;">
                      <b>粉丝</b> <a class="pull-right">{$statistic.fans}</a>
                    </li>
                </ul>
            </div>
          </div>
        </div>
      </div>

    </section>
    <script type="text/javascript">
        var config = {
            uid: {$user.uid},
            score: {$user.score},
            phone: '{$user.phone}',
            topics: {:json_encode($topics)}
        };
    </script>
    {$footerLayout}
  </div>
</div>
</body>
</html>
