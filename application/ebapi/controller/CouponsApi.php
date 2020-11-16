<?php
namespace app\ebapi\controller;

use app\ebapi\model\store\StoreCoupon;
use app\ebapi\model\store\StoreCouponIssue;
use app\ebapi\model\store\StoreCouponUser;
use service\JsonService;

/**
 * 小程序优惠券api接口
 * Class CouponsApi
 * @package app\ebapi\controller
 *
 */
class CouponsApi extends AuthController
{
    /**
     * 获取用户优惠券
     * @return \think\response\Json
     */
    public function get_use_coupons($types='')
    {
        switch ($types){
            case 0:case '':
            $list= StoreCouponUser::getUserAllCoupon($this->userInfo['uid']);
            break;
            case 1:
                $list=StoreCouponUser::getUserValidCoupon($this->userInfo['uid']);
                break;
            case 2:
                $list=StoreCouponUser::getUserAlreadyUsedCoupon($this->userInfo['uid']);
                break;
            default:
                $list=StoreCouponUser::getUserBeOverdueCoupon($this->userInfo['uid']);
                break;
        }
        return JsonService::successfuljson($list);
    }
    /**
     * 获取用户优惠券
     * @return \think\response\Json
     */
    public function get_use_coupon(){

        return JsonService::successful('',StoreCouponUser::getUserAllCoupon($this->userInfo['uid']));
    }

    /**
     * 获取可以使用的优惠券
     * @param int $totalPrice
     * @return \think\response\Json
     */
    public function get_use_coupon_order()
    {
        $pids = input('get.pids','');
        $totalPrice = input('get.totalPrice',0);
        $mer_ids = input('get.mer_ids',0);
        if (!$pids) return JsonService::failjson('数据错误');
        $pids = explode(',',$pids);
        $mer_ids = explode(',',$mer_ids);
        return JsonService::successfuljson(StoreCouponUser::beUsableCouponList($this->userInfo['uid'],$pids,$totalPrice,$mer_ids));
    }


//    /**
//     * 领取优惠券
//     * @param string $couponId
//     * @return \think\response\Json
//     */
//    public function user_get_coupon($couponId = '')
//    {
//        if(!$couponId || !is_numeric($couponId)) return JsonService::fail('参数错误!');
//        if(StoreCouponIssue::issueUserCoupon($couponId,$this->userInfo['uid'])){
//            return JsonService::successful('领取成功');
//        }else{
//            return JsonService::fail(StoreCouponIssue::getErrorInfo('领取失败!'));
//        }
//    }
    /**
     * 领取优惠券
     * @param string $couponId
     * @auth:pyp
     * @date:2020/5/11 17:46
     */
    public function user_get_coupon($couponId = '')
    {
        if(!$couponId || !is_numeric($couponId)) return JsonService::failjson('参数错误!');
        $coupon = StoreCoupon::get($couponId)->toArray();
        if (empty($coupon)) return JsonService::failjson('没有此优惠券');
        if ($coupon['is_limit'] == 1 && $coupon['coupon_num'] < 1) return JsonService::failjson('优惠券已领完');
        if ($coupon['is_del'] == 1) return JsonService::failjson('没有此优惠券');
        if ($coupon['status'] != 1) return JsonService::failjson('此优惠券已失效或已过期');
        if ($coupon['self_can_get'] == 0) return JsonService::failjson('此优惠券不可以领取');
        if ($coupon['time_type'] == 2 && $coupon['coupon_end_time'] < time()) JsonService::failjson('此优惠券已过期');

        //查看用户领取过几次
        $count = StoreCouponUser::userGetCount($this->uid, $couponId);
        if ($count >= $coupon['self_max_num']) return JsonService::failjson('已达到领取次数');

        $time = time();
        $data['cid'] = $couponId;
        $data['uid'] = $this->uid;
        $data['type'] = 'get';
        $data['coupon_title'] = $coupon['title'];
        $data['coupon_price'] = $coupon['coupon_price'];
        $data['use_min_price'] = $coupon['use_min_price'];
        $data['coupon_type'] = $coupon['coupon_type'];
        $data['self_can_get'] = $coupon['self_can_get'];
        $data['self_max_num'] = $coupon['self_max_num'];
        $data['is_fail'] = 0;
        $data['status'] = 0;
        $data['coupon_products'] = $coupon['coupon_products'];
        $data['coupon_discount'] = $coupon['coupon_discount'];
        if ($coupon['time_type'] == 1){
            $data['coupon_long_time'] = $coupon['coupon_long_time'];
            $data['coupon_start_time'] = $time;
            $start_time = strtotime(date('Y-m-d',$time));
            $data['coupon_end_time'] = $coupon['coupon_long_time']*3600*24+$start_time-1;
        }elseif ($coupon['time_type'] == 2){
            $data['coupon_start_time'] = $time;
            $data['coupon_end_time'] = $coupon['coupon_end_time'];
        }
        $data['add_time'] = $time;
        StoreCouponUser::beginTrans();
        $res1 = StoreCouponUser::set($data);
        $res2 = 1;
        if ($coupon['is_limit'] == 1){
            $res2 = StoreCoupon::edit(['coupon_num'=>$coupon['coupon_num']-1],$couponId);
        }
        $res = $res1 && $res2;
        StoreCouponUser::checkTrans($res);
        if ($res){
            return JsonService::successfuljson('领取成功');
        }else{
            return JsonService::failjson('领取失败');
        }


    }

    /**
     * 获取一条优惠券
     * @param int $couponId
     * @return \think\response\Json
     */
    public function get_coupon_rope($couponId = 0){
        if(!$couponId) return JsonService::fail('参数错误');
        $couponUser = StoreCouponUser::validAddressWhere()->where('id',$couponId)->where('uid',$this->userInfo['uid'])->find();
        return JsonService::successful($couponUser);
    }
//    /**
//     * 获取  可以领取的优惠券
//     * @param int $limit
//     * @return \think\response\Json
//     */
//    public function get_issue_coupon_list($limit = 2,$page=0)
//    {
//        return JsonService::successful(StoreCouponIssue::getIssueCouponList($this->uid,$limit,$page));
//    }

    /**
     * 获取  可以领取的优惠券
     * @param int $limit
     * @return \think\response\Json
     */
    public function get_issue_coupon_list()
    {
        $limit = input('limit/d',10);
        $page = input('page/d',1);
//        $res = StoreCouponIssue::getIssueCouponList($this->uid,$limit,$page);
        $res = StoreCoupon::getCanGetCoupon($this->uid,$page,$limit);
        return JsonService::successfuljson($res);

    }
}