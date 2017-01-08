{$headerLayout}
<div class="content-wrapper">
    <section class="content-header">
        <ol class="breadcrumb">
          <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
          <li class="active">搜索 <i class="fa fa-search"></i> {if !empty($error)}{$error}{else}{$data.q}{/if}</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div id="topic-list" class="ibox-content" style="border-top: none;">
                        <div class="feed-activity-list">
                            {if empty($data['rows'])}
                                <div class="no-data">暂无搜索结果</div>
                            {/if}
                            {loop $data['rows'] $topic}
                                <div class="post">
                                  <div class="user-block">
                                    <a href="/user/{$topic.uid}">
                                        <img class="img-circle img-bordered-sm topic-avatar" src="{$topic.avatar}" alt="{$topic.username}" onerror="javascript:this.src='https://dn-roc.qbox.me/avatar/0-avatar.png';">
                                    </a>
                                    <span class="username">
                                      <a href="/user/{$topic.uid}">{$topic.username}</a>
                                      <a href="/read/{$topic.tid}" class="pull-right btn-box-tool"><i class="fa fa-comments-o margin-r-5"></i> 评论 ({$topic.comment_num})</a>
                                    </span>
                                    <span class="description">
                                        {if $topic['location'] != ''}
                                            <i class="fa fa-map-marker margin-r-5"></i> {$topic.location} -
                                        {/if}
                                        {$topic.post_time}
                                    </span>
                                  </div>
                                  <a href="/read/{$topic.tid}" class="post-title">
                                    <h5>
                                        {if $topic['is_top'] > 0}
                                            <span class="label label-success margin-r-5">置顶</span>
                                        {/if}
                                        {:str_ireplace($data['q'], '<em>'.$data['q'].'</em>', $topic['title'])}
                                    </h5>
                                  </a>
                                  {if Roc::get('xs.server.switch')}
                                  <p>
                                      {:strip_tags($topic['content'], '<em>')}
                                  </p>
                                  {/if}
                                </div>
                            {/loop}

                            <div id="pagination" class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        var page = {$data.page},
            per = {$data.per},
            total = {$data.total},
            q = '{$data.q}';
    </script>
    {$footerLayout}
  </div>
</div>

</body>
</html>
