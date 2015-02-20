<?php

# 数据库配置项
$db_config = array(
    'database_type' => 'mysql',
    # *必填，数据库连接类型: MySQL, MariaDB, MSSQL, Sybase, PostgreSQL, Oracle
    
    'database_name' => '',
    # *必填，数据库名称
    
    'server' => '',
    # *必填，数据库主机名
    
    'username' => '',
    # *必填，数据库用户名
    
    'password' => '',
    # *必填，数据库密码
    
    'port' => 3306,
    # 选填，数据库端口
    
    'charset' => 'utf8',
    # 选填，编码格式
    
    'option' => array(
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    )
    # 选填，pdo选项，请确保pdo已经打开，配置 php.ini
);

# 系统配置项
$sys_config = array(
    'sitename' => '又一个ROCBOSS社区',
    # 网站名称

    'keywords' => '',
    # 网站关键词，以,隔开

    'description' => '',
    # 网站描述，建议不要超过100字
    
    'version' => 'V2.0',
    # 系统当前版本
    
    'ROCKEY' => '58sdfgh78#frc211',
    # 网站密钥，不少于14位，请定期更改，切勿泄露
    
    'db_switch' => true,
    # 数据库开关，开启true，关闭false，不需要使用数据库时请配置 false

    'join_switch' => false,
    # 用户注册开关，开启true，关闭false

    'ad' => '',
    # 广告代码

    'scores' => array(
    # 用户积分策略
            'register' => 25,
            # 注册积分

            'topic' => 2,
            # 创建主题

            'reply' => 1,
            # 创建回复

            'praise' => 1,
            # 主题被赞

            'whisper' => 5,
            # 私信积分(扣除)

            'sign' => rand(1, 10)
            # 签到积分，请输入整数范围
        )
);

# 模板配置项 (默认无需修改)
$tpl_config = array(
    'tpl_dir' => 'rocboss',
    # 设置模板目录
    
    'tpl_ext' => '.tpl',
    # 设定模板后缀
    
    'tpl_cache' => 'template',
    # 模板编译目录, 默认无需修改
    
    'tpl_time' => '0'
    # 缓存生命周期 单位秒, 0是每次都重新编译, -1是永不过期
);

# 自定义扩展功能配置项

# QQ登录
$qq_config = array(
    'appid' => '',
    # APPID

    'appkey' => ''
    # APPKEY
);

?>