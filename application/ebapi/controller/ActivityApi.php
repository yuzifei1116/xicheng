<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\ebapi\controller;


use app\core\model\activity\Activity;
use app\core\model\activity\ActivityComment;
use app\core\model\activity\ActivityOrder;
use app\core\model\coupon\Coupon;
use app\core\model\coupon\CouponUse;
use app\core\model\user\User;
use app\core\model\user\UserBill;
use app\core\util\SystemConfigService;
use service\JsonService;

class ActivityApi extends AuthController
{
    public static function whiteList()
    {
        return [
            'activity',
            'order_list',
            'comment',
            'qrcode',
        ];
    }

    /**
     * 活动门票列表
     */
    public function activity()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',4);
        $activity = Activity::getTicketList($page,$limit); //TODO 门票列表
        return JsonService::successfuljson($activity);
    }

    /**
     * 购买
     */
    public function buy()
    {
        $activity_id = input('get.activity_id/d',0);
        $num = input('get.num/d',1);
        $integral = input('get.integral/d',0);
        $coupon_id = input('get.coupon_id/d',0);
        if (!$activity_id) return JsonService::failjson('数据错误');

        $userInfo = User::get($this->uid);
        if ($userInfo['integral'] < $integral) return JsonService::failjson('积分不足');

        //验证活动状态
        $activity = Activity::get($activity_id);
        if($activity['status'] != 1) return JsonService::failjson('活动未上线');
        if($activity['start_time'] > time() || $activity['end_time'] < time()) return JsonService::failjson('活动暂未上线');
        $order = $this->create_order($activity,$integral,$coupon_id,$num); //创建订单
        $info = ['oid'=>$order['id']];
        if ($order['pay_price'] <= 0){
            if (ActivityOrder::jsPayPrice($order['order_id'], $this->userInfo['uid'])){
                return JsonService::status('success', '微信支付成功',$info);
            }else{
                return JsonService::status('pay_error','微信支付失败');
            }
        }else{ //微信支付
            if (ActivityOrder::jsPay($order['order_id'])){
                return JsonService::status('success', '微信支付成功',$info);
            }else{
                return JsonService::status('pay_error','微信支付失败');
            }
        }
    }

    /**
     *创建订单
     */
    public function create_order($activity,$integral = 0,$coupon_id = 0,$num)
    {
        //优惠券抵扣
        $discount_price = 0; //TODO 优惠券抵扣金额
        if ($coupon_id){
            $coupon = Coupon::get(['id'=>$coupon_id,'is_del'=>0,'status'=>1]);
            if (empty($coupon)) return JsonService::failjson('没有此优惠券');
            if ($coupon['start_time'] > time() || $coupon['end_time'] < time()) return JsonService::failjson('此优惠券暂不能使用');
            $coupon_use = CouponUse::get(['coupon_id'=>$coupon_id,'uid'=>$this->uid,'is_use'=>0]);
            if (empty($coupon_use)) return JsonService::failjson('请先领取优惠券');
            $discount_price = $coupon['money'];
        }

        //积分抵扣
        $integral_deduction = 0; //TODO 积分抵扣金额
        if ($integral){
            $integral_ratio = SystemConfigService::get('integral_ratio'); //积分抵扣比例
            $integral_deduction = bcmul($integral,$integral_ratio,2); //抵扣金额
        }

        //门票总金额
        $money = bcmul($activity['price'],$num,2); //门票总金额
        $pay_price = $money; //支付金额
        if ($discount_price && $money >= $discount_price) $pay_price = bcsub($money,$discount_price,2);
        if ($integral_deduction && $pay_price >= $integral_deduction) $pay_price = bcsub($money,$integral_deduction,2);

        $data = [
            'order_id' => 'ac'.date('YmdHis').mt_rand(100000,999999),
            'activity_id'=>$activity['id'],
            'uid'=>$this->uid,
            'people_num'=>$num,
            'money'=>$money,
            'pay_price'=>$pay_price,
            'discount_price'=>$discount_price,
            'coupon_id'=>$coupon_id,
            'integral_deduction'=>$integral_deduction,
            'use_integral'=>$integral,
            'paid'=>0,
            'status'=>0,
            'pay_time'=>0,
            'add_time'=>time()
        ];
        ActivityOrder::beginTrans();
        $order = ActivityOrder::set($data); //创建订单
        $res2 = true;
        $res22 = true;
        $res3 = true;
        $res33 = true;
        if ($coupon_id){ //使用优惠券
            $res2 = CouponUse::edit(['is_use'=>1],$coupon_use['id']);
            $res22 = UserBill::expend($this->uid,'购买门票','activity','coupon',0,$order['id']);
        }
        if ($integral){ //减积分
            $res3 = User::where('uid',$this->uid)->setDec('integral',$integral);
            $res33 = UserBill::expend($this->uid,'购买门票','activity','integral',$integral,$order['id']);
        }
        $res = $order && $res2 && $res22 && $res3 && $res33;
        ActivityOrder::checkTrans($res);
        if ($res){
            return $order;
        }else{
            return JsonService::failjson('创建订单失败');
        }

    }

    /**
     * 门票订单列表
     */
    public function order_list()
    {
        $type = input('get.type/d',0); //0全部 1待付款 2待取货 3待评价 4已完成
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = ActivityOrder::OrderList($this->uid,$type,$page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 订单详情
     * @throws \think\exception\DbException
     */
    public function order_detail()
    {
        $oid = input('get.oid/d',0);
        if (!$oid) return JsonService::failjson('数据错误');
        $order = ActivityOrder::get($oid);
        if (empty($order)) return JsonService::failjson('订单不存在');
        $order['_activity'] = Activity::get($order['activity_id']);
        if ($order['paid']){
            $order['_paid'] = '已支付';
        }else{
            $order['_paid'] = '未支付';
        }
        if ($order['status'] == 0){
            $order['_status'] = '待领取';
        }elseif($order['status'] == 1){
            $order['_status'] = '待评价';
        }elseif($order['status'] == 2){
            $order['_status'] = '已完成';
        }elseif($order['status'] == 3){
            $order['_status'] = '已取消';
        }
        if ($order['pay_time']) $order['pay_time'] = date('Y-m-d H:i:s',$order['pay_time']);
        $order['add_time'] = date('Y-m-d H:i:s',$order['add_time']);
    }

    /**
     * 取消订单
     * @throws \think\exception\DbException
     */
    public function cancel_order()
    {
        $oid = input('get.oid/d',0);
        if (!$oid) return JsonService::failjson('数据错误');
        $order = ActivityOrder::get($oid);
        if (empty($order)) return JsonService::failjson('订单不存在');
        if ($order['paid']) return JsonService::failjson('数据错误');
        if ($order['status'] == 3) return JsonService::failjson('订单已取消');
        if (ActivityOrder::edit(['status'=>3],$oid)){
            return JsonService::successful('取消成功');
        }else{
            return JsonService::failjson('取消失败');
        }
    }

    /**
     * 评论
     */
    public function comment()
    {
        $oid = input('post.oid/d',0);
        $content = input('post.content','');
        if (!$oid) return JsonService::failjson('数据错误');
        if ($content == '') return JsonService::failjson('请输入评论内容');
        if (strlen($content) > 240) return JsonService::failjson('评论内容80字以内');
        $order = ActivityOrder::get($oid);
        if (empty($order)) return JsonService::failjson('数据不存在');
        if ($order['uid'] != $this->uid) JsonService::failjson('数据错误');
        if ($order['status'] != 1) return JsonService::failjson('数据错误');
        ActivityComment::beginTrans();
        $res1 = ActivityComment::set(['activity_id'=>$order['activity_id'],'uid'=>$this->uid,'content'=>$content,'add_time'=>time()]);
        $res2 = ActivityOrder::edit(['status'=>2],$oid);
        $res = $res1 && $res2;
        ActivityComment::checkTrans($res);
        if ($res){
            return JsonService::successfuljson('评论成功');
        }else{
            return JsonService::failjson('评论失败');
        }
    }

    /**
     * 查看二维码
     * @return string|void
     * @throws \think\exception\DbException
     */
    public function qrcode()
    {
        $order_id = input('get.order_id/d',0);
        $order = ActivityOrder::get(['order_id'=>$order_id]);
        if (empty($order)) return JsonService::failjson('数据不存在');
        if ($order['uid'] != $this->uid) return JsonService::failjson('数据错误');
        if ($order['status'] != 0) return JsonService::failjson('数据错误');

        $dir = 'public/qrcode/activity/'.date('Y/m/d');
        if(!is_dir($dir)){
            // 创建文件夹
            mkdir($dir,777,true);
        }
        $file = $dir.'/'.$order_id.'.png';
        self::qr_code($order_id,$file);
        return JsonService::successfuljson(request()->domain() . '/'.$file);
    }

    /**
     * 创建永久二维码
     * @param $id
     * @param $type
     */
    public function qr_code($order_id,$file)
    {
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel = 'L';  //容错级别
        $matrixPointSize = 5;      //生成图片大小
        \QRcode::png($order_id,$file, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}