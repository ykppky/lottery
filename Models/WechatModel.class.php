<?php
class WechatModel {

    /**
     * WechatModel constructor.
     */
    function __construct() {

    }

    /**
     * 跳转到微信网页授权登录URL
     * @param $redirect_url     用户同意登录，收集用户信息的URL
     * @param $scope            微信授权登录方式
     * @param $state            授权登录类型
     */
    function jumpWechatLogin($redirect_url, $scope, $state) {
        $open_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx011cddc56212c6ed&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        header("Location: ".$open_url);
    }


    /**
     * 获取当前网页URL方法
     * @return string   当前请求路径
     */
    function getPageURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on")
        {
          $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
        {
          $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } 
        else
        {
          $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }


    /**
     * 获取用户信息
     * @return mixed    数组-用户信息
     */
    function getUserInfo() {
        //用户同意授权，获取code
        $code = !empty($_GET['code']) ? $_GET['code'] : exit();
        $state = !empty($_GET['state']) ? $_GET['state'] : exit();
        //通过code换取网页授权openid
        $url_openid="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx011cddc56212c6ed&secret=b2aa40a0d1f71b9ba4103ca05c22453e&code=$code&grant_type=authorization_code";
        $json_token = file_get_contents($url_openid);
        $arr_token = json_decode($json_token);
        //判断授权类型
        if($state == "base"){  
            return $arr_token;         //返回包含openid的数组
        }elseif($state == "userinfo"){
            $access_token = $arr_token->{'access_token'};         //获取access_token
            $openid = $arr_token->{'openid'};                     //获取openid
            //拉取用户信息(需scope为 snsapi_userinfo)
            $url_info="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
            $json_info = file_get_contents($url_info);
            return json_decode($json_info);         //返回用户信息数组
        }
    }


    /**
     * 存储用户信息到数据库、cookie
     * @param $user_data    用户数据
     * @param $table_header   存储用户信息的数据库的表前缀
     */
    function storeUserInfo($user_data, $table_header) {
        $db = new DatabaseModel();
        $pdo = $db->connectDatabase();
        //获取授权类型
        $state = !empty($_GET['state']) ? $_GET['state'] : exit();
        if($state == "base"){
            setcookie("openid", $user_data->{'openid'}, time()+36000);
            //把用户数据存进数据库中
            $sql_insert="INSERT ".$table_header."userbase ( openid, logintime ) values ( ?, ? ) ON DUPLICATE KEY UPDATE logintime = ?";
            $sth = $pdo->prepare($sql_insert);
            $sth->execute(array($user_data->{'openid'}, time(), time())) or die("数据库错误: " . $sth->errorInfo()[2]);
        }else if($state == "userinfo"){
            $openid = $user_data->{'openid'};
            setcookie("openid", $openid, time()+36000);
            //把用户数据存进数据库中
            $sql_insert = "INSERT ".$table_header."userinfo (openid, nickname, sex, province, city, country, headimgurl, privilege) values (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE headimgurl = ?";
            $sth = $pdo->prepare($sql_insert);
            $sth->execute(array($user_data->{'openid'}, $user_data->{'nickname'}, $user_data->{'sex'}, $user_data->{'province'}, $user_data->{'city'}, $user_data->{'country'}, $user_data->{'headimgurl'}, json_encode($user_data->{'privilege'}), $user_data->{'headimgurl'})) or die("数据库错误: " . $sth->errorInfo()[2]);
            //在cookie中设置曾经以snsapi_base方式登录过的标记
            setcookie("userinfo", "true", time()+36000);
        }
        //跳转到登录前的url
        header("Location: ".$_COOKIE["last_url"] );
    }


    /**
     * 授权登录信息获取
     *
     * @param $table_userinfo   存储用户信息的数据库表的名字
     */
    function wxOAuthLogin($table_header) {
        $user_data = $this->getUserInfo();
        $this->storeUserInfo($user_data, $table_header);
    }


    /**
     * 登录检查
     * 默认以snsapi_base的方式授权登录。如果要求以scope=snsapi_userinfo的方式授权登录，$state参数必须为“userinfo”。
     * 防止用户在别的页面以snsapi_base授权登录过被cookie记住openid后，来到投稿页面不以snsapi_userinfo方式授权登录，导致无法获取微信用户详细信息。
     * 因为业务要求是投稿要获取微信昵称，点赞只需要获取openid.
     * @param $redirect_url     用户同意登录，收集用户信息的URL，必须经过urldecode()
     * @param bool $isUserInfo  是否强制以snsapi_userinfo的方式登录，true为强制要求重新登录
     * @return mixed            openid
     */
    function loginCheck($redirect_url, $isUserInfo = false) {
        //根据登录类型，更改微信授权登录GET请求参数
        $scope = "";
        $state = "";
        if($isUserInfo){
            $scope = "snsapi_userinfo";
            $state = "userinfo";
        }else{
            $scope = "snsapi_base";
            $state = "base";
        }
        //进行是否即使cookie中有openid也登录的判断
        $flag = false;
        if($isUserInfo){
            if(!isset($_COOKIE["userinfo"])){
                $flag = true;
            }
        }
        //判断cookie中是否有openid
        if (!isset($_COOKIE["openid"]) || $flag){
            $pageUrl = $this->getPageURL();
            //把当前请求url存进cookie中
            setcookie("last_url", $pageUrl, time()+600);
            $this->jumpWechatLogin($redirect_url,$scope,$state);
            die();   //没有登录终止执行
        }else if($flag){
            return $_COOKIE["openid"];   //返回openid
        }
    }
    

}

