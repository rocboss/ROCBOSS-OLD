{$headerLayout}
<div class="content-wrapper">
    <section class="content-header">
        <ol class="breadcrumb">
          <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
          <li><a href="/user">用户中心</a></li>
          <li class="active">积分明细</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <div class="panel-title m-b-md" style="padding: 0 0 5px 0; margin-bottom: 0;">
                            <h4 class="box-title">当前积分余额 <strong class="text-warning">{$user.score}</strong></h4>
                            <small>（只展现最近100条记录）</small>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="score-list" v-for="score in scores">
                            <div class="feed-element">
                                <div class="media-body ">
                                    <small>{{score.add_time}}</small> &nbsp; <span>{{score.reason}}</span><small v-if="score.tid > 0">（<a href="/read/{{score.tid}}">{{score.title}}</a>）</small>，<span v-bind:class="{'text-danger': score.changed > 0, 'text-success': score.changed < 0}"><span v-if="score.changed > 0">+</span>{{score.changed}}</span> 积分，余额 <strong class="text-warning">{{score.remain}}</strong> 积分
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        var config = {
            scores: {:json_encode($data)}
        };
    </script>
    {$footerLayout}
  </div>
</div>
</body>
</html>
