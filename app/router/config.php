<?php
// 路由映射表
return [
    // 切换明暗主题
    ['POST /turn/light', 'frontend\Index:turnLight'],

    ['GET /(topic)', 'frontend\Index:index'],

    ['GET /page-((@cid:[0-9]+-)@page:[0-9]+).html', 'frontend\Index:index'],

    ['GET /category-(@cid:[0-9]+)-(@page:[0-9]+).html', 'frontend\Index:index'],

    ['GET /(@cid:[0-9]+/)@page:[0-9]+', 'frontend\Index:indexRedirect'],

    ['GET /search', 'frontend\Index:search'],

    ['GET /read/@tid:[0-9]+', 'frontend\Index:read'],

    ['GET /changeSort/@cid/@page/@sort', 'frontend\Index:changeSort'],

    ['GET /newTopic', 'frontend\Index:newTopic'],

    ['GET /edit/topic/@tid:[0-9]+', 'frontend\Index:editTopic'],

    ['GET /newArticle', 'frontend\Article:newArticle'],

    ['GET /article(/@page:[0-9]+)', 'frontend\Article:index'],

    ['GET /read/article-@id:[0-9]+', 'frontend\Article:read'],

    ['GET /user(/@uid:[0-9]+)', 'frontend\User:index'],

    ['GET /notice', 'frontend\User:notice'],

    ['GET /notice/change-type/@type', 'frontend\User:changeNoticeType'],

    ['GET /setting', 'frontend\User:setting'],

    ['GET /scores', 'frontend\User:scores'],

    ['GET /recharge/@money:[0-9]+', 'frontend\User:recharge'],

    ['GET /recharge/return', 'frontend\User:alipayReturn'],

    ['GET /user/@uid:[0-9]+/change-tab/@type', 'frontend\User:changeTabType'],

    ['GET /user/topic/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreTopic'],

    ['GET /user/reply/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreReply'],

    ['GET /user/article/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreArticle'],

    ['GET /user/collection/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreCollection'],

    ['GET /user/fans/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreFans'],

    ['GET /user/follows/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreFollows'],

    ['GET /get/notice/@page:[0-9]+', 'frontend\User:getMoreNotice'],

    ['GET /get/whisper/@page:[0-9]+', 'frontend\User:getMoreWhisper'],

    ['GET /user/whisper/@uid:[0-9]+/@page:[0-9]+', 'frontend\User:getMoreWhisperDialog'],

    ['GET /chat-with-@uid:[0-9]+', 'frontend\User:chatWithUser'],

    ['GET /logout', 'frontend\User:logout'],

    ['GET|POST /login(/@type:qq|weibo)', 'frontend\User:login'],

    ['GET|POST /register(/@type:qq|weibo|weixin)', 'frontend\User:register'],

    ['POST /add/topic', 'frontend\Post:addTopic'],

    ['POST /add/article', 'frontend\Post:addArticle'],

    ['POST /add/reply/@tid:[0-9]+', 'frontend\Post:addReply'],

    ['POST /do/praise/@tid:[0-9]+', 'frontend\Post:doPraise'],

    ['POST /do/article-praise/@aid:[0-9]+', 'frontend\Post:doArticlePraise'],

    ['POST /do/collection/@tid:[0-9]+', 'frontend\Post:doCollection'],

    ['POST /do/article-collection/@aid:[0-9]+', 'frontend\Post:doArticleCollection'],

    ['POST /do/read/@type', 'frontend\Post:doRead'],

    ['POST /do/reward/@tid:[0-9]+', 'frontend\Post:doReward'],

    ['POST /do/follow', 'frontend\User:doFollow'],

    ['POST /upgrade/vip/@type:[0-9]+', 'frontend\User:doUpgrade'],

    ['POST /deliver/whisper', 'frontend\User:deliverWhisper'],

    ['POST /do/withdraw', 'frontend\Post:withdraw'],

    ['POST /do/transfer', 'frontend\Post:transfer'],

    ['POST /save/profile', 'frontend\User:saveProfile'],

    ['POST /recharge/notify', 'frontend\User:alipayNotify'],

    ['POST /change/club/@tid:[0-9]+', 'frontend\Post:changeClub'],

    ['POST /top/topic/@tid:[0-9]+', 'frontend\Post:topTopic'],

    ['POST /essence/topic/@tid:[0-9]+', 'frontend\Post:essenceTopic'],

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

    // 管理文章
    ['GET /admin/articles(/@page:[0-9]+)', 'backend\Article:articles'],

    // 管理帖子
    ['GET /admin/topics(/@page:[0-9]+)', 'backend\Admin:topics'],

    // 管理主题
    ['GET /admin/replys(/@page:[0-9]+)', 'backend\Admin:replys'],

    // 提现申请
    ['GET /admin/withdraw(/@page:[0-9]+)', 'backend\Admin:withdraw'],

    // 管理用户
    ['GET /admin/users(/@page:[0-9]+)', 'backend\Admin:users'],

    // 链接管理
    ['GET /admin/links', 'backend\Admin:links'],

    // 用户积分详情
    ['GET /admin/user-score-records/@uid:[0-9]+', 'backend\User:getUserScoreRecords'],

    // 预览文章
    ['GET /admin/preview/article(/@id:[0-9]+)', 'backend\Article:preview'],

    // 审核文章
    ['POST /admin/review/article', 'backend\Article:doReview'],

    // 提现审核
    ['POST /admin/review/withdraw', 'backend\Admin:doWithdraw'],

    // 删除文章
    ['POST /delete/article/@id:[0-9]+', 'backend\Article:delete'],
];
