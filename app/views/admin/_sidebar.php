<div class="sidebar">
    <div id="logo-container">
        <p>
            <a href="/admin" style="color: #fff; text-decoration: none;">
                <img id="logo" src="/vendor/admin/img/logo.png">
                <span>ROCBOSS</span>
            </a>
        </p>
    </div>
    <div id="person-info">
        <p class="user-name">
            <i class="fa fa-user-secret"></i> {$loginInfo.username}
        </p>
        <p>
            <a href="/">回到首页</a>
            <a href="/logout">退出登录</a>
        </p>
    </div>
    <div class="meun-item<?php echo $active == 'index' ? ' meun-item-active' : ''; ?>">
        <a href="/admin"><i class="fa fa-inbox fa-fw"></i> 系统信息</a>
    </div>
    <div class="meun-item<?php echo $active == 'system' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/system"><i class="fa fa-cog fa-fw"></i> 系统设置</a>
    </div>
    <div class="meun-item<?php echo $active == 'clubs' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/clubs"><i class="fa fa-cloud fa-fw"></i> 分类管理</a>
    </div>
    <div class="meun-item<?php echo $active == 'articles' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/articles"><i class="fa fa-pagelines fa-fw"></i> 文章管理</a>
    </div>
    <div class="meun-item<?php echo $active == 'topics' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/topics"><i class="fa fa-clipboard fa-fw"></i> 主题管理</a>
    </div>
    <div class="meun-item<?php echo $active == 'replys' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/replys"><i class="fa fa-mail-reply-all fa-fw"></i> 回复管理</a>
    </div>
    <div class="meun-item<?php echo $active == 'withdraws' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/withdraw"><i class="fa fa-credit-card fa-fw"></i> 提现申请</a>
    </div>
    <div class="meun-item<?php echo $active == 'users' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/users"><i class="fa fa-users fa-fw"></i> 用户管理</a>
    </div>
    <div class="meun-item<?php echo $active == 'links' ? ' meun-item-active' : ''; ?>">
        <a href="/admin/links"><i class="fa fa-link fa-fw"></i> 链接管理</a>
    </div>
</div>
