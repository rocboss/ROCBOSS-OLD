<div class="turn-light">
    <a id="turn-light"><i class="fa fa-lightbulb-o"></i></a>
</div>
<footer class="main-footer">
  <div class="pull-right hidden-xs">
    浙ICP备15008828号-2. 公安部备案 32010502010016. <b>Version</b> 2.2.1 Beta
  </div>
  <strong>Copyright &copy; 2016 <a href="https://www.rocboss.com" target="_blank">ROCBOSS</a>.</strong> All rights
  reserved.
</footer>
{if Roc::get('system.webpack_debug')}
<script src="http://localhost:8080/webpack-dev-server.js"></script>
<script src="http://localhost:8080/web/hot/{$asset}.min.js"></script>
{else}
<script src="/dist/{$asset}.min.js"></script>
{/if}
<script type="text/javascript">
    // 百度统计代码
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?48042604b3c7a9973810a87540843e34";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(hm, s);
    })();
</script>
