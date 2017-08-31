<?php
/**
 * Created by Greatfar
 * Date: 2017/5/21
 * Time: 11:12
 */

class LotteryController{

    function __construct(){

    }


    /**
     * 首页
     */
    function indexAction() {
        header("Content-type:text/html;charset=utf-8");
        //检查是否已经登录
        $wechat = new WechatModel();
        $redirect_url = "http%3A%2F%2Fwximg.gzxd120.com%2Flottery%2Findex.php%3Fc%3DWechat%26a%3DcollectUserInfo";
        $wechat->loginCheck($redirect_url, true);
        //获取openid
        $openid = !empty($_COOKIE['openid']) ? $_COOKIE['openid'] : exit();
        //检查是否已经分配奖项
        $lottery = new LotteryModel();
        $invflag = $lottery->checkAward($openid);
        //获取抽奖信息
        $arr_award = $lottery->getAwardStatus($openid);
        //载入首页视图
        require './Views/award.html';
    }


    /**
     * 标记抽奖状态
     */
    function markAwardStatusAction() {
        //获取openid
        $openid = !empty($_COOKIE[openid]) ? $_COOKIE[openid] : exit();
        $lottery = new LotteryModel();
        $lottery->markAwardStatus($openid);
    }


    /**
     * 获奖列表
     */
    function awardListAction() {
        //检查是否已经登录
        $wechat = new WechatModel();
        $redirect_url = "http%3A%2F%2Fwximg.gzxd120.com%2Flottery%2Findex.php%3Fc%3DWechat%26a%3DcollectUserInfo";
        $wechat->loginCheck($redirect_url, true);
        //获取页码
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        //实例化模型层
        $lottery = new LotteryModel();
        //分页数据计算
        $perNumber = 20;    //每页显示的记录数
        $paginationNumber = 5;  //页码导航显示的页码个数
        $totalNumber  = $lottery->getTotalRecord();     //获取记录总数
        $totalPage = ceil ( $totalNumber / $perNumber );    //计算页数
        $paginationNumber = min($totalPage, $paginationNumber);  //如果定义的页码导航显示页码个数大于实际总页数时，把页码导航页码个数重置为实际页数
        $endPage = $page + floor($paginationNumber/2) <= $totalPage ? $page + floor($paginationNumber/2) : $totalPage;  //计算页码导航结束页号
        $startPage = $endPage - $paginationNumber + 1;  //计算页码导航开始页号
        if($startPage < 1) {  //处理页码导航开始页号小于1的情况
            $endPage -= $startPage - 1;  //把结束页码重置为实际最大页码
            $startPage = 1;
        }
        $startCount = ($page - 1) * $perNumber;    // 根据页码计算出开始的记录
        //获取获奖列表
        $arr_sqldata = $lottery->getAwardList($startCount, $perNumber);
        //载入获奖列表视图
        require './Views/awardlist.html';
    }


    /**
     * 领奖令牌
     */
    function getAwardTokenAction() {
        //检查是否已经登录
        $wechat = new WechatModel();
        $redirect_url = "http%3A%2F%2Fwximg.gzxd120.com%2Flottery%2Findex.php%3Fc%3DWechat%26a%3DcollectUserInfo";
        $wechat->loginCheck($redirect_url, true);
        //获取openid
        $openid = !empty($_COOKIE[openid]) ? $_COOKIE[openid] : exit();
        //获取个人获奖信息
        $lottery = new LotteryModel();
        $arr_sqldata = $lottery->awardToken($openid);
        require './Views/token.html';
    }


}