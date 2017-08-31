<?php
header("Content-type:text/html;charset=utf-8");
setcookie("openid", "", time() - 3600);         //删除微信openid
setcookie("userinfo", "", time() - 3600);       //删除微信授权登录标记
echo "<meta http-equiv='refresh' content='3; url=./index.php' /><h2 align='center'>清空微信浏览器cookie成功，3秒后自动跳转...</h2>";
//header("Location: index.php");         //跳转到抽奖页面