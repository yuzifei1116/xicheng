<?php

namespace app\gzh\controller;


use app\gzh\model\user\User;
use app\gzh\model\user\WechatUser;
use service\UtilService;
use think\Cookie;
use think\Request;
use think\Session;
use think\Url;

class Login extends GzhBasic
{
    public function index($ref = '',$spreadUid = 0)
    {
        Cookie::set('is_bg',1);
        $ref && $ref=htmlspecialchars_decode(base64_decode(urldecode($ref)));
        if(UtilService::isWechatBrowser()){
            $this->_logout();
            $openid = $this->oauth($spreadUid);
//            dump($openid);die;
            Cookie::delete('_oen');
            exit($this->redirect(empty($ref) ? Url::build('Index/index') : $ref));
        }
        $this->assign('ref',$ref);
        return $this->fetch();
    }

    public function check(Request $request)
    {
        list($account,$pwd,$ref) = UtilService::postMore(['account','pwd','ref'],$request,true);
        if(!$account || !$pwd) return $this->failed('请输入登录账号');
        if(!$pwd) return $this->failed('请输入登录密码');
        if(!User::be(['account'=>$account])) return $this->failed('登陆账号不存在!');
        $userInfo = User::where('account',$account)->find();
        $errorInfo = Session::get('login_error_info','gzh')?:['num'=>0];
        $now = time();
        if($errorInfo['num'] > 5 && $errorInfo['time'] < ($now - 900))
            return $this->failed('错误次数过多,请稍候再试!');
        if($userInfo['pwd'] != md5($pwd)){
            Session::set('login_error_info',['num'=>$errorInfo['num']+1,'time'=>$now],'gzh');
            return $this->failed('账号或密码输入错误!');
        }
        if(!$userInfo['status']) return $this->failed('账号已被锁定,无法登陆!');
        $this->_logout();
        Session::set('loginUid',$userInfo['uid'],'gzh');
        $userInfo['last_time'] = time();
        $userInfo['last_ip'] = $request->ip();
        $userInfo->save();
        Session::delete('login_error_info','gzh');
        Cookie::set('is_login',1);
        exit($this->redirect(empty($ref) ? Url::build('Index/index') : $ref));
    }

    public function logout()
    {
        $this->_logout();
        $this->successful('退出登陆成功',Url::build('Index/index'));
    }

    private function _logout()
    {
        Session::clear('gzh');
        Cookie::delete('is_login');
    }

}