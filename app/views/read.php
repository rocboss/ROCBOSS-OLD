{$headerLayout}
  <div class="content-wrapper">
    <section class="content-header">
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-home"></i> 首页</a></li>
        <li><a href="/">社区</a></li>
        <li><a href="/{$data.cid}/1">{$data.club_name}</a></li>
        <li class="active">主题详情页</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <!-- 主题区 -->
        <div class="col-md-8">
            <div class="nav-tabs-custom">
                <div class="nav topic-d-title">
                    <h4>
                        {if $data['is_top'] > 0}<span class="label label-success margin-r-5">置顶</span>{/if}
                        {if $data['is_essence'] > 0}<span class="label label-warning margin-r-5">精华</span>{/if}
                        {$data.title}
                    </h4>
                </div>
                <div class="tab-content topic-d-content">
                  <div class="info">
                      <p>
                          <span class="topic-info margin-r-5"><a href="/user/{$data.uid}">{$data.username}</a></span>
                          <span class="topic-info margin-r-5"><i class="fa fa-clock-o"></i> {$data.post_time}</span>
                          {if (!empty($data['client']))}
                              <span class="topic-info margin-r-5"><i class="fa fa-safari"></i> {$data.client}</span>
                          {/if}
                          {if (!empty($data['location']))}
                              <span class="topic-info margin-r-5"><i class="fa fa-map-marker"></i> {$data.location}</span>
                          {/if}
                      </p>
                  </div>
                  <div class="content">
                    {$data.content}
                    {if $data['edit_time'] != $data['post_time'] && $data['edit_time'] != '1970年1月1日'}
                    <p class="text-warning margin-t-15">该主题最后编辑于 {$data.edit_time}</p>
                    {/if}
                  </div>
                  {if ($data['images']['count'] > 0)}
                    <div class="image-list">
                        {loop $data['images']['rows'] $img}
                        <a class="fancybox" data-fancybox-group="group" href="{$img}">
                            <img src="{$img}" alt="">
                        </a>
                        {/loop}
                    </div>
                  {/if}
                </div>
            </div>
            <div class="nav-tabs-custom">
                <!-- 操作 -->
                <div class="nav">
                    {if $loginInfo['uid'] > 0}
                    <div class="topic-option">
                        <div class="pull-right">
                            <!-- 管理 -->
                            {if $loginInfo['groupid'] == 99 || ($loginInfo['uid'] == $data['uid'] && $data['add_time'] >= time() - 3600)}
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn bg-gray btn-sm dropdown-toggle">
                                    更改分类 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {loop $clubs $club}
                                    <li>
                                        <a class="change-club btn btn-sm" data-cid="{$club.cid}"><i class="fa fa-tag"></i> {$club.club_name}</a>
                                    </li>
                                    {/loop}
                                </ul>
                            </div>
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn bg-gray btn-sm dropdown-toggle">
                                    管理操作 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {if $loginInfo['groupid'] == 99}
                                    <li>
                                        <a class="btn btn-sm top-topic"><i class="fa fa-level-up"></i> 置顶</a>
                                    </li>
                                    <li>
                                        <a class="btn btn-sm essence-topic"><i class="fa fa-thumb-tack"></i> 精华</a>
                                    </li>
                                    <li>
                                        <a class="btn btn-sm lock-topic"><i class="fa fa-lock"></i> 锁帖</a>
                                    </li>
                                    {/if}
                                    <li>
                                        <a href="/edit/topic/{$data.tid}" target="_blank" class="btn btn-sm"><i class="fa fa-edit"></i> 编辑</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a class="delete-topic btn btn-sm"><i class="fa fa-trash"></i> 删除</a>
                                    </li>
                                </ul>
                            </div>
                            {/if}
                            <!-- 互动 -->
                            {if $data['praise']['hasPraise']}
                                <a class="do-praise btn btn-sm bg-olive" disabled="disabled"><i class="fa fa-thumbs-up"></i> 已点赞 </a>
                            {else}
                                <a class="do-praise btn btn-sm bg-olive"><i class="fa fa-thumbs-up"></i> 点赞 </a>
                            {/if}
                            {if $data['hasCollection']}
                                <a class="do-collection btn btn-sm bg-purple"><i class="fa fa-star"></i> 已收藏</a>
                            {else}
                                <a class="do-collection btn btn-sm bg-purple"><i class="fa fa-star-o"></i> 收藏</a>
                            {/if}
                            <a class="do-reward btn btn-sm btn-danger" data-pid="0"><i class="fa fa-gift"></i> 打赏</a>
                            <a class="do-reply btn btn-sm btn-primary" data-pid="0"><i class="fa fa-pencil"></i> 回复</a>
                        </div>
                        <div class="clearfix"></div>
                        <div class="input-group pull-right reward-input">
                            <div style="display: inline-table;">
                                <span class="input-group-addon">积分</span>
                                <input type="text" class="reward-score form-control" placeholder="范围1~1000">
                                <span class="input-group-btn">
                                    <button type="button" class="confirm-reward btn btn-primary">打赏</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    {/if}
                </div>
                <!-- 回复 -->
                <div class="tab-content">
                    <div class="feed-activity-list">
                        <!-- 微社区主题评论相对较少，一次性加载完毕，为避免掘坟可以合理使用锁帖功能 -->
                        {if ($data['reply']['count'] > 0)}
                            <ul class="timeline timeline-inverse">
                                {loop $data['reply']['rows'] $reply}
                                  <li class="time-label">
                                      <span class="bg-gray">
                                          {$reply.post_time}
                                      </span>
                                  </li>
                                  <li id="reply-{$reply.pid}">
                                    <a href="/user/{$reply.uid}" class="pull-left reply-user">
                                        <img alt="image" class="reply-avatar u-avatar" src="{$reply.avatar}" onerror="javascript:this.src='https://dn-roc.qbox.me/avatar/0-avatar.png';">
                                    </a>
                                    <div class="timeline-item direct-chat-text">
                                      <h3 class="timeline-header">
                                          <a href="/user/{$reply.uid}">
                                              <strong>{$reply.username}</strong>
                                          </a>
                                          {if $reply['groupid'] != 0}
                                          <span class="text-warning">
                                              {if $reply['groupid'] != 99}
                                                  <i class="iconfont icon-vdengji" style="font-size: 12px;"></i><span class="sm-text">{$reply.groupid}</span>
                                              {else}
                                                  <i class="iconfont icon-guanliyuan" style="font-size: 12px;"></i><span class="sm-text">管理员</span>
                                              {/if}
                                          </span>
                                          {/if}
                                          {if (!empty($reply['client']))}
                                            <small class="margin-l-5 margin-r-5"><i class="fa fa-safari"></i> {$reply.client}</small>
                                          {/if}
                                          {if (!empty($reply['location']))}
                                            <small class="margin-r-5"><i class="fa fa-map-marker"></i> {$reply.location}</small>
                                          {/if}

                                          {if $loginInfo['uid'] > 0}
                                          <div class="pull-right">
                                              <a class="do-reply btn btn-xs" data-pid="{$reply.pid}" title="回复TA" data-toggle="tooltip" data-placement="top"><i class="fa fa-reply"></i></a>
                                              <!-- 超过一小时不可删除（管理员除外） -->
                                              {if $loginInfo['groupid'] == 99 || ($loginInfo['uid'] == $reply['uid'] && $reply['add_time'] >= time() - 3600)}
                                              <a class="delete-reply btn btn-xs" data-pid="{$reply.pid}" title="删除" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></a>
                                              {/if}
                                          </div>
                                          {/if}
                                      </h3>
                                      <div class="timeline-body">
                                          {if $reply['at_pid'] > 0}
                                          <div class="well load-at-reply" data-at_pid="{$reply.at_pid}" data-tid="{$reply.tid}" title="点击加载全部" data-toggle="tooltip" data-placement="top">
                                              <p>引用 <a href="/user/{$reply.at_reply.uid}"><strong>{$reply.at_reply.username}</strong></a> {$reply.at_reply.post_time}</p>
                                              <p class="ellipsis at-reply-{$reply.at_pid}">
                                                  {$reply.at_reply.content}
                                              </p>
                                          </div>
                                          {/if}
                                          <div class="reply-content">
                                            {$reply.content}
                                          </div>
                                          {if ($reply['images']['count'] > 0)}
                                              <div class="image-list">
                                                  {loop $reply['images']['rows'] $img}
                                                  <a class="fancybox" data-fancybox-group="group" href="{$img}">
                                                      <img src="{$img}" alt="">
                                                  </a>
                                                  {/loop}
                                              </div>
                                          {/if}
                                      </div>
                                    </div>
                                  </li>
                                {/loop}
                                <li>
                                    <i class="fa fa-pencil bg-olive"></i>
                                    <div class="timeline-item direct-chat-text reply-list">
                                        {if $loginInfo['uid'] > 0}
                                            <div id="reply-input" style="padding: 10px;{if $data['is_lock'] == 1}display: none;{/if}">
                                                <textarea id="editor" class="editor"></textarea>
                                                <a class="more-input">
                                                    <i class="fa fa-angle-double-down"></i>
                                                </a>
                                                <a id="post-btn" data-at_pid="0" data-tid="{$data.tid}" href="javascript:;" class="btn btn-sm bg-olive btn-block" style="border-radius: 0;">
                                                    <i class="fa fa-paper-plane"></i> 提交
                                                </a>
                                            </div>
                                            {if $data['is_lock'] == 1}
                                                <div style="padding: 30px; text-align: center; color: #999;">
                                                    主题被锁，不再支持回复
                                                </div>
                                            {/if}
                                        {else}
                                        <div class="no-data">
                                            <a href="/login">登录</a>后可回复哦~
                                        </div>
                                        {/if}
                                    </div>
                                </li>
                            </ul>
                        {else}
                            <div class="no-data">
                                暂无回复，快来抢沙发~
                            </div>
                            <ul class="timeline timeline-inverse">
                                <li>
                                    <i class="fa fa-pencil bg-olive"></i>
                                    <div class="timeline-item direct-chat-text reply-list">
                                        {if $loginInfo['uid'] > 0}
                                            <div id="reply-input" style="padding: 10px;{if $data['is_lock'] == 1}display: none;{/if}">
                                                <textarea id="editor" class="editor"></textarea>
                                                <a class="more-input">
                                                    <i class="fa fa-angle-double-down"></i>
                                                </a>
                                                <a id="post-btn" data-at_pid="0" data-tid="{$data.tid}" href="javascript:;" class="btn btn-sm bg-olive btn-block" style="border-radius: 0;">
                                                    <i class="fa fa-paper-plane"></i> 提交
                                                </a>
                                            </div>
                                            {if $data['is_lock'] == 1}
                                                <div style="padding: 30px; text-align: center; color: #999;">
                                                    主题被锁，不再支持回复
                                                </div>
                                            {/if}
                                        {else}
                                        <div class="no-data">
                                            <a href="/login">登录</a>后可回复哦~
                                        </div>
                                        {/if}
                                    </div>
                                </li>
                            </ul>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <!-- 附区 -->
        <div class="col-md-4">
            <!-- 楼主信息 -->
            <div class="box box-widget box-success widget-user">
                <div class="widget-user-header bg-gray">
                  <h3 class="widget-user-username"><strong>{$data.username}</strong></h3>
                  <h5 class="widget-user-desc text-warning">
                      {if $data['groupid'] != 99}
                          <i class="iconfont icon-vdengji"></i><span class="sm-text">{$data.groupid}</span>
                      {else}
                          <i class="iconfont icon-guanliyuan"></i><span class="sm-text">管理员</span>
                      {/if}
                  </h5>
                </div>
                <div class="widget-user-image">
                    <a href="/user/{$data.uid}">
                        <img class="img-circle" src="{$data.avatar}" alt="{$data.username}">
                    </a>
                </div>
                <div class="box-footer">
                  <div class="row">
                    <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"><a href="/user/{$data.uid}#topics">{$data.owner_statistic.topic}</a></h5>
                        <span class="description-text">主题</span>
                      </div>
                    </div>
                    <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"><a href="/user/{$data.uid}#replys">{$data.owner_statistic.reply}</a></h5>
                        <span class="description-text">回复</span>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="description-block">
                        <h5 class="description-header"><a href="/user/{$data.uid}#fans">{$data.owner_statistic.fans}</a></h5>
                        <span class="description-text">粉丝</span>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- 点赞墙 -->
            <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-thumbs-up margin-r-5"></i> TA们点赞了</h3>
                </div>
                <div class="box-body">
                    <div class="topic-praise">
                        {if (!empty($data['praise']['rows']))}
                            {loop $data['praise']['rows'] $praise}
                            <a href="/user/{$praise.uid}" class="praise-user">
                                <img alt="image" class="img-circle u-avatar" src="{$praise.avatar}">
                            </a>
                            {/loop}
                        {else}
                            <div class="no-data">还没有人点赞 ^_^</div>
                        {/if}
                        <div class="clear clear-fix"></div>
                    </div>
                </div>
            </div>
            <!-- 打赏 -->
            <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-gift margin-r-5"></i> TA们打赏了</h3>
                </div>
                <div class="box-body">
                    <div class="topic-reward">
                        {if (!empty($data['reward']['rows']))}
                                {loop $data['reward']['rows'] $reward}
                                    <p><a href="/user/{$reward.add_user}" class="margin-r-15">{$reward.username}</a><small class="margin-r-15">{$reward.add_time}</small>赏 <strong class="text-danger">{$reward.changed}</strong> 积分</p>
                                {/loop}
                        {else}
                            <div class="no-data">还没有人打赏 ^_^</div>
                        {/if}
                        <div class="clear clear-fix"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </section>
    <script type="text/javascript">
        var config = {
            tid: {$data.tid}
        };
    </script>
    {$footerLayout}
  </div>
</div>

</body>
</html>
