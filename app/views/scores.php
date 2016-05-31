{$headerLayout}
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                    <a href="/">主页</a>
                </li>
                <li>
                    <a href="/user">我的主页</a>
                </li>
                <li>
                    <strong>积分明细</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div id="scores-info" class="row profile-info" style="display: none;">
            <div class="col-md-12">
                <div class="panel blank-panel">
                    <div class="panel-heading ibox-title">
                        <div class="panel-title m-b-md" style="padding: 0 0 5px 0; margin-bottom: 0;">
                            <h4>当前积分余额 <span class="text-success">{$user.score}</span></h4>
                            <small>（只展现最近100条记录）</small>
                        </div>
                    </div>
                    <div class="panel-body ibox-content ibox float-e-margins">
                        <div class="tab-content">
                            <div class="feed-activity-list" v-for="score in scores">
                                <div class="feed-element">
                                    <div class="media-body ">
                                        <small>{{score.add_time}}</small> &nbsp; <span>{{score.reason}}</span><small v-if="score.tid > 0">（<a href="/read/{{score.tid}}">{{score.title}}</a>）</small>，<span v-bind:class="{'text-danger': score.changed > 0, 'text-success': score.changed < 0}"><span v-if="score.changed > 0">+</span>{{score.changed}}</span> 积分，余额 <strong>{{score.remain}}</strong> 积分
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            
    {$footerLayout}
    <script type="text/javascript">
        seajs.use("user", function(user) {
            user.scores({
                scores: {:json_encode($data)}
            });
        });
    </script>
</body>
</html>