{$headerLayout}
<style media="screen">
    .users .col-sm-2 {
        border-left: 1px dashed #ddd;
        height: 40px;
    }
</style>
    <div id="wrap">
        <!-- 左侧菜单栏目 -->
        {$sidebarLayout}
        <!-- 右侧内容 -->
        <div id="right-content">
            <a class="toggle-btn">
                <i class="fa fa-navicon"></i>
            </a>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="check-div form-inline">
                    <span class="pull-right" style="margin-right: 15px;">UID {$uid} 用户，最新300条积分变动记录</span>
                </div>
                <div class="data-div">
                    <div class="row table-header">
                        <div class="col-sm-3">
                            时间
                        </div>
                        <div class="col-sm-5">
                            原因
                        </div>
                        <div class="col-sm-2">
                            变动
                        </div>
                        <div class="col-sm-2">
                            余额
                        </div>
                    </div>
                    <div class="table-body users">
                        {loop $scores $score}
                        <div class="row">
                            <div class="col-sm-3">
                                {$score.add_time}
                            </div>
                            <div class="col-sm-5">
                                {$score.reason}
                            </div>
                            <div class="col-sm-2">
                                {$score.changed}
                            </div>
                            <div class="col-sm-2">
                                {$score.remain}
                            </div>
                        </div>
                        {/loop}
                    </div>
                </div>
                <!-- 底部 -->
                {$footerLayout}
            </div>
        </div>
    </div>
