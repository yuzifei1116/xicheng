<?php

namespace app\seller\model\ump;


use app\admin\model\wechat\WechatUser as UserModel;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class StoreCategory
 * @package app\admin\model\store
 */
class StoreCouponUser extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where,$mer_id){
        $model = new self;
        if($where['status'] != '')  $model = $model->where('status',$where['status']);
        if($where['is_fail'] != '')  $model = $model->where('status',$where['is_fail']);
        if($where['coupon_title'] != '')  $model = $model->where('coupon_title','LIKE',"%$where[coupon_title]%");
        if($where['nickname'] != ''){
            $uid = UserModel::where('nickname','LIKE',"%$where[nickname]%")->column('uid');
            $model = $model->where('uid','IN',implode(',',$uid));
        };
//        $model = $model->where('is_del',0);
        $model = $model->where('mer_id',$mer_id)->order('id desc');
        return self::page($model,function ($item){
            $item['nickname'] = UserModel::where('uid',$item['uid'])->value('nickname');
        },$where);
    }

    /**
     * 给用户发放优惠券
     * @param $coupon
     * @param $user
     * @return int|string
     */
    public static function setCoupon($coupon,$user){
        $data = array();
        $time = time();
        foreach ($user as $k=>$v){
            $data[$k]['cid'] = $coupon['id'];
            $data[$k]['uid'] = $v;
            $data[$k]['coupon_title'] = $coupon['title'];
            $data[$k]['coupon_price'] = $coupon['coupon_price'];
            $data[$k]['use_min_price'] = $coupon['use_min_price'];
            $data[$k]['coupon_type'] = $coupon['coupon_type'];
            $data[$k]['is_fail'] = 0; //1已失效 0有效
            $data['status'] = 0;
            $data[$k]['self_can_get'] = $coupon['self_can_get'];
            $data[$k]['self_max_num'] = $coupon['self_max_num'];
            $data[$k]['coupon_products'] = $coupon['coupon_products'];
            $data[$k]['coupon_discount'] = $coupon['coupon_discount'];
            if ($coupon['time_type'] == 1){
                $data[$k]['coupon_long_time'] = $coupon['coupon_long_time'];
                $data[$k]['coupon_start_time'] = $time;
                $start_time = strtotime(date('Y-m-d',$time));
                $data[$k]['coupon_end_time'] = $coupon['coupon_long_time']*3600*24+$start_time-1;
            }elseif ($coupon['time_type'] == 2){
                $data[$k]['coupon_start_time'] = $time;
                $data[$k]['coupon_end_time'] = $coupon['coupon_end_time'];
            }
            $data[$k]['add_time'] = $time;
//            $data[$k]['end_time'] = $data[$k]['add_time']+$coupon['coupon_time']*86400;
        }
        $data_num = array_chunk($data,30);
        self::beginTrans();
        $res = true;
        foreach ($data_num as $k=>$v){
          $res = $res && self::insertAll($v);
        }
        self::checkTrans($res);
        return $res;
    }

    /**
     * TODO 恢复优惠券
     * @param $id
     * @return StoreCouponUser|bool
     */
    public static function recoverCoupon($id)
    {
        $status = self::where('id',$id)->value('status');
        if($status) return self::where('id',$id)->update(['status'=>0,'use_time'=>'']);
        else return true;
    }
}