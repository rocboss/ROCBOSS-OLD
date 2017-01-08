<?php
/**
 * Database configuration
 * You can extend other databases.
 */
return [
# ======> Mysql数据库配置

    # 数据库主机地址
    'db.host' => '127.0.0.1',

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

    # Redis密码
    'redis.auth' => '',

# ======> Xunsearch 配置

    # 服务开关，使用则为true，不使用为false，推荐使用
    'xs.server.switch' => true,

    # 索引服务端配置(IP:PORT)
    'xs.server.index' => '127.0.0.1:8383',

    # 搜索服务端配置
    'xs.server.search' => '127.0.0.1:8384',
];
