<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\menu;


use app\core\model\user\User;
use app\core\model\user\UserBill;
use basic\ModelBasic;
use traits\ModelTrait;

class MenuOrder extends ModelBasic
{
    use ModelTrait;

    public static function getOrderList($where)
    {
        $data = self::setWhere($where)
            ->order('add_time desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time'] );
            if ($v['paid'] == 0) {
                $v['_paid'] = '未支付';
            }else{
                $v['_paid'] = '已支付';
            }
            if ($v['status'] == 0) { //0待付款 1待完成 2待评价 3已完成
                $v['_status'] = '待付款';
            }elseif ($v['status'] == 1){
                $v['_status'] = '待完成';
            }elseif ($v['status'] == 1){
                $v['_status'] = '待评价';
            }else{
                $v['_status'] = '已完成';
            }
            $menu_info = [];
            if ($v['menu'] != ''){
                $carts = explode(',',$v['menu']);
                foreach ($carts as $kk=>&$vv){
                    $cart = MenuCart::get($vv);
                    $menu = Menu::get($cart['menu_id']);
                    $menu_info[$kk]['menu_title'] = $menu['menu_title'];
                    $menu_info[$kk]['num'] = $cart['num'];
                }
            }
            $v['menu_info'] = $menu_info;
        }
        $count = self::setWhere($where)->count();
        return compact('count','data');
    }

    public static function setWhere($where,$model=null)
    {
        if ($model === null) $model = new self();
        if (isset($where['order_id']) && $where['order_id'] != '') $model = $model->where('order_id',$where['order_id']);
        if (isset($where['paid']) && $where['paid'] != '') $model = $model->where('paid',$where['paid']);
        if (isset($where['status']) && $where['status'] != '') $model = $model->where('status',$where['status']);
        if (isset($where['add_time']) && $where['add_time'] != ''){
            list($startTime, $endTime) = explode(' - ', $where['add_time']);
            $model = $model->where('add_time', '>', strtotime($startTime));
            $model = $model->where('add_time', '<', strtotime($endTime)+24*3600);
        }
        return $model;
    }

    public static function OrderList($uid,$type = 0,$page,$limit)
    {
        $model = new self();
        if ($type == 1) $model = $model->where('paid',0);
        if ($type == 2) $model = $model->where('paid',1)->where('status',1);
        if ($type == 3) $model = $model->where('paid',1)->where('status',2);
        $list = $model->where('uid',$uid)
            ->order('add_time desc')
            ->page($page,$limit)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        return $list;
    }

    /**
     * 微信支付 为 0元时 预售尾款不会为0
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayPrice($order_id,$uid){
        $orderInfo = self::where('uid',$uid)->where('order_id',$order_id)->find();
        if(!$orderInfo) return self::setErrorInfo('订单不存在!');
        if($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        self::beginTrans();
        $res1 = UserBill::expend($uid,'点餐','menu','wechat',$orderInfo['pay_price'],$orderInfo['id']);
        $res2 = self::paySuccess($orderInfo,$uid);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 回调
     */
    public static function paySuccess($orderInfo,$uid)
    {
        $res1 = self::edit(['paid'=>1,'pay_time'=>time()],$orderInfo['id']);
        $res2 = User::where('uid',$orderInfo['uid'])->setInc('menu_count');
        $carts = MenuCart::getCartList($uid);
        $cart_info = [];
        $res3 = true;
        foreach ($carts as $k=>&$v){
            $price = Menu::get($v['menu_id'])['price'];
            $v['price'] = $price;
            $v['total_price'] = bcmul($price,$v['num'],2);
            $cart_info[$k] = $v;
        }
        if (!empty($cart_info)){
            $res3 = MenuCartInfo::setAll($cart_info)
        }
        return $res1 && $res2 && $res3;
    }

    public static function getTwoCount()
    {
        $count = self::group('uid')->count(); //用户购买的次数
        $two_count = User::where('menu_count','>',1)->count();
        return bcdiv($two_count,$count,2)*100;
    }

    public static function getBadge($where){
        $model = self::where('paid',1);
        $total_price = self::getModelTime($where,$model)->sum('money');
        return [
            [
                'name'=>'收入总额',
                'field'=>'元',
                'count'=>$total_price,
                'background_color'=>'layui-bg-blue',
                'col'=>4
            ]
        ];
    }
}