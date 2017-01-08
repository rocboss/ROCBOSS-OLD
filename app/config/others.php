<?php
/**
 * Other configuration
 */
return [
# ======> 微信开放平台网站二维码登录配置
    # 开关，使用则为true，不使用为false
    'wx.switch' => false,

    'wx.appId' => '',

    'wx.appSecret' => '',

# ======> QQ一键登录相关配置项(connect.qq.com)
    # 开关
    'qq.switch' => false,

    # AppID
    'qq.appid' => '',

    # AppKey
    'qq.appkey' => '',

# =======> 微博一键登录相关配置项
    # 开关
    'weibo.switch' => false,

    # AKEY
    'weibo.akey' => '',

    # SKEY
    'weibo.skey' => '',

    # Callback，默认请勿修改
    'weibo.callback' => isset($_SERVER['HTTP_HOST']) ? (Roc::request()->secure ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/register/weibo' : '',

# =======> 七牛云存储相关配置(www.qiniu.com)

    # Ak
    'qiniu.ak' => '',

    # SK
    'qiniu.sk' => '',

    # 加速域名，不要加http
    'qiniu.domain' => '',

    # 空间，需要设定为公开类型
    'qiniu.bucket' => '',

# =======> 极验行为验证码配置(www.geetest.com)
    # 开关
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

    # [私信] 短信模板ID （短信模板内容请设置为：【ROCBOSS】#username# 向您发送了一条私信“#content#”，请及时登录查看。）
    'sms.whisper_tplid' => '',

# =======> 个推推送相关配置

    # 推送开关
    'push.switch' => false,

    # APP的名称
    'push.name' => '',

    # appkey
    'push.appkey' => '',

    # appid
    'push.appid' => '',

    # mastersecret
    'push.mastersecret' => ''
];
