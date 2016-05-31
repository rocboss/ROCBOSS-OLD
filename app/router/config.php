<?php

// 路由映射表
return [
    ['GET /((@cid:[0-9]+/)@page:[0-9]+)', 'frontend\Index:index'],

    ['GET /search', 'frontend\Index:search'],

    ['GET /read/@tid:[0-9]+', 'frontend\Index:read'],

    ['GET /changeSort/@cid/@page/@sort', 'frontend\Index:changeSort'],

    ['GET /newTopic', 'frontend\Index:newTopic'],

    ['GET /edit/topic/@tid:[0-9]+', 'frontend\Index:editTopic'],

    ['GET /user(/@uid:[0-9]+)', 'frontend\User:index'],

    ['GET /notice', 'frontend\User:notice'],

    ['GET /profile', 'frontend\User:profile'],

    ['GET /scores', 'frontend\User:scores'],

    ['GET /recharge/@money:[0-9]+', 'frontend\User:recharge'],

    ['GET /recharge/return', 'frontend\User:alipayReturn'],

    ['GET /user/topic/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreTopic'],

    ['GET /user/reply/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreReply'],

    ['GET /user/collection/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreCollection'],

    ['GET /user/fans/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreFans'],

    ['GET /get/whisper/@type:0|1/@page:[0-9]+', 'frontend\User:getMoreWhisper'],

    ['GET /logout', 'frontend\User:logout'],

    ['GET|POST /login(/@type:qq|weibo)', 'frontend\User:login'],

    ['GET|POST /register(/@type:qq|weibo)', 'frontend\User:register'],

    ['POST /add/topic', 'frontend\Post:addTopic'],

    ['POST /add/reply/@tid:[0-9]+', 'frontend\Post:addReply'],

    ['POST /do/praise/@tid:[0-9]+', 'frontend\Post:doPraise'],

    ['POST /do/collection/@tid:[0-9]+', 'frontend\Post:doCollection'],

    ['POST /do/read/@type', 'frontend\Post:doRead'],

    ['POST /do/reward/@tid:[0-9]+', 'frontend\Post:doReward'],

    ['POST /do/follow', 'frontend\User:doFollow'],

    ['POST /upgrade/vip/@type:[0-9]+', 'frontend\User:doUpgrade'],

    ['POST /deliver/whisper', 'frontend\User:deliverWhisper'],

    ['POST /save/profile', 'frontend\User:saveProfile'],

    ['POST /recharge/notify', 'frontend\User:alipayNotify'],

    ['POST /change/club/@tid:[0-9]+', 'frontend\Post:changeClub'],

    ['POST /top/topic/@tid:[0-9]+', 'frontend\Post:topTopic'],

    ['POST /lock/topic/@tid:[0-9]+', 'frontend\Post:lockTopic'],

    ['POST /delete/topic/@tid:[0-9]+', 'frontend\Post:deleteTopic'],

    ['POST /edit/topic/@tid:[0-9]+', 'frontend\Post:editTopic'],

    ['POST /delete/reply/@pid:[0-9]+', 'frontend\Post:deleteReply'],

    ['POST /delete/whisper', 'frontend\Post:deleteWhisper'],

    ['/uploads', 'frontend\Post:upload'],

    // 管理首页
    ['GET /admin', 'backend\Admin:index'],

    // 管理分类
    ['GET /admin/clubs', 'backend\Admin:clubs'],

    // 系统设置
    ['GET /admin/system', 'backend\Admin:system'],

    // 管理帖子
    ['GET /admin/topics(/@page:[0-9]+)', 'backend\Admin:topics'],

    // 管理主题
    ['GET /admin/replys(/@page:[0-9]+)', 'backend\Admin:replys'],

    // 管理用户
    ['GET /admin/users(/@page:[0-9]+)', 'backend\Admin:users'],

    // 链接管理
    ['GET /admin/links', 'backend\Admin:links'],

    // 用户积分详情
    ['GET /admin/user-score-records/@uid:[0-9]+', 'backend\User:getUserScoreRecords'],
];
