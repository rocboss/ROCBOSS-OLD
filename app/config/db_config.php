<?php

# 数据库配置项
$db_config = array(
    'database_type' => 'mysql',
    # *必填，默认数据库连接类型: MySQL, 其余类型 MariaDB, MSSQL, Sybase, PostgreSQL, Oracle

    'database_name' => 'rocboss_2_1',
    # *必填，数据库名称

    'server' => 'localhost',
    # *必填，数据库主机名
    
    'username' => 'root',
    # *必填，数据库用户名
    
    'password' => '123123',
    # *必填，数据库密码
    
    'port' => 3306,
    #  默认，数据库端口
    
    'charset' => 'utf8',
    #  默认，编码格式
    
    'option' => array( PDO::ATTR_CASE => PDO::CASE_NATURAL )
    #  默认，pdo选项，请确保pdo已经打开，配置 php.ini
);

?>