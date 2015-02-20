# ROCBOSS 2.0 系统

# 关于安装

1. 确认您的环境是否支持：PHP >= 5.1 ，开启 pdo_mysql 扩展 ， 开启 PATHINFO ， MySQL数据库需要支持 InnoDB 引擎
2. 打开 http://您的域名/install.php
3. 填写相应信息执行安装
4. 安装完成后，为了安全请务必删除 install.php
5. 登陆后，后台管理地址 http://您的域名/admin/ ,请先配置基础信息

# 关于伪静态

1. Apache平台下， 直接使用根目录.htaccess文件即可
2. Nginx平台下，规则如下：

        location / {
            if (!-e $request_filename) {
                   rewrite ^/(.*)$ /index.php/$1 last;
            }
        } 
        location ~* .tpl {
            deny all;
        } 

# 关于版权

为了开发ROCBOSS 2.0，作者投入了很多时间和精力，程序以开源形式免费发布就是为了帮助一些草根站长快速低成本地构建自己的网络平台，因此也请大家在没有或得授权的情况下，不要私自删改底部的版权链接，尊重他人劳动成果。对于私自删改版权的用户不提供任何支持并保留法律追究相关责任的权利，若是用于商业用途想去除底部链接，请到官网联系管理员获取商业授权。

官网地址： https://www.rocboss.com
官网QQ群：286717809

如有意见或BUG，欢迎反馈