<?php

namespace app\gzh\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreCoupon extends ModelBasic
{
    use ModelTrait;
    /**
     * 获取可以领取的优惠券列表
     * @return array
     * @AUTH GYZ
     * @TIME 2020/5/10 22:52
     */
    public static function getCanGetCoupon($uid,$page=1,$limit=10,$mer_id=0)
    {
        self::checkInvalidCoupon();

        $map = [
            'status' => 1,
            'is_del' => 0,
            'self_can_get' => 1,
        ];

        $list = self::where($map)->where(' is_limit = 0 or coupon_num > 0');
        if ($mer_id>0) $list = $list->where('mer_id',$mer_id);
        $list = $list->page($page,$limit)->select();
        foreach ($list as $k=>&$v){
            $has_get = StoreCouponUser::getUserCouponNum($uid,$v['id']);
            $v['can_get_num'] = $v['self_max_num']-$has_get;
        }
//        echo self::getLastSql();die;
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            if (empty($v['coupon_start_time']) || empty($v['coupon_end_time'])){
                $v['coupon_start_time'] = date('Y-m-d',time());
                $v['coupon_end_time'] = date('Y-m-d',time()+$v['coupon_long_time']*3600*24);
            }else{
                $v['coupon_start_time'] = date('Y-m-d',$v['coupon_start_time']);
                $v['coupon_end_time'] = date('Y-m-d',$v['coupon_end_time']);
            }
//            $v['coupon_start_time'] = empty($v['coupon_start_time']) ? 'now' : date('Y-m-d',$v['coupon_start_time']);
//            $v['coupon_end_time'] = empty($v['coupon_end_time']) ? 'future' : date('Y-m-d',$v['coupon_end_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $v['_msg'] = ''; //凑数
            $v['_type'] = ''; //凑数
        }
        return $list;

    }

    public static function checkInvalidCoupon()
    {
        self::where('coupon_end_time','<',time())
            ->where('time_type',2)
            ->where('status',1)
            ->update(['status'=>2]);
    }
}