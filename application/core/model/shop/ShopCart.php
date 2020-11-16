<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\shop;


use basic\ModelBasic;
use think\Cache;
use traits\ModelTrait;

class ShopCart extends ModelBasic
{
    use ModelTrait;

    /**
     * 购物车列表
     * @param $uid
     */
    public static function getCartList($uid)
    {
        $list = self::alias('c')->join('shop_product p','c.product_id=p.id','left')
            ->where('uid',$uid)->where('is_pay',0)->where('is_new',0)
            ->field('c.*,p.id,p.store_name,p.image,p.integral')
            ->order('c.add_time desc')->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        return $list;
    }

    public static function getConfirmOrder($cid,$uid)
    {
        $data = self::alias('c')->join('shop_product p','c.product_id=p.id','left')
            ->where('uid',$uid)->where('is_pay',0)->where('id','in',$cid)->where('is_new',0)
            ->field('p.id,p.store_name,p.image,p.integral,c.num')
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();
        return $data;
    }

    public static function cacheOrderInfo($uid,$cart_info)
    {
        $key = md5(time());
        Cache::set('shop_order_'.$uid.$key,compact('cartInfo','priceGroup','other'),600);
        return $key;
    }
}