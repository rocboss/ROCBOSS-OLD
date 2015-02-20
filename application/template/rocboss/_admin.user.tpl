<section>
    <div class="body">
        <h2>用户管理</h2>
        <form method="post" id="form">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                <th class="text-center">头像</th>
                <th class="text-center">昵称</th>
                <th class="text-center">邮箱</th>
                <th class="text-center">积分</th>
                <th class="text-center">余额</th>
                <th class="text-center">最后登录</th>
                <th class="text-center">操作</th>
                </tr>
                </thead>
                <tbody>
                <!--{loop $userArray $user}-->
                <tr id="user-<!--{$user['uid']}-->">
                <td align="center">
                    <img src="<!--{Image::getAvatarURL($user['uid'])}-->" width="30" height="30" />
                </td>
                <td align="center">
                    <a href="<!--{ROOT}-->user/index/uid/<!--{$user['uid']}-->/" target="_blank">
                        <!--{$user['username']}-->
                    </a>
                </td>
                <td align="center">
                    <!--{if empty($user['email'])}-->
                        暂未设定
                    <!--{else}-->
                        <!--{$user['email']}-->
                    <!--{/if}-->
                </td>
                <td align="center">
                    <!--{$user['scores']}-->
                </td>
                <td align="center">
                    <!--{$user['money']}-->
                </td>
                <td align="center">
                    <!--{Utils::formatTime($user['lasttime'])}-->
                </td>
                <td align="center">
                    <!--{if $user['groupid'] == 1}-->
                    <a class="btn btn-warning" onclick="javascript:ban(<!--{$user['uid']}-->, this, 0);" title="禁止发言">禁言</a>
                    <!--{elseif $user['groupid'] == 0}-->
                    <a class="btn btn-warning" onclick="javascript:ban(<!--{$user['uid']}-->, this, 1);" title="解除禁言">解除禁言</a>
                    <!--{/if}-->
                    <!--{if $user['groupid'] == 9}-->
                    <a class="btn btn-warning" onclick="javascript:ban(<!--{$user['uid']}-->, this, 1);" title="降级为普通会员">设为普通会员</a>
                    <!--{else}-->
                    <a class="btn btn-warning" onclick="javascript:ban(<!--{$user['uid']}-->, this, 9);" title="升级为管理员">升级</a>
                    <!--{/if}-->
                </td>
                </tr>
                <!--{/loop}-->
                </tbody>
            </table>
        </form>
        <div class="pagination">
            <!--{if empty($userArray)}--> 
                暂无数据 
            <!--{else}--> 
                <!--{$page}--> 
            <!--{/if}-->
        </div>
    </div>
</section>