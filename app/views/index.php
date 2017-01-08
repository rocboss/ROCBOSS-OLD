{$headerLayout}
  <div class="content-wrapper">
    <!-- 因首屏异步渲染，单独针对爬虫给出链接 -->
    <div class="hide for-seo">
      {loop $data['rows'] $row}
        <div>
          <a href="/read/{$row.tid}" title="{$row.title}">
            <span>{$row.title}</span>
          </a>
        </div>
      {/loop}
      <?php
          $url = $data['cid'] == 0 ? '/page-:page.html': '/category-'.$data['cid'].'-:page.html';
          $allPage = ceil($data['total']/$data['per']);
          for ($p = 1; $p <= $allPage; $p++):
      ?>
      <a href="{:str_replace(':page', $p, $url)}">{$p}</a>
      <?php endfor; ?>
    </div>
    <section class="content-header">
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
        <li class="active">社区</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <!-- 主题区 -->
        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li v-bind:class="{'active' : sort == 'post_time'}"><a href="#" v-on:click="changeSort($event, 'post_time')">最新发表</a></li>
              <li v-bind:class="{'active' : sort == 'last_time'}"><a href="#" v-on:click="changeSort($event, 'last_time')">最后回复</a></li>
              <li v-bind:class="{'active' : sort == 'comment_num'}"><a href="#" v-on:click="changeSort($event, 'comment_num')">最多回复</a></li>
              <li v-bind:class="{'active' : sort == 'essence'}"><a href="#" v-on:click="changeSort($event, 'essence')">只看精华</a></li>
              <span id="requesting" class="pull-right">
                <i class="fa fa-spinner fa-spin"></i> 加载中
              </span>
              <div class="clearfix"></div>
            </ul>
            <div class="tab-content">
              <div class="active" v-lazyload="topics">
                <template v-for="topic in topics">
                    <div class="post">
                      <div class="user-block">
                        <a href="/user/{{ topic.uid }}" class="user-link">
                            <img class="topic-avatar" src="/dist/img/loading.gif" data-original="{{ topic.avatar }}" alt="{{ topic.username }}">
                            <span class="comment_num" v-if="topic.comment_num > 0">{{ topic.comment_num }}</span>
                        </a>
                        <span class="username">
                          <a href="/user/{{ topic.uid }}">{{ topic.username }}</a>
                          <div class="topic-status">
                            <div class="c-ico">
                                <i class="fa fa-thumbs-up"></i>
                            </div>
                            <div class="c-num">{{ topic.praise_num }}</div>
                          </div>
                        </span>
                        <span class="description">
                            <a href="/read/{{ topic.tid }}" class="post-title">
                              <h5>
                                  <span class="label label-success margin-r-5" v-show="topic.is_top > 0">置顶</span>
                                  <span class="label label-warning margin-r-5" v-show="topic.is_essence > 0">精华</span>
                                  {{ topic.title }}
                              </h5>
                            </a>
                        </span>
                      </div>
                    </div>
                </template>
                <div id="pagination" class="pagination"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
            <!-- 发帖入口 -->
            <div class="box box-success">
              <div class="box-body">
                  <ul class="list-group list-group-unbordered index-option">
                      <li class="list-group-item" style="border: none;">
                          <a href="/newTopic" class="btn">
                              <i class="fa fa-comments margin-r-5"></i> 发布主题
                          </a>
                      </li>
                      <li class="list-group-item" style="border-bottom: none;">
                          <a href="/newArticle" class="btn">
                              <i class="fa fa-file margin-r-5"></i> 文章投稿
                          </a>
                      </li>
                  </ul>
              </div>
            </div>
            <!-- 分类 -->
            <div class="box box-success">
                <div class="box-body">
                    <a class="btn btn-block btn-gray{if ($active == 'index-0')} active{/if}" href="/">全部</a>
                    {loop $clubs $club}
                      <a class="btn btn-block btn-gray{if ($active == 'index-'.$club['cid'])} active{/if}" href="/category-{$club.cid}-1.html">{$club.club_name}</a>
                    {/loop}
                </div>
            </div>
            <!-- 二维码.螺壳云 -->
            <div class="box box-success">
                <div class="box-header with-border">
                  <h5 class="box-title"><i class="fa fa-qrcode margin-r-5"></i> 扫一扫，关注我</h5>
                </div>
                <div class="box-body row">
                    <div class="col-md-12">
                        <img src="https://ask.luoke.io/static/qrcode.jpeg" style="width: 100%;">
                    </div>
                </div>
            </div>
            <!-- 统计 -->
            <div class="box box-success">
                <div class="box-header with-border">
                  <h5 class="box-title"><i class="fa fa-bar-chart margin-r-5"></i> 统计</h5>
                </div>
                <div class="box-body row statistics">
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
            </div>
            <!-- 邻居 -->
            <div class="box box-success">
                <div class="box-header with-border">
                  <h5 class="box-title"><i class="fa fa-link margin-r-5"></i> 邻居</h5>
                </div>
                <div class="box-body row">
                    <ul class="tag-list">
                        {loop $links $link}
                        <li><a href="{$link.url}" target="_blank">{$link.name}</a></li>
                        {/loop}
                    </ul>
                </div>
            </div>
            <!-- 合作 -->
            <div class="box box-success">
                <div class="box-header with-border">
                  <h5 class="box-title"><i class="fa fa-coffee margin-r-5"></i> 合作</h5>
                </div>
                <div class="box-body row">
                    <div class="col-md-12">
                        <span><i class="fa fa-envelope margin-r-5"></i> <a href="mailto:admin@rocboss.com">admin@rocboss.com</a></span>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </section>
    <script type="text/javascript">
        var obj = {
            sort: '{$data.sort}',
            topics: {:json_encode($data['rows'])},
            page: {$data.page},
            cid: {$data.cid},
            per: {$data.per},
            total: {$data.total}
        };
    </script>
    {$footerLayout}
  </div>
</div>

</body>
</html>
