<?php

namespace app\gzh\model\user;

use app\gzh\model\user\WechatUser;
use basic\ModelBasic;
use app\core\util\WechatService;
use traits\ModelTrait;

class UserRecharge extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function addRecharge($uid,$price,$give_money,$recharge_type = 'weixin',$paid = 0)
    {
        $order_id = self::getNewOrderId();
        return self::set(compact('order_id','uid','price','give_money','recharge_type','paid'));
    }

    public static function getNewOrderId()
    {
        $count = (int) self::where('add_time',['>=',strtotime(date("Y-m-d"))],['<',strtotime(date("Y-m-d",strtotime('+1 day')))])->count();
        return 'wx1'.date('YmdHis',time()).(10000+$count+1);
    }

    public static function jsPay($orderInfo)
    {
        return WechatService::jsPay(WechatUser::uidToOpenid($orderInfo['uid']),$orderInfo['order_id'],$orderInfo['price'],'user_recharge','用户充值');
    }

    /**
     * //TODO用户充值成功后
     * @param $orderId
     */
    public static function rechargeSuccess($orderId)
    {
        $order = self::where('order_id',$orderId)->where('paid',0)->find();
        if(!$order) return false;
        $user = User::getUserInfo($order['uid']);
        self::beginTrans();
        $now_money = bcadd($order['price'],$order['give_money'],2);
        $res1 = self::where('order_id',$order['order_id'])->update(['paid'=>1,'pay_time'=>time()]);
        $res2 = UserBill::income('用户余额充值',$order['uid'],'now_money','recharge',$now_money,$order['id'],0,'成功充值余额'.floatval($order['price']).'元,赠送'.$order['give_money'].'元');
        $res3 = User::edit(['now_money'=>bcadd($user['now_money'],$now_money,2)],$order['uid'],'uid');
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }
}