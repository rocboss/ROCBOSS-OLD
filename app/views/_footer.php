<div class="turn-light">
    <a href="javascript:turnLight();"><i class="fa fa-lightbulb-o"></i></a>
</div>
<footer class="main-footer">
  <div class="pull-right hidden-xs">
    浙ICP备15008828号-2. 公安部备案 32010502010016. <b>Version</b> 2.2.1 Beta
  </div>
  <strong>Copyright &copy; 2016 <a href="https://www.rocboss.com" target="_blank">ROCBOSS</a>.</strong> All rights
  reserved.
</footer>
<script src="{:'/'.Roc::get('system.views.path').'/'}js/sea.js?v=2.2.0"></script>
<script type="text/javascript">
    seajs.config({
        base: "{:'/'.Roc::get('system.views.path').'/'}",
        alias: {
            "jquery": "vendor/jquery-1.10.2.min",
            "bootstrap": "vendor/bootstrap.min",
            "lazyload": "vendor/jquery.lazyload.min.js",
            "webuploader": "vendor/webuploader/webuploader.js",
            "layer": "vendor/layer/layer",
            "laypage": "vendor/laypage",
            "vue": "vendor/vue.min",
            "fancybox": "vendor/jquery.fancybox",
            "wangEditor": "vendor/wangEditor.js",
            "peity": "vendor/jquery.peity.min",
            "vue": "vendor/vue.min",
            "laypage": "vendor/laypage",
            "highlight": "vendor/highlight.pack"
        },
        preload: ["jquery"]
    });
    function turnLight() {
        layer.load(2);
        $.post('/turn/light', {}, function(data) {
            if (data.status == 'success') {
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            }
        }, 'json');
    }
</script>
