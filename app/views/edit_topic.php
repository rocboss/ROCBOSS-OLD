{$headerLayout}
<style type="text/css">
    .col-lg-12 {
        position: inherit;
    }
</style>
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                    <a href="/">主页</a>
                </li>
                <li>
                    编辑主题
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row" style="margin: 20px 0px;">
                            {loop $clubs $club}
                                <span class="chooseClub btn btn-sm btn-default" style="margin-bottom: 5px;" data-cid="{$club.cid}">{$club.club_name}</span>
                            {/loop}
                            <div class="space-15"></div>
                            <input type="hidden" id="tid" value="{$topic.tid}">
                            <input type="text" id="title" placeholder="请输入标题" value="{$topic.title}" class="form-control">
                        </div>
                        <div class="row">
                            <textarea id="editor" class="editor" rows="20"></textarea>
                            <a class="more-input">
                                <i class="fa fa-angle-double-down"></i>
                            </a>
                            <a id="post-btn" href="javascript:;" class="btn btn-sm btn-primary submit-btn" style="margin: 15px"><i class="fa fa-check "></i> 提交 </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {$footerLayout}
    <script type="text/javascript">
        seajs.use("topic", function(topic) {
            topic.edit({
                txt: '{:str_replace("\n", "", $topic['content'])}'
            });
        });
    </script>
</body>
</html>
