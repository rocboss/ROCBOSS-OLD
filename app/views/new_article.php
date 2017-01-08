{$headerLayout}
<link rel="stylesheet" type="text/css" href="/vendor/webuploader/webuploader.css" charset="utf-8">
<div class="content-wrapper">
  <section class="content-header">
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
      <li><a href="/article">文章</a></li>
      <li class="active">投稿</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="nav-tabs-custom">
                <div class="nav" style="padding: 20px 10px 0;">
                    <div class="form-group" style="display: inline-block; margin: 0;">
                        <input type="hidden" id="poster" name="poster" value=""/>
                        <img class="poster-img" src="" style="width: 90px; height: 68px; float: left; margin: 15px 0; display: none;"/>
                        <div id="uploader-demo" style="float: left; margin: 20px 0;">
                            <span id="u-tips" class="text-default hide"><i class="fa fa-spinner fa-spin"></i> 上传中...</span>
                            <div id="posterPicker">上传封面图（建议尺寸 360*272）</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" id="title" placeholder="请输入文章标题" class="form-control">
                    </div>
                </div>
                <div class="tab-content">
                    <div class="form-group">
                        <textarea id="editor" class="editor" rows="20"></textarea>
                        <a class="more-input">
                            <i class="fa fa-angle-double-down"></i>
                        </a>
                    </div>
                    <div class="form-group">
                        <button type="button" id="post-btn" href="javascript:;" class="btn bg-olive btn-block submit-btn"><i class="fa fa-paper-plane"></i> 提交 </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>
  <script type="text/javascript">
      var uploadToken = '{$data.uploadToken}';
      var saveKey = '{$data.saveKey}';
  </script>
  {$footerLayout}
</body>
</html>
