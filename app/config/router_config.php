<?php

# 路由配置项
$router_config = array(
    # 首页
    '/(@page:[0-9]+)' => array('home', 'index'),

    # 帖子详情页
    '/read/@tid:[0-9]+' => array('home', 'read'),

    # 标签
    '/tag/@name(/@page:[0-9]+)' => array('home', 'tag'),

    # 搜索
    '/search/@s(/@page:[0-9]+)' => array('home', 'search'),

    # 用户设置
    '/setting(/@type)' => array('setting', 'index'),

    # 设定阅读排序(最新发表)
    '/do/posttime' => array('doController', 'posttime'),

    # 设定阅读排序(最后回复)
    '/do/lasttime' => array('doController', 'lasttime'),

    # 登录
    '/login' => array('user', 'login'),

    # 退出
    '/logout' => array('user', 'logout'),

    # 注册
    '/register' => array('user', 'register'),

    # QQ登录
    '/qqlogin' => array('user', 'qqlogin'),

    # 提醒
    '/notification(/@status:0|1(/@page:[0-9]+))' => array('user', 'notification'),

    # 私信
    '/whisper(/@status:0|1|2(/@page:[0-9]+))' => array('user', 'whisper'),

    # 用户中心
    '/@@username' => array('user', 'transUser'),

    # 用户中心
    '/user(/@uid:[0-9]+)' => array('user', 'index'),

    # 标记提醒为已读
    '/do/readNotification/@nid:[0-9]+' => array('doController', 'readNotification'),

    # 用户帖子
    '/user(-@uid:[0-9]+)-topic(-@page:[0-9]+)' => array('user', 'topic'),

    # 用户回复
    '/user(-@uid:[0-9]+)-reply(-@page:[0-9]+)' => array('user', 'reply'),

    # 用户关注
    '/user(-@uid:[0-9]+)-follow(-@page:[0-9]+)' => array('user', 'follow'),

    # 用户粉丝
    '/user(-@uid:[0-9]+)-fans(-@page:[0-9]+)' => array('user', 'fans'),

    # 我的收藏
    '/my/favorite(/@page:[0-9]+)' => array('user', 'favorite'),

    # 我的积分
    '/my/score(/@page:[0-9]+)' => array('user', 'score'),

    # QQ登录返回
    '/user/QQCallback/*' => array('user', 'QQCallback'),

    # 重置密码 step 1
    '/resetPassword' => array('user', 'resetPassword'),

    # 重置密码 step 2
    '/doReset' => array('user', 'doReset'),

    # 获取验证码
    '/identifyImage(/*)' => array('user', 'identifyImage'),

    # 后台管理
    '/admin(/@type:system|common|topic|reply|tag|user|link(/@page:[0-9]+))' => array('admin', 'index'),

    # 清理缓存
    '/admin/ClearCache/@type:template|attachment|score' => array('manage', 'ClearCache'),

    # 删除标签
    '/manage/del_tag/@tagid:[0-9]+' => array('manage', 'del_tag'),

    # 删除链接
    '/manage/del_link/@position:[0-9]+' => array('manage', 'del_link'),

    # AJAX 发表帖子
    'POST /do/postTopic' => array('doController', 'postTopic'),

    # AJAX 删除帖子
    'POST /do/deleteTopic' => array('doController', 'deleteTopic'),

    # AJAX 发布回复
    'POST /do/postReply' => array('doController', 'postReply'),

    # AJAX 删除回复
    'POST /do/deleteReply' => array('doController', 'deleteReply'),

    # AJAX 楼中楼回复
    'POST /do/postFloor' => array('doController', 'postFloor'),

    # AJAX 删除楼中楼回复
    'POST /do/deleteFloor' => array('doController', 'deleteFloor'),

    # AJAX 删除提醒
    'POST /do/deleteNotification' => array('doController', 'deleteNotification'),

    # AJAX 删除私信
    'POST /do/deleteWhisper' => array('doController', 'deleteWhisper'),

    # AJAX 上传图片
    'POST /do/uploadPicture' => array('doController', 'uploadPicture'),

    # AJAX 上传头像
    'POST /do/uploadAvatar' => array('doController', 'uploadAvatar'),

    # AJAX 删除图片
    'POST /do/delPic' => array('doController', 'delPic'),

    # AJAX 标记私信已读
    'POST /do/readWhisper' => array('doController', 'readWhisper'),

    # AJAX 传送私信
    'POST /do/deliverWhisper' => array('doController', 'deliverWhisper'),

    # AJAX 收藏帖子
    'POST /do/favorTopic' => array('doController', 'favorTopic'),

    # AJAX 赞帖子
    'POST /do/praiseTopic' => array('doController', 'praiseTopic'),

    # AJAX 关注用户
    'POST /do/follow' => array('doController', 'follow'),

    # AJAX 获取帖子的详细信息
    'POST /manage/getTopicInfo' => array('manage', 'getTopicInfo'),

    # AJAX 编辑帖子
    'POST /manage/editTopic' => array('manage', 'editTopic'),

    # AJAX 获取回复详情
    'POST /manage/getReplyInfo' => array('manage', 'getReplyInfo'),

    # AJAX 编辑回复
    'POST /manage/editReply' => array('manage', 'editReply'),

    # AJAX 锁帖
    'POST /manage/lockTopic' => array('manage', 'lockTopic'),

    # AJAX 帖子置顶
    'POST /manage/topTopic' => array('manage', 'topTopic'),

    # AJAX 禁言
    'POST /manage/ban' => array('manage', 'ban'),

    # 编辑链接
    'POST /manage/edit_link' => array('manage', 'edit_link'),

    # AJAX 获取更多楼中楼回复
    'POST /getReplyFloorList' => array('home', 'getReplyFloorList'),

    # AJAX 获取更多回复列表
    'POST /getReplyList' => array('home', 'getReplyList'),

    # AJAX 用QQ注册用户
    'POST /qqjoin' => array('user', 'qqjoin'),

    # AJAX 签到
    'POST /do/doSign' => array('doController', 'doSign'),

    # AJAX 设置个性签名
    'POST /do/setSignature' => array('doController', 'setSignature'),

    # AJAX 设置邮箱
    'POST /do/setEmail' => array('doController', 'setEmail'),

    # AJAX 设置密码
    'POST /do/setPassword' => array('doController', 'setPassword'),

    # 未匹配转404，默认不可删除规则，且必须置于最后
    '*' => 'notFound'
);

?>