SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `roc_article`
-- ----------------------------
DROP TABLE IF EXISTS `roc_article`;
CREATE TABLE `roc_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `poster_id` int(11) NOT NULL DEFAULT '0' COMMENT '封面ID',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '文章标题',
  `content` text NOT NULL COMMENT '文章内容',
  `praise_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `collection_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `post_time` int(11) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `is_open` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否审核开放',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效，0删除，1正常，2待审核',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_attachment`
-- ----------------------------
DROP TABLE IF EXISTS `roc_attachment`;
CREATE TABLE `roc_attachment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户ID',
  `path` varchar(255) NOT NULL COMMENT '路径',
  `mime_type` char(16) NOT NULL DEFAULT '' COMMENT '附件mimeType',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '附件类型，1图片',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_club`
-- ----------------------------
DROP TABLE IF EXISTS `roc_club`;
CREATE TABLE `roc_club` (
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `club_name` varchar(16) NOT NULL DEFAULT '' COMMENT '分类名',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '分类排序',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`cid`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `roc_club`
-- ----------------------------
BEGIN;
INSERT INTO `roc_club` VALUES ('1', '技术交流', '0', '1'), ('2', '天下杂谈', '0', '1'), ('3', '心情分享', '0', '1'), ('4', '灌水专区', '0', '1');
COMMIT;

-- ----------------------------
--  Table structure for `roc_collection`
-- ----------------------------
DROP TABLE IF EXISTS `roc_collection`;
CREATE TABLE `roc_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户ID',
  `tid` int(11) unsigned NOT NULL COMMENT '话题ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`id`),
  KEY `uid` (`valid`,`uid`,`tid`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_config`
-- ----------------------------
DROP TABLE IF EXISTS `roc_config`;
CREATE TABLE `roc_config` (
  `key` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`key`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `roc_config`
-- ----------------------------
BEGIN;
INSERT INTO `roc_config` VALUES ('description', '最前沿的社区交流，最纯粹的技术切磋，一款优雅而简约的垂直微社区。'), ('keywords', 'BBS,社区,微社区'), ('rockey', 'z5fiz0ps4r9z'), ('sitename', 'ROCBOSS');
COMMIT;

-- ----------------------------
--  Table structure for `roc_follow`
-- ----------------------------
DROP TABLE IF EXISTS `roc_follow`;
CREATE TABLE `roc_follow` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '关注者UID',
  `fuid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '被关注者UID',
  PRIMARY KEY (`uid`,`fuid`),
  KEY `uid` (`uid`),
  KEY `fuid` (`fuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_link`
-- ----------------------------
DROP TABLE IF EXISTS `roc_link`;
CREATE TABLE `roc_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(12) NOT NULL DEFAULT '' COMMENT '链接名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接URL',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效，1有效，0无效',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `roc_link`
-- ----------------------------
BEGIN;
INSERT INTO `roc_link` VALUES ('1', 'ROCBOSS', 'https://www.rocboss.com', '50', '1');
COMMIT;

-- ----------------------------
--  Table structure for `roc_message`
-- ----------------------------
DROP TABLE IF EXISTS `roc_message`;
CREATE TABLE `roc_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '私信ID',
  `at_uid` mediumint(8) unsigned NOT NULL COMMENT '目标用户ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '发送用户ID',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '私信内容',
  `post_time` int(11) unsigned NOT NULL COMMENT '私信时间',
  `del_uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '率先删除者ID',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`id`),
  KEY `message` (`at_uid`,`uid`,`del_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_notification`
-- ----------------------------
DROP TABLE IF EXISTS `roc_notification`;
CREATE TABLE `roc_notification` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知ID',
  `at_uid` mediumint(8) unsigned NOT NULL COMMENT '通知对象ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户ID',
  `tid` int(11) unsigned NOT NULL COMMENT '主题ID',
  `pid` int(11) unsigned NOT NULL COMMENT '通知的资源ID',
  `post_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读，0未读，1已读',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`id`),
  KEY `at_uid` (`at_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_praise`
-- ----------------------------
DROP TABLE IF EXISTS `roc_praise`;
CREATE TABLE `roc_praise` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '主题ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效，0删除，1有效',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `tid` (`tid`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_relation`
-- ----------------------------
DROP TABLE IF EXISTS `roc_relation`;
CREATE TABLE `roc_relation` (
  `attachment_id` int(11) unsigned NOT NULL,
  `res_id` int(11) unsigned NOT NULL COMMENT '目标源ID',
  `type` tinyint(2) unsigned NOT NULL COMMENT '目标源类型，1主题，2回复',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`res_id`,`type`,`attachment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_reply`
-- ----------------------------
DROP TABLE IF EXISTS `roc_reply`;
CREATE TABLE `roc_reply` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '回复ID',
  `tid` int(11) unsigned NOT NULL COMMENT '主题ID',
  `at_pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '引用回复ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '回复内容',
  `client` char(20) NOT NULL DEFAULT '' COMMENT '客户端标识',
  `location` char(20) NOT NULL DEFAULT '' COMMENT '发表地区',
  `post_time` int(11) unsigned NOT NULL COMMENT '发布时间',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`pid`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_score`
-- ----------------------------
DROP TABLE IF EXISTS `roc_score`;
CREATE TABLE `roc_score` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trade_no` varchar(32) NOT NULL DEFAULT '' COMMENT '支付宝订单号',
  `tid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属主题ID',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '变化者用户ID',
  `changed` mediumint(8) NOT NULL DEFAULT '0' COMMENT '变动积分',
  `remain` int(11) NOT NULL DEFAULT '0' COMMENT '剩余积分',
  `reason` varchar(200) NOT NULL DEFAULT '' COMMENT '变动缘由',
  `add_user` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '变动时间',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`id`),
  KEY `INDEX_USER` (`uid`,`valid`),
  KEY `INDEX_TOPIC` (`tid`,`changed`,`valid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_topic`
-- ----------------------------
DROP TABLE IF EXISTS `roc_topic`;
CREATE TABLE `roc_topic` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '话题ID',
  `cid` mediumint(8) unsigned NOT NULL COMMENT '分类ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '话题标题',
  `content` text NOT NULL COMMENT '内容',
  `praise_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `collection_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `comment_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `location` char(20) NOT NULL DEFAULT '' COMMENT '发表地区',
  `client` char(20) NOT NULL DEFAULT '' COMMENT '客户端',
  `post_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `edit_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后编辑时间',
  `last_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后回复时间',
  `is_essence` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0普通，1精华',
  `is_lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0正常，1锁帖',
  `is_top` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0普通，1置顶',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`tid`),
  KEY `cid` (`valid`,`cid`),
  KEY `uid` (`valid`,`uid`),
  KEY `last_time` (`valid`,`last_time`),
  KEY `post_time` (`valid`,`post_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_user`
-- ----------------------------
DROP TABLE IF EXISTS `roc_user`;
CREATE TABLE `roc_user` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `email` char(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `phone` char(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `username` char(32) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT 'MD5后的密码',
  `salt` char(8) NOT NULL DEFAULT '' COMMENT '盐值',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `reg_time` int(11) unsigned NOT NULL COMMENT '注册时间',
  `last_time` int(11) NOT NULL COMMENT '最后活跃时间',
  `qq_openid` char(32) NOT NULL DEFAULT '' COMMENT 'QQ授权openID',
  `weibo_openid` char(32) NOT NULL DEFAULT '' COMMENT '微博授权openID',
  `wx_openid` char(32) NOT NULL DEFAULT '' COMMENT '微信OPENID',
  `wx_unionid` char(128) NOT NULL DEFAULT '' COMMENT '微信UnionID',
  `client_id` char(32) NOT NULL DEFAULT '' COMMENT 'ClientID，用于APP推送',
  `client_os` char(16) NOT NULL DEFAULT '' COMMENT 'APP操作系统OS',
  `token` char(32) NOT NULL DEFAULT '' COMMENT '客户端Token',
  `expire_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '超时时间',
  `groupid` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '用户组ID',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`valid`,`username`),
  KEY `qq` (`valid`,`qq_openid`),
  KEY `weibo` (`valid`,`weibo_openid`),
  KEY `email` (`valid`,`email`),
  KEY `phone` (`valid`,`phone`),
  KEY `weixin` (`valid`,`wx_unionid`,`wx_openid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `roc_user`
-- ----------------------------
BEGIN;
INSERT INTO `roc_user` VALUES ('1', 'admin@admin.com', '18399998888', 'admin', 'f5bb0c8de146c67b44babbf4e6584cc0', '9is38gt5', '5000', '1483879807', '1483879807', '', '', '', '', '', '', '', '0', '99', '1');
COMMIT;

-- ----------------------------
--  Table structure for `roc_whisper`
-- ----------------------------
DROP TABLE IF EXISTS `roc_whisper`;
CREATE TABLE `roc_whisper` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `at_uid` mediumint(8) unsigned NOT NULL COMMENT '目标用户ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '发送者ID',
  `content` varchar(255) NOT NULL,
  `post_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '私信时间',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读，0未读，1已读',
  `del_flag` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '首删者ID',
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除，0删除，1正常',
  PRIMARY KEY (`id`),
  KEY `uid` (`at_uid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `roc_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `roc_withdraw`;
CREATE TABLE `roc_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '提现申请单ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `pay_account` varchar(32) NOT NULL DEFAULT '' COMMENT '支付宝账户',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '提现的积分',
  `should_pay` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应支付金额',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0申请中，1审核通过，2审核拒绝',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `handle_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '处理者ID',
  `handle_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '处理时间',
  `valid` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效，1有效，0删除',
  PRIMARY KEY (`id`),
  KEY `INDEX_USER` (`uid`,`valid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='提现申请表';

SET FOREIGN_KEY_CHECKS = 1;
