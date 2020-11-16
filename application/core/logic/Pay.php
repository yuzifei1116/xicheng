<?php

namespace app\core\logic;

use app\core\util\MiniProgramService;
use app\core\util\WechatService;
use think\Request;

/**
 * Created by PhpStorm.
 * User: xurongyao <763569752@qq.com>
 * Date: 2019/4/8 5:48 PM
 */
class Pay
{
    public static function notify(){
        $request=Request::instance();
//        file_put_contents('D:\www\2001_yuanshi\notify6666666.txt',$request->param('notify_type','wenxin'));
        switch (strtolower($request->param('notify_type','wenxin'))){
            case 'wenxin':
                break;
            case 'weixingzh':
                WechatService::handleNotify();
                break;
            case 'routine': //小程序支付回调
                MiniProgramService::handleNotify();
                break;
            case 'alipay':
                break;
            default:
                echo 121;
                break;
        }
    }
}