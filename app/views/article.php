{$headerLayout}
<!-- 因首屏异步渲染，单独针对爬虫给出链接 -->
<div class="hide for-seo">
  {loop $data['rows'] $row}
      <a href="/read/article-{$row.id}">
        <h5>{$row.title}</h5>
      </a>
  {/loop}
  <?php
      $url = '/article/:page';
      $allPage = ceil($data['total']/$data['per']);
      for ($p = 1; $p <= $allPage; $p++):
  ?>
  <a href="{:str_replace(':page', $p, $url)}">{$p}</a>
  <?php endfor; ?>
</div>
<div class="content-wrapper">
  <section class="content-header">
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
      <li class="active">文章专区</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title">
                    <a href="/newArticle" class="btn btn-success"><i class="fa fa-pencil margin-r-5"></i> 我要投稿</a>
                </h3>
              </div>
              <div class="box-body">
                <template v-for="row in rows">
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
                <div id="pagination" class="pagination"></div>
              </div>
            </div>
        </div>
    </div>
  </section>
  <script type="text/javascript">
    var config = {
        rows: {:json_encode($data['rows'])},
        page: {$data.page},
        per: {$data.per},
        total: {$data.total},
    };
  </script>
  {$footerLayout}
</body>
</html>
