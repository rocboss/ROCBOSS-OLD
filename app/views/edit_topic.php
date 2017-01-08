{$headerLayout}
<div class="content-wrapper">
  <section class="content-header">
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
      <li><a href="/">社区</a></li>
      <li class="active">编辑主题主题</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="nav-tabs-custom">
                <div class="nav" style="padding: 20px 10px 0;">
                    <div class="form-group">
                        {loop $clubs $club}
                            <span class="chooseClub btn btn-sm {if $topic['cid'] == $club['cid']}bg-olive{else}bg-gray{/if}" style="margin-bottom: 5px;" data-cid="{$club.cid}">{$club.club_name}</span>
                        {/loop}
                        <input type="hidden" id="tid" value="{$topic.tid}">
                    </div>
                    <div class="form-group">
                        <input type="text" id="title" placeholder="请输入标题" value="{$topic.title}" class="form-control">
                    </div>
                    <div class="tab-content">
                        <div class="form-group">
                            <textarea id="editor" class="editor" rows="20"></textarea>
                            <a class="more-input">
                                <i class="fa fa-angle-double-down"></i>
                            </a>
                        </div>
                        <div class="form-group">
                            <button type="button" id="post-btn" href="javascript:;" class="btn bg-olive btn-block submit-btn"><i class="fa fa-paper-plane"></i> 提交</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>
  <script type="text/javascript">
      var config = {
          cid: {$topic.cid},
          txt: '{:str_replace("\n", "", $topic['content'])}'
      };
  </script>
  {$footerLayout}
</body>
</html>
