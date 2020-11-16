<?php

namespace app\gzh\controller;


use app\gzh\model\store\StoreOrder;
use app\gzh\model\user\User;
use app\gzh\model\user\WechatUser;
use service\UtilService;
use think\Cookie;
use think\Session;
use think\Url;
use service\JsonService;


class AuthController extends GzhBasic
{
    /**
     * 用户ID
     * @var int
     */
    protected $uid;

    /**
     * 用户信息
     * @var
     */
    protected $userInfo;

    protected function _initialize()
    {
        parent::_initialize();
        try{
//            $uid = User::getActiveUid();
            $uid = 5; //todo 测试用
        }catch (\Exception $e){
            $spreadUid = (int)$this->request->get('spuid',0);
            if($this->request->isAjax()){//todo 如果是ajax请求
                return JsonService::failjson('未授权','',901);
            }else{
                Cookie::set('is_login',0);
//                $url=$this->request->url(true);//todo 此处要修改成 参数的url
//                return $this->redirect(Url::build('Login/index',['ref'=>base64_encode(htmlspecialchars($url))]));
                $url=$this->request->get('re_url');//base64之后的参数
                $url = !empty($url) ? urldecode($url) : base64_encode($this->request->url(true));//todo 如果是空的就跳首页
//                dump($url);die;
//                return $this->redirect(Url::build('Login/index',['ref'=>urlencode($url)]));
                return $this->redirect(Url::build('Login/index',['ref'=>urlencode($url),'spreadUid'=>$spreadUid]));
            }
        }
//        dump($uid);die;
        $this->userInfo = User::getUserInfo($uid);
        if(!$this->userInfo || !isset($this->userInfo['uid'])) return $this->failed('读取用户信息失败!!',0,'提示信息',903);
        if(!$this->userInfo['status']) return $this->failed('已被禁止登陆!',0,'提示信息',902);
        $this->uid = $this->userInfo['uid'];
        $this->assign('userInfo',$this->userInfo);
    }

}