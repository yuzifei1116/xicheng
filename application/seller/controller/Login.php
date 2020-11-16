<?php

namespace app\seller\controller;


use app\seller\model\system\SystemAdmin;
use app\seller\model\merchant\MerchantList;
use service\CacheService;
use service\UtilService;
use think\Request;
use think\Response;
use think\Session;
use think\Url;

/**
 * 登录验证控制器
 * Class Login
 * @package app\seller\controller
 */
class Login extends SystemBasic
{
    public function index()
    {
        if (Session::has('sellerId') && Session::has('sellerInfo')) $this->redirect('index/index');
        return $this->fetch();
    }

    /**
     * 登录验证 + 验证码验证
     */
    public function verify(Request $request)
    {
        if(!$request->isPost()) return $this->failed('请登陆!');
        list($account,$pwd,$verify) = UtilService::postMore([
            'account','pwd','verify'
        ],$request,true);
        //检验验证码
        if(!captcha_check($verify)) return $this->failed('验证码错误，请重新输入');
        $error  = Session::get('seller_login_error')?:['num'=>0,'time'=>time()];
        $error['num'] = 0;
        if($error['num'] >=5 && $error['time'] > strtotime('- 5 minutes'))
            return $this->failed('错误次数过多,请稍候再试!');
        //检验帐号密码
        $res = MerchantList::login($account,$pwd);
        if($res){
            Session::set('seller_login_error',null);
            return $this->redirect(Url::build('Index/index'));
        }else{
            $error['num'] += 1;
            $error['time'] = time();
            Session::set('seller_login_error',$error);
            return $this->failed(MerchantList::getErrorInfo('账号错误，请重新输入'));
        }
    }

    public function captcha()
    {
        ob_clean();
        $captcha = new \think\captcha\Captcha([
            'codeSet'=>'0123456789',
            'length'=>4,
            'fontttf'=>'4.ttf',
            'fontSize'=>30
        ]);
        return $captcha->entry();
    }

    /**
     * 退出登陆
     */
    public function logout()
    {
        MerchantList::clearLoginInfo();
        $this->redirect('Login/index');
    }
}