{$headerLayout}
<div class="content-wrapper">
  <section class="content-header">
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
        <li><a href="/article">文章</a></li>
      <li class="active">文章详情</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
              <div class="box-header with-border text-center">
                <h3 class="box-title">
                    {$data.title}
                </h3>
              </div>
              <div class="box-body">
                  <p class="info text-default text-center">
                    <img src="{$data.avatar}" alt="{$data.username}" class="img-circle img-responsive profile-small-avatar header-avatar">
                    <a href="/user/{$data.uid}">{$data.username}</a> 发布于 {$data.post_time}
                  </p>
                  <div class="content">{$data.content}</div>
                  <!-- 点赞墙 -->
                  <div class="praise-list" style="margin-top: 20px; border-top: 1px dashed #383838;">
                      <div class="box-body">
                          <div class="article-praise">
                              {if (!empty($data['praise']['rows']))}
                                  {loop $data['praise']['rows'] $praise}
                                  <a href="/user/{$praise.uid}" class="praise-user">
                                      <img alt="image" class="img-circle u-avatar" src="{$praise.avatar}">
                                  </a>
                                  {/loop}
                                  <span style="color: #999; line-height: 45px;">觉得很赞</span>
                              {else}
                                  <div class="no-data">还没有人点赞 ^_^</div>
                              {/if}
                              <div class="clear clear-fix"></div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="options text-center">
                {if $data['praise']['hasPraise']}
                    <a class="do-praise btn bg-olive" disabled="disabled"><i class="fa fa-thumbs-up"></i> 已点赞 </a>
                {else}
                    <a class="do-praise btn bg-olive"><i class="fa fa-thumbs-up"></i> 点赞 </a>
                {/if}
                <span class="or">or</span>
                {if $data['hasCollection']}
                    <a class="do-collection btn btn-danger"><i class="fa fa-star"></i> 已收藏</a>
                {else}
                    <a class="do-collection btn btn-danger"><i class="fa fa-star-o"></i> 收藏</a>
                {/if}
            </div>
        </div>
    </div>
  </section>
  <script type="text/javascript">
      var aid = {$data.id};
  </script>
  {$footerLayout}
</body>
</html>
