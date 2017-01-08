{$headerLayout}
<div class="content-wrapper">
    <section class="content-header">
        <ol class="breadcrumb">
          <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
          <li><a href="/user/{$user.uid}">{$user.username}</a></li>
          <li class="active">私信</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success direct-chat direct-chat-success">
                    <div class="box-header with-border">
                      <h3 class="box-title">与<a href="/user/{$user.uid}">{$user.username}</a>的私信</h3>
                    </div>

                    <div class="box-body">
                      <div class="direct-chat-messages">
                        <div class="load-more-whisper">
                            <a href="#" v-on:click="loadMoreWhisper($event, {$user.uid});"><i class="fa fa-angle-double-up"></i> 加载更多</a>
                        </div>
                        <template v-for="v in data.rows">
                            <div class="direct-chat-msg" v-bind:class="{'left': v.is_mine == 0, 'right' : v.is_mine == 1}">
                              <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-left" v-if="v.is_mine == 0">{$user.username}</span>
                                <span class="direct-chat-name pull-left" v-if="v.is_mine == 1">{$loginInfo.username}</span>
                                <span class="direct-chat-timestamp pull-right">{{v.time}}</span>
                              </div>
                              <img class="direct-chat-img" src="{$user.avatar}" v-if="v.is_mine == 0">
                              <img class="direct-chat-img" src="{$loginInfo.avatar}" v-if="v.is_mine == 1">
                              <div class="direct-chat-text">
                                {{v.content}}
                              </div>
                            </div>
                        </template>

                        <div id="end-whisper"></div>
                      </div>
                    </div>
                    <div class="box-footer">
                        <div class="input-group">
                          <input type="text" id="whisper-msg" name="message" placeholder="请输入私信内容（需消耗{:Roc::get('system.score.whisper')}积分）" class="form-control">
                          <span class="input-group-btn">
                            <button type="button" v-on:click="postWhisper($event);" class="btn btn-success btn-flat" data-at_uid="{$user.uid}">发送</button>
                          </span>
                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        var config = {
            data: {:json_encode($data)}
        };
    </script>
    {$footerLayout}
  </div>
</div>
</body>
</html>
