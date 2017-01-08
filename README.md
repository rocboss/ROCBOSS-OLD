## v2.2.1 主要新特性

- 新增全文索引支持(xunsearch)
- 新增积分提现功能
- 新增会员间积分转账功能
- 新增文章模块
- 新增明/暗两套主题，可自主切换
- 新增微信扫码登录模块
- 前端Webpack打包
- 优化系统结构
- 变更加密流程，提高安全性
- 解决2.2.0中存留的一些小BUG

## 安装须知

**环境要求**

1. PHP >= 5.4，MySQL >= 5.5
2. 部署Redis服务器以及对应的PHP Redis扩展
3. 部署[Xunsearch服务器][0](如果不启用可忽略该步骤)
4. 开启pdo_mysql扩展
5. 支持伪静态（建议使用Linux操作系统，**不支持虚拟主机**!）

**安装步骤**

1. 配置网站指向到 web/ 目录下

2. 导入 install.sql 数据库文件

3. 修改配置文件，**app/config/** 下的文件需要分别重命名为 _base.php,_othere.php,_database.php，然后根据注释修改配置。

4. 新建并设置 app/cache 目录777权限

5. 配置文件完全填写结束后，访问首页，管理员登陆后，可进入管理地址 : `你的网址/admin`， 默认管理员 `admin` 密码 `123123123`

6. 关于伪静态，apache环境直接使用 .htaccess 文件，nginx使用如下规则：
    ```
    location / {
        try_files $uri $uri/ /index.php;
    }
    ```

7. 由于使用[七牛云存储][1]，所以需要配置图片处理样式，分割符为“ - ”，必须配置，否则图片无法使用
    - - -
        名称： `800`
        处理接口： 自行控制水印等，宽度800
    - - -
        名称： `100x100`
        处理接口：`imageView2/1/w/100/h/100/q/100`
    - - -
        名称：`800.png`
        处理接口：自行控制水印等，宽度800
    - - -
        名称：`90x68.png`
        处理接口： `imageView2/1/w/90/h/68/q/100/format/png`
    - - -
        名称：`avatar.png`
        处理接口：`imageView2/1/w/100/h/100/q/100/format/png`

8. 针对启用xunsearch的用户，考虑到数据的一致性和老数据的同步，项目根目录下提供 console 脚本文件，项目根目录下执行 ``` ./console index/push-all ``` 命令即可全量推送数据到索引服务器，建议每天定时跑一次该脚本。


**关于2.2.0到2.2.1的升级**

配置修改完相应配置后，连接MySQL，在console中执行如下语句

```
ALTER TABLE `rocboss`.`roc_collection` ADD COLUMN `article_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章ID' AFTER `tid`, DROP INDEX `uid`, ADD INDEX `uid` USING BTREE (`valid`, `uid`, `tid`, `article_id`) comment '';
```

```
ALTER TABLE `rocboss`.`roc_user` ADD COLUMN `salt` char(8) NOT NULL DEFAULT '' COMMENT '盐值' AFTER `password`;
```

```
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现申请表';
```

## v2.2.0 主要新特性

- 系统架构调整，优化Model层逻辑
- 热点数据缓存，提升负载能力
- 大量采用AJAX和PJAX，提升用户体验和系统运行速度
- 新增发帖、回帖本地自动草稿功能
- 新增图片CDN加速
- 新增支付宝积分充值功能
- 新增会员等级制度（可扩展）
- 新增主题打赏功能
- 新增私信手机短信通知功能
- 新增登录、注册人机行为验证

还有很多细节不一一介绍，留给伙伴们自行发现。

#### 附言
由于 v2.2 系列环境要求比较严格，所以还请耐心安装，相关知识不了解的请先自行搜索查询相关资料。

  [0]:http://www.xunsearch.com/
  [1]: https://portal.qiniu.com/signup?code=3lho3ffob4oya
