<section>
    <div class="body">
        <h2>服务器信息</h2>
        <div class="info">
            <ul>
                <li>服务器引擎：<!--{$server['software']}--></li>
                <li>当前端口：<!--{$server['port']}--></li>
                <li>网站域名：<!--{$server['name']}--></li>
                <li>服务器版本：<!--{$server['os']}--></li>
                <li>数据库版本：MYSQL <!--{mysql_get_server_info()}--></li>
                <li>PHP版本：<!--{$server['version']}--></li>
                <li>网站根目录：<!--{$server['root']}--></li>
                <li>最大上传值：<!--{$server['upload']}--></li>
                <li>会话超时：<!--{$server['timeout']}--> 分</li>
                <li>占用内存：<!--{$server['memory_usage']}--></li>
                <li><b>系统信息 <i class="icon icon-xiangxia"></i></b></li>
                <li>会员总数：<!--{$server['user_count']}--></li>
                <li>今日签到：<!--{$server['sign_count']}--></li>
            </ul>
        </div>
    </div>
    <footer>&copy; ROCBOSS 后台管理中心</footer>
</section>