<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 10:13
 */

namespace app\gzh\model\shop;

use app\gzh\model\user\UserAddress;
use basic\ModelBasic;
use traits\ModelTrait;

class ShopOrder extends ModelBasic
{
    use ModelTrait;

    public static function prizeRecord($act_id,$userInfo,$result,$time)
    {
        $data['act_id'] = $act_id;
        $data['prize_id'] = $result['prize_id'];
        $data['uid'] = $userInfo['uid'];
        $data['avatar'] = $userInfo['avatar'];
        $data['username'] = $userInfo['nickname'];
        $data['tel'] = $userInfo['phone'];
        $data['addr'] = UserAddress::getAddr($userInfo['uid']);
        $data['status'] = 1;
        $data['add_time'] = $time;
        return self::set($data);
    }

    public static function getPrizeUserCount($uid,$aid)
    {
        return self::where('uid',$uid)->where('act_id',$aid)->count();
    }

    public static function getOrderList($uid,$page,$limit)
    {
        $list = self::where('uid',$uid)
            ->where('is_del',0)
            ->field('id,order_id,product_id,total_num,total_integral,status')
            ->order('add_time desc')
            ->page($page,$limit)
            ->select();
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['info'] = ShopProduct::getShopProduct($v['product_id']);
        }

        return $list;
    }

    /**
     * 订单详情
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @auth:pyp
     * @date:2020/5/19 9:29
     */
    public static function getOrderDetail($id)
    {
        $order = self::where('is_del',0)->where('id',$id)->find();
        if (empty($order)) return [];
        $order['info'] = ShopProduct::getShopProduct($order['product_id']);
        return $order;
    }
}