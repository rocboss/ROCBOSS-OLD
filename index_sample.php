<?php

session_start();

require 'system/Roc.php';

# 是否为HTTPS
Roc::set('system.secure', false);

# 基础配置
Roc::set([
# ======> 系统配置（默认无需修改）
    'system.handle_errors' => true,
    'system.controllers.path' => 'app/controllers',
    'system.models.path' => 'app/models',
    'system.views.path' => 'app/views',
    'system.views.cache' => 'app/cache',
    'system.views.cacheTime' => 0,
    'system.libs.path' => 'app/libs',
    'system.router' => require 'app/router/config.php',

    # 私信消耗积分
    'system.score.whisper' => 10,

# ======> 数据库配置

    # 数据库主机地址
    'db.host' => 'localhost',
    # 数据库端口
    'db.port' => 3306,
    # 数据库用户名
    'db.user' => 'root',
    # 数据库密码
    'db.pass' => 'root',
    # 数据库名称
    'db.name' => 'rocboss',
    # 数据库编码，默认utf8
    'db.charset' => 'utf8',

# ======> Redis配置

    # Redis主机地址
    'redis.host' => '127.0.0.1',
    # Redis端口，默认6379
    'redis.port' => 6379,
    # Redis数据库号，范围1~16，默认无需修改，0默认预留给杂项使用
    'redis.db' => 1,
    # Redis密码，无则留空
    'redis.auth' => '',

# ======> QQ一键登录相关配置项(connect.qq.com)，网站回调域示例：http://www.youdomain.com/register/qq

    # AppID
    'qq.appid' => '',
    # AppKey
    'qq.appkey' => '',

# =======> 微博一键登录相关配置项

    # AKEY
    'weibo.akey' => '',
    # SKEY
    'weibo.skey' => '',
    # Callback，默认请勿修改
    'weibo.callback' => (Roc::request()->secure ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/register/weibo',

# =======> 七牛云存储相关配置(www.qiniu.com)

    # Ak，从七牛 个人面板->秘钥管理 获取
    'qiniu.ak' => '',
    # SK
    'qiniu.sk' => '',
    # 加速域名，不要加http，如 dn-roc.qbox.me
    'qiniu.domain' => '',
    # 空间，需要设定为公开类型，如 rocboss
    'qiniu.bucket' => '',

# =======> 极验行为验证码配置(www.geetest.com)

    # 验证码开关，启用为true，否则为false
    'geetest.switch' => false,
    # AppID
    'geetest.appid' => '',
    # AppKey
    'geetest.appkey' => '',

# =======> 支付宝即时到帐接口配置

    # pid
    'alipay.pid' => '',
    # key
    'alipay.key' => '',

# =======> 聚合数据短信API配置（https://www.juhe.cn/docs/api/id/54）

    # 开关
    'sms.switch' => false,
    # AppKey
    'sms.appkey' => '',
    # [验证码] 短信模板ID （短信模板内容请设置为：您申请的验证码是#code#。有效期为#time#分钟，请尽快验证（如非您本人操作，请忽略本短信））
    'sms.code_tplid' => '',
    # [私信] 短信模板ID （短信模板内容请设置为：【您的网站名】#username# 向您发送了一条私信“#content#”，请及时登录查看。）
    'sms.whisper_tplid' => '',

# =======> 个推推送相关配置，默认无需配置

    # 推送开关，使用则为true，不使用为false
    'push.switch' => false,
    # APP的名称
    'push.name' => '',
    # appkey
    'push.appkey' => '',
    # appid
    'push.appid' => '',
    # mastersecret
    'push.mastersecret' => ''
]);

Roc::path(Roc::get('system.controllers.path'));
Roc::path(Roc::get('system.models.path'));
Roc::path(Roc::get('system.libs.path'));

Roc::before('start', ['Controller', 'init']);
Roc::start();
