<?php
class DatabaseModel {

    /**
     * DatabaseModel constructor.
     */
    function __construct() {
        
    }


    /**
     * 连接数据库方法
     * @return resource 数据库连接对象
     */
    function connectDatabase() {
        $dbms='mysql';                                      //数据库类型
        $dbName='wximg_gzxd120_com';                        //使用的数据库
        $user='xdwximg';                                    //数据库连接用户名
        $pwd='XDsql%*0308';                                 //数据库连接密码
        $host='rds10mh1rv5l0jb7wnxa.mysql.rds.aliyuncs.com';//数据库主机名
        $dsn="$dbms:host=$host;port=3306;dbname=$dbName";
        $pdo=new PDO($dsn, $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        return $pdo;
    }



}




