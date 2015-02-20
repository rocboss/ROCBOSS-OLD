<section>
<div class="body">
    <form action="<!--{ROOT}-->manage/adminCommon/" method="post">
        <h4>系统设置</h4>
        <fieldset>
            <legend>基本信息设置</legend>
            <table width="100%" class="form-table">
            <tr>
                <td class="input-name">
                    站点标题：
                </td>
                <td>
                    <input type="text" size="60" name="sitename" value="<!--{$sys_config['sitename']}-->" class="input"/>
                </td>
            </tr>
            <tr>
                <td class="input-name">
                    站点关键字(SEO)：
                </td>
                <td>
                    <input type="text" size="60" name="keywords" value="<!--{$sys_config['keywords']}-->" class="input"/> 英文逗号隔开
                </td>
            </tr>
            <tr>
                <td class="input-name valign-top">
                    网站描述：
                </td>
                <td>
                    <textarea class="input" name="description" rows="4" cols="60"><!--{$sys_config['description']}--></textarea>
                </td>
            </tr>
            <tr>
                <td class="input-name valign-top">
                    注册开关：
                </td>
                <td>
                    <input type="checkbox" name="join_switch" size="15" value="1" <!--{if $sys_config['join_switch'] == true}-->checked="checked"<!--{/if}-->> 允许注册
                </td>
            </tr>
            <tr>
                <td class="input-name">
                    积分策略：
                </td>
                <td>
                    <p>
                    注册积分：
                    <input type="text" size="6" name="register" value="<!--{$sys_config['scores']['register']}-->" class="input"/>
                    （正整数）
                    </p>
                    <p>
                    创建主题：
                    <input type="text" size="6" name="topic" value="<!--{$sys_config['scores']['topic']}-->" class="input"/>
                    （正整数）
                    </p>
                    <p>
                    创建回复：
                    <input type="text" size="6" name="reply" value="<!--{$sys_config['scores']['reply']}-->" class="input"/>
                   （正整数）
                    </p>
                    <p>
                    主题被赞：
                    <input type="text" size="6" name="praise" value="<!--{$sys_config['scores']['praise']}-->" class="input"/>
                    （正整数）
                    </p>
                    <p>
                    私信费用：
                    <input type="text" size="6" name="whisper" value="<!--{$sys_config['scores']['whisper']}-->" class="input"/>
                    （正整数）
                    </p>
                </td>
            </tr>
            <tr>
                <td class="input-name valign-top">
                    网站秘钥：
                </td>
                <td>
                    <input type="text" class="input" name="ROCKEY" size="60" value="<!--{$sys_config['ROCKEY']}-->"/> 请定期修改，不少于14位
                </td>
            </tr>
            <tr>
                <td class="input-name valign-top">
                    QQ登录APPID：
                </td>
                <td>
                    <input type="text" class="input" name="appid" size="60" value="<!--{$qq_config['appid']}-->"/>
                    请去 <a href="http://connect.qq.com" target="_blank">http://connect.qq.com</a> 申请
                </td>
            </tr>
            <tr>
                <td class="input-name valign-top">
                    QQ登录APPKEY：
                </td>
                <td>
                    <input type="text" class="input" name="appkey" size="60" value="<!--{$qq_config['appkey']}-->"/>
                </td>
            </tr>
            <tr>
                <td class="input-name valign-top">
                    网站广告代码：
                </td>
                <td>
                    <textarea class="input" name="ad" rows="4" cols="60"><!--{$sys_config['ad']}--></textarea>
                </td>
            </tr>
            </table>
        </fieldset>
        <div class="form-submit">
            <input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
        </div>
    </form>
</div>
<footer>&copy; ROCBOSS 后台管理中心</footer>
</section>