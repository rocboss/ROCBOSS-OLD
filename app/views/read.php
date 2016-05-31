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
                    <a href="/{$data.cid}/1">{$data.club_name}</a>
                </li>
                {if $loginInfo['groupid'] == 99 || ($loginInfo['uid'] == $data['uid'] && $data['add_time'] >= time() - 3600)}
                <div class="pull-right">
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">
                            更改分类 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            {loop $clubs $club}
                            <li>
                                <a class="change-club btn btn-xs btn-primary" data-cid="{$club.cid}"><i class="fa fa-tag"></i> {$club.club_name}</a>
                            </li>
                            {/loop}
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">
                            管理操作 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            {if $loginInfo['groupid'] == 99}
                            <li>
                                <a class="btn btn-xs btn-primary top-topic"><i class="fa fa-level-up"></i> 置顶</a>
                            </li>
                            <li>
                                <a class="btn btn-xs btn-primary lock-topic"><i class="fa fa-lock"></i> 锁帖</a>
                            </li>
                            {/if}
                            <li>
                                <a href="/edit/topic/{$data.tid}" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> 编辑</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="delete-topic btn btn-xs btn-primary"><i class="fa fa-trash"></i> 删除</a>
                            </li>
                        </ul>
                    </div>
                </div>
                {/if}
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="/user/{$data.uid}" class="pull-left">
                                    <img class="topic-detail-avatar" src="{$data.avatar}" alt="">
                                </a>
                                <div class="topic-about">
                                    <h3>{$data.title}{if $data['is_top'] == 1} <span style="color: #ed5565">【置顶】</span>{/if}</h3>
                                    <p>
                                        <a href="/user/{$data.uid}">{$data.username}</a>
                                        {if $data['groupid'] != 0}
                                        <span class="text-warning" style="margin-top: -20px;">
                                            {if $data['groupid'] != 99}
                                                <i class="iconfont icon-vdengji"></i><span class="sm-text">{$data.groupid}</span>
                                            {else}
                                                <i class="iconfont icon-guanliyuan"></i><span class="sm-text">管理员</span>
                                            {/if}
                                        </span>
                                        {/if}
                                        <span class="topic-info"><i class="fa fa-clock-o"></i> {$data.post_time}</span>
                                        {if (!empty($data['client']))}
                                            <span class="topic-info"><i class="fa fa-safari"></i> {$data.client}</span>
                                        {/if}
                                        {if (!empty($data['location']))}
                                            <span class="topic-info"><i class="fa fa-map-marker"></i> {$data.location}</span>
                                        {/if}
                                    </p>
                                </div>
                            </div>
                            <div class="topic-detail col-lg-12">
                                <div class="content">
                                    {$data.content}
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
                            <div class="topic-praise">
                                {if (!empty($data['praise']['rows']))}
                                <span class="p-tips">以下用户赞了本帖</span>
                                    {loop $data['praise']['rows'] $praise}
                                    <a href="/user/{$praise.uid}" class="praise-user">
                                        <img alt="image" class="img-circle" src="{$praise.avatar}">
                                    </a>
                                    {/loop}
                                {else}
                                    <span class="p-tips" style="display: none;">以下用户赞了本帖</span>
                                {/if}
                                <div class="clear clear-fix"></div>
                            </div>
                            {if (!empty($data['reward']['rows']))}
                                <div class="topic-reward">
                                    {loop $data['reward']['rows'] $reward}
                                        <p><a href="/user/{$reward.add_user}">{$reward.username}</a> &nbsp; <small>{$reward.add_time}</small> &nbsp; 打赏了 <strong>{$reward.changed}</strong> 积分</p>
                                    {/loop}
                                </div>
                            {else}
                                <div class="topic-reward" style="display: none;"></div>
                            {/if}
                            {if $loginInfo['uid'] > 0}
                            <div class="topic-option">
                                <div class="pull-right">
                                    {if $data['praise']['hasPraise']}
                                        <a class="do-praise btn btn-sm btn-white" disabled="disabled"><i class="fa fa-thumbs-up"></i> 已点赞 </a>
                                    {else}
                                        <a class="do-praise btn btn-sm btn-white"><i class="fa fa-thumbs-up"></i> 点赞 </a>
                                    {/if}
                                    {if $data['hasCollection']}
                                        <a class="do-collection btn btn-sm btn-white"><i class="fa fa-star"></i> 已收藏</a>
                                    {else}
                                        <a class="do-collection btn btn-sm btn-white"><i class="fa fa-star-o"></i> 收藏</a>
                                    {/if}
                                    <a class="do-reward btn btn-sm btn-success" data-pid="0"><i class="fa fa-database"></i> 打赏</a>
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

                        <div class="row">
                            <div class="reply-list">
                                <div class="feed-activity-list">
                                    {if ($data['reply']['count'] > 0)}
                                        {loop $data['reply']['rows'] $reply}
                                            <div id="reply-{$reply.pid}" class="feed-element">
                                                <a href="/user/{$reply.uid}" class="pull-left">
                                                    <img alt="image" class="reply-avatar" src="{$reply.avatar}">
                                                </a>
                                                <div class="media-body reply-info">
                                                    <small class="pull-right">{$reply.post_time}</small>
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
                                                    <br>
                                                    {if (!empty($reply['client']))}
                                                        <span class="topic-info"><i class="fa fa-safari"></i> {$reply.client}</span>
                                                    {else}
                                                        <span class="topic-info"><i class="fa fa-safari"></i> 未知设备</span>
                                                    {/if}
                                                    {if (!empty($reply['location']))}
                                                        <span class="topic-info"><i class="fa fa-map-marker"></i> {$reply.location}</span>
                                                    {/if}
                                                </div>
                                                <div class="topic-detail">
                                                    <div class="content">
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

                                                    {if ($reply['at_pid'] > 0)}
                                                    <a data-at_pid="{$reply.at_pid}" data-tid="{$reply.tid}" class="load-at-reply">
                                                        <div class="well">
                                                            <p>
                                                                引用 <strong>{$reply.at_reply.username}</strong> {$reply.at_reply.post_time}
                                                            </p>
                                                            <div class="at-reply-{$reply.at_pid}">
                                                                {$reply.at_reply.content}
                                                            </div>
                                                        </div>
                                                    </a>
                                                    {/if}
                                                    {if $loginInfo['uid'] > 0}
                                                    <div class="pull-right">
                                                        <a class="do-reply btn btn-xs btn-white" data-pid="{$reply.pid}"><i class="fa fa-reply"></i></a>
                                                        {if $loginInfo['groupid'] == 99 || ($loginInfo['uid'] == $reply['uid'] && $reply['add_time'] >= time() - 3600)}
                                                        <a class="delete-reply btn btn-xs btn-white" data-pid="{$reply.pid}"><i class="fa fa-trash"></i></a>
                                                        {/if}
                                                    </div>
                                                    {/if}
                                                </div>
                                            </div>
                                        {/loop}
                                    {else}
                                        <div class="reply-tips">
                                            暂无回复，快来抢沙发~
                                        </div>
                                    {/if}
                                </div>
                            </div>

                            {if $loginInfo['uid'] > 0}
                            <div id="reply-input"{if $data['is_lock'] == 1} style="display: none;"{/if}>
                                <textarea id="editor" class="editor"></textarea>
                                <a class="more-input">
                                    <i class="fa fa-angle-double-down"></i>
                                </a>
                                <a id="post-btn" data-at_pid="0" data-tid="{$data.tid}" href="javascript:;" class="btn btn-sm btn-primary submit-btn" style="margin: 15px"><i class="fa fa-check "></i> 提交 </a>
                            </div>
                            {if $data['is_lock'] == 1}
                                <div style="padding: 30px; text-align: center; color: #999;">
                                    主题被锁，不再支持回复
                                </div>
                            {/if}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {$footerLayout}
    <script type="text/javascript">
        seajs.use("read", function(read) {
            read.init({
                tid: {$data.tid}
            });
        });
    </script>
</body>
</html>
