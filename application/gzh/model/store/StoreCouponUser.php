<?php


namespace app\gzh\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreCouponUser extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取用户 拥有的优惠券数量
     * @param $uid
     * @param $cid
     * @author: gyz
     * @Time: 2020/5/11 10:48
     * @return int
     */
    public static function getUserCouponNum($uid, $cid)
    {
        $map = [
            'cid' => $cid,
            'uid' => $uid,
        ];
        $num = self::where($map)->count();
        return $num;
    }
    /**
     * 获取用户优惠券（全部）
     * @return \think\response\Json
     */
    public static function getUserAllCoupon($uid,$page,$limit)
    {
        self::checkInvalidCoupon();
        $couponList = self::where('uid',$uid)->page($page,$limit)->order('is_fail ASC,status ASC,add_time DESC')->select()->toArray();
        if (empty($couponList)) return [];
        return self::tidyCouponList($couponList);
    }
    /**
     * 获取用户优惠券（未使用）
     * @return \think\response\Json
     */
    public static function getUserValidCoupon($uid,$page,$limit)
    {
        self::checkInvalidCoupon();
        $couponList = self::where('uid',$uid)->where('status',0)->page($page,$limit)->order('is_fail ASC,status ASC,add_time DESC')->select()->toArray();
        if (empty($couponList)) return [];
        return self::tidyCouponList($couponList);
    }
    /**
     * 获取用户优惠券（已使用）
     * @return \think\response\Json
     */
    public static function getUserAlreadyUsedCoupon($uid,$page,$limit)
    {
        self::checkInvalidCoupon();
        $couponList = self::where('uid',$uid)->where('status',1)->page($page,$limit)->order('is_fail ASC,status ASC,add_time DESC')->select()->toArray();
        if (empty($couponList)) return [];
        return self::tidyCouponList($couponList);
    }
    /**
     * 获取用户优惠券（已过期）
     * @return \think\response\Json
     */
    public static function getUserBeOverdueCoupon($uid,$page,$limit)
    {
        self::checkInvalidCoupon();
        $couponList = self::where('uid',$uid)->where('status',2)->page($page,$limit)->order('is_fail ASC,status ASC,add_time DESC')->select()->toArray();
        if (empty($couponList)) return [];
        return self::tidyCouponList($couponList);
    }
    public static function beUsableCoupon($uid,$price)
    {
        return self::where('uid',$uid)->where('is_fail',0)->where('status',0)->where('use_min_price','<=',$price)->find();
    }

    /**
     * 获取用户可以使用的优惠券
     * @param $uid
     * @param $price
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function beUsableCouponList($uid,$pids,$price=0,$mer_ids){
        $list=self::where('uid',$uid)
            ->whereIn('mer_id',$mer_ids)
            ->where('is_fail',0)
            ->where('status',0)
            ->where('use_min_price','<=',$price)
            ->where('coupon_end_time','>',time())
            ->order('add_time desc')
            ->select();
        if ($list->isEmpty()) return $list;
        $list = $list->toArray();
//        dump($list);die;
        foreach ($list as $key=>&$item){
            $coupon_products = explode(',',$item['coupon_products']);
            $item['coupon_products'] = $coupon_products;
            if ($item['coupon_products'] != ''){
                $result = array_intersect($pids,$coupon_products); //交集
                if (empty($result)){
                    unset($list[$key]);
                }
            }
            $item['coupon_start_time']=date('Y/m/d',$item['coupon_start_time']);
            $item['coupon_end_time']=date('Y/m/d',$item['coupon_end_time']);
        }
        return $list;
    }

    public static function validAddressWhere($model=null,$prefix = '')
    {
        self::checkInvalidCoupon();
        if($prefix) $prefix .='.';
        $model = self::getSelfModel($model);
        return $model->where("{$prefix}is_fail",0)->where("{$prefix}status",0);
    }

    /**
     * 检查 所有用户  过期的优惠券改状态
     */
    public static function checkInvalidCoupon()
    {
        self::where('coupon_end_time','<',time())->where('status',0)->update(['status'=>2]);
    }

    public static function tidyCouponList($couponList)
    {
        $time = time();
        foreach ($couponList as $k=>$coupon){
            if($coupon['is_fail']){
                $coupon['_type'] = 0;
                $coupon['_msg'] = '已失效';
            }else if ($coupon['status'] == 1){
                $coupon['_type'] = 0;
                $coupon['_msg'] = '已使用';
            }else if ($coupon['status'] == 2){
                $coupon['_type'] = 0;
                $coupon['_msg'] = '已过期';
            }else if($coupon['coupon_end_time'] < $time){
                $coupon['_type'] = 0;
                $coupon['_msg'] = '已过期';
            }else{
                if($coupon['coupon_end_time'] > $time && $coupon['status']==0){
                    $coupon['_type'] = 1;
                    $coupon['_msg'] = '可使用';
                }
            }
            $coupon['add_time'] = date('Y/m/d',$coupon['add_time']);
            $coupon['coupon_start_time'] = date('Y/m/d',$coupon['coupon_start_time']);
            $coupon['coupon_end_time'] = date('Y/m/d',$coupon['coupon_end_time']);
            $couponList[$k] = $coupon;
        }
        return $couponList;
    }

    public static function getUserValidCouponCount($uid)
    {
        self::checkInvalidCoupon();
        return self::where('uid',$uid)->where('status',0)->where('is_fail',0)->order('is_fail ASC,status ASC,add_time DESC')->count();
    }

    public static function useCoupon($id)
    {
        return self::where('id',$id)->update(['status'=>1,'use_time'=>time()]);
    }

    public static function addUserCoupon($uid,$cid,$type = 'get')
    {
        $couponInfo = StoreCoupon::find($cid);
        if(!$couponInfo) return self::setErrorInfo('优惠劵不存在!');
        $data = [];
        $data['cid'] = $couponInfo['id'];
        $data['uid'] = $uid;
        $data['coupon_title'] = $couponInfo['title'];
        $data['coupon_price'] = $couponInfo['coupon_price'];
        $data['use_min_price'] = $couponInfo['use_min_price'];
        $data['add_time'] = time();
        $data['end_time'] = $data['add_time']+$couponInfo['coupon_time']*86400;
        $data['type'] = $type;
        return self::set($data);
    }

    /**
     * 用户领取此优惠券的次数
     * @param $uid
     * @param $couponId
     * @auth:pyp
     * @date:2020/5/11 18:05
     */
    public static function userGetCount($uid, $couponId)
    {
        return self::where('uid',$uid)->where('cid',$couponId)->where('type','get')->count();
    }
}