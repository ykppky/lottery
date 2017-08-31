<?php
//注册 类的自动加载函数
spl_autoload_register(function ($class_name) {
    //通过类名规则自动加载
    if('Model' == substr($class_name, -5)){
        require './Models/' . $class_name . '.class.php';
    }elseif ('Controller' == substr($class_name, -10)){
        require './Controllers/' . $class_name . '.class.php';
    }
});

//前端请求分发
$c = !empty($_GET['c']) ? $_GET['c'] : "Lottery";         //默认载入Lottery这个控制器
$controller_name = $c."Controller";				//构建控制器的类名
$ctrl = new $controller_name();                 //可变类
$act = !empty($_GET['a']) ? $_GET['a'] : "index";   //默认调用控制器的index动作
$action = $act."Action";                            //组装动作名
$ctrl->$action();                                   //可变函数