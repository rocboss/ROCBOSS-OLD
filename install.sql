SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `roc_attachment`
-- ----------------------------
DROP TABLE IF EXISTS `roc_attachment`;
CREATE TABLE `roc_attachment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `path` varchar(128) NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`uid`,`tid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `roc_favorite`
-- ----------------------------
DROP TABLE IF EXISTS `roc_favorite`;
CREATE TABLE `roc_favorite` (
  `fid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`fid`),
  KEY `fuid` (`fid`),
  KEY `id` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_floor`
-- ----------------------------
DROP TABLE IF EXISTS `roc_floor`;
CREATE TABLE `roc_floor` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `content` varchar(120) NOT NULL,
  `posttime` int(11) NOT NULL,
  PRIMARY KEY (`id`,`pid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `roc_follow`
-- ----------------------------
DROP TABLE IF EXISTS `roc_follow`;
CREATE TABLE `roc_follow` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `fuid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`fuid`),
  KEY `uid` (`uid`),
  KEY `fuid` (`fuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_notification`
-- ----------------------------
DROP TABLE IF EXISTS `roc_notification`;
CREATE TABLE `roc_notification` (
  `nid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `atuid` mediumint(8) NOT NULL,
  `uid` mediumint(8) NOT NULL,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `fid` int(11) unsigned NOT NULL,
  `isread` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0',
  PRIMARY KEY (`nid`),
  KEY `atuid` (`atuid`,`isread`,`nid`),
  KEY `tid` (`tid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_praise`
-- ----------------------------
DROP TABLE IF EXISTS `roc_praise`;
CREATE TABLE `roc_praise` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`uid`,`tid`),
  KEY `uid` (`uid`),
  KEY `fuid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_reply`
-- ----------------------------
DROP TABLE IF EXISTS `roc_reply`;
CREATE TABLE `roc_reply` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) NOT NULL,
  `content` varchar(250) NOT NULL,
  `client` varchar(16) NOT NULL,
  `posttime` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`tid`,`uid`),
  KEY `tid` (`tid`,`pid`),
  KEY `uid` (`uid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `roc_score`
-- ----------------------------
DROP TABLE IF EXISTS `roc_score`;
CREATE TABLE `roc_score` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `changed` smallint(6) NOT NULL,
  `remain` mediumint(8) NOT NULL,
  `type` tinyint(2) NOT NULL,
  `time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_system`
-- ----------------------------
DROP TABLE IF EXISTS `roc_system`;
CREATE TABLE `roc_system` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Records of `roc_system`
-- ----------------------------
BEGIN;
INSERT INTO `roc_system` VALUES ('1', 'sitename', '站点名'), ('2', 'keywords', '关键词1 关键词2 关键词3'), ('3', 'description', '您的网站描述'), ('4', 'rockey', '39d3#32k%d&2890'), ('5', 'ad', '这里是广告位'), ('6', 'join_switch', '1'), ('7', 'scores_register', '25'), ('8', 'scores_topic', '2'), ('9', 'scores_reply', '1'), ('10', 'scores_praise', '1'), ('11', 'scores_whisper', '5'), ('12', 'scores_sign', '10'), ('13', 'appid', ''), ('14', 'appkey', ''), ('15', 'notice', '这是公告'), ('16', 'theme', 'rocboss'), ('17', 'smtp_server', ''), ('18', 'smtp_port', ''), ('19', 'smtp_user', ''), ('20', 'smtp_password', '');
COMMIT;

-- ----------------------------
--  Table structure for `roc_tag`
-- ----------------------------
DROP TABLE IF EXISTS `roc_tag`;
CREATE TABLE `roc_tag` (
  `tagid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tagname` varchar(16) NOT NULL,
  `used` int(11) unsigned NOT NULL,
  PRIMARY KEY (`tagid`,`tagname`,`used`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `roc_topic`
-- ----------------------------
DROP TABLE IF EXISTS `roc_topic`;
CREATE TABLE `roc_topic` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL,
  `title` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `comments` mediumint(8) NOT NULL DEFAULT '0',
  `client` varchar(16) DEFAULT NULL,
  `istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `islock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `posttime` int(11) NOT NULL,
  `lasttime` int(11) NOT NULL,
  PRIMARY KEY (`tid`,`uid`,`title`),
  KEY `uid` (`uid`,`tid`),
  KEY `cid` (`lasttime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `roc_topic_tag_connection`
-- ----------------------------
DROP TABLE IF EXISTS `roc_topic_tag_connection`;
CREATE TABLE `roc_topic_tag_connection` (
  `tid` int(11) unsigned NOT NULL,
  `tagid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`tid`,`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_user`
-- ----------------------------
DROP TABLE IF EXISTS `roc_user`;
CREATE TABLE `roc_user` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(26) NOT NULL,
  `email` char(36) NOT NULL,
  `signature` varchar(32) NOT NULL,
  `password` char(32) NOT NULL,
  `regtime` int(11) NOT NULL,
  `lasttime` int(11) NOT NULL,
  `qqid` char(32) NOT NULL,
  `scores` mediumint(8) unsigned NOT NULL,
  `money` mediumint(8) unsigned NOT NULL,
  `groupid` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `nickname` (`username`),
  KEY `email` (`email`),
  KEY `qqid` (`qqid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Records of `roc_user`
-- ----------------------------
BEGIN;
INSERT INTO `roc_user` VALUES ('1', 'admin', 'admin@admin', '我是管理员', 'e10adc3949ba59abbe56e057f20f883e', '1432384146', '1432384146', '', '5000', '0', '9');
COMMIT;

-- ----------------------------
--  Table structure for `roc_user_reset`
-- ----------------------------
DROP TABLE IF EXISTS `roc_user_reset`;
CREATE TABLE `roc_user_reset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `code` char(16) NOT NULL,
  `time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
--  Table structure for `roc_whisper`
-- ----------------------------
DROP TABLE IF EXISTS `roc_whisper`;
CREATE TABLE `roc_whisper` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `atuid` mediumint(8) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `content` varchar(255) NOT NULL,
  `posttime` int(11) NOT NULL,
  `isread` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `del_flag` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `atuid` (`atuid`,`isread`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
