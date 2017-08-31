<?php
/**
 * Created by Greatfar.
 * Date: 2017/8/23
 * Time: 15:19
 */

class WechatController{
    /**
     * WechatController constructor.
     */
    public function __construct(){
    }


    /**
     * 微信登录-用户信息收集
     */
    function collectUserInfoAction() {
        header("Content-type:text/html;charset=utf-8");
        //调用微信模块-授权登录方法
        $wechat = new WechatModel();
        $user_data = $wechat->wxOAuthLogin("lottery_");
    }


}