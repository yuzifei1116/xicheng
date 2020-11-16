<?php

namespace app\gzh\model\store;


use app\gzh\model\store\StoreCombination;
use app\gzh\model\store\StoreOrderCartInfo;
use app\gzh\model\store\StoreOrderStatus;
use app\gzh\model\store\StoreProductReply;
use app\gzh\model\user\User;
use app\gzh\model\user\UserAddress;
use app\gzh\model\user\UserBill;
use app\gzh\model\user\WechatUser;
use basic\ModelBasic;
use behavior\gzh\StoreProductBehavior;
use behavior\gzhwechat\PaymentBehavior;
use service\HookService;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UtilService;
use app\core\util\WechatService;
use app\core\util\WechatTemplateService;
use think\Cache;
use think\Db;
use think\Request;
use think\Url;
use traits\ModelTrait;

class StoreOrder extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected static $payType = ['weixin'=>'微信支付','yue'=>'余额支付','offline'=>'线下支付'];

    protected static $deliveryType = ['send'=>'商家配送','express'=>'快递配送'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    protected function setCartIdAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    protected function getCartIdAttr($value)
    {
        return json_decode($value,true);
    }

    /**
     * 获取订单的各个价格信息
     * @param $cartInfo
     * @return array
     * @author:
     * @Time: 2020/5/30 18:14
     */
    public static function getOrderPriceGroup($cartInfo)
    {
        $storePostage = floatval(SystemConfigService::get('store_postage'))?:0; //基础邮费
        $storeFreePostage =  floatval(SystemConfigService::get('store_free_postage'))?:0; //满多少包邮 都是自营产品
        $totalPrice = self::getOrderTotalPrice($cartInfo);
        $merTotalPrice = self::getOrderTotalPriceEveryMer($cartInfo);
        $costPrice = self::getOrderCostPrice($cartInfo);
        $presaleWkPrice = self::getOrderPresaleWkPrice($cartInfo);
//        $vipPrice = self::getOrderSumPrice($cartInfo,'vip_truePrice');//获取订单会员优惠金额

        //获取自营 产品 ，商铺产品的  各个邮费merPostage，总邮费storePostage
        $merPostage = [];
        $tem = [];
        $ziyingPrice = 0;
        foreach ($cartInfo as $cart){
            if ($cart['mer_id'] == 0) $ziyingPrice = bcadd($ziyingPrice,bcmul($cart['cart_num'],$cart['truePrice'],2),2);

            if(!$cart['productInfo']['is_postage']){ //如果不包邮，找出店铺最大的邮费
                $tem[$cart['mer_id']][] = $cart['productInfo']['postage'];
//                    $storePostage = bcadd($storePostage,$cart['productInfo']['postage'],2);
            }
        }
        if (!empty($tem)){
            foreach ($tem as $k=>$v){
                $merPostage[$k] = max($v);
            }
            if (isset($merPostage[0]) && ($storeFreePostage <= $ziyingPrice || !$storeFreePostage)) $merPostage[0] = 0;
            $storePostage = array_sum($merPostage);
        }

//        $merPostage = [];
//        if(!$storeFreePostage) {
//            $storePostage = 0;
//        }else{
//            $tem = [];
//            foreach ($cartInfo as $cart){
//                if(!$cart['productInfo']['is_postage']){ //如果不包邮，找出店铺最大的邮费
//                    $tem[$cart['mer_id']][] = $cart['productInfo']['postage'];
////                    $storePostage = bcadd($storePostage,$cart['productInfo']['postage'],2);
//                }
//            }
//            if (!empty($tem)){
//                foreach ($tem as $k=>$v){
//                    $merPostage[$k] = max($v);
//                }
//            }
//
//            if($storeFreePostage <= $totalPrice) $storePostage = 0;
//        }
        return compact('storePostage','storeFreePostage','totalPrice','costPrice','vipPrice','presaleWkPrice','merPostage','merTotalPrice');
    }

    /**
     * 获取订单总额
     * @param $cartInfo
     * @return int|string
     * @author: 
     * @Time: 2020/5/30 18:12
     */
    public static function getOrderTotalPrice($cartInfo)
    {
        $totalPrice = 0;
        foreach ($cartInfo as $cart){
            $totalPrice = bcadd($totalPrice,bcmul($cart['cart_num'],$cart['truePrice'],2),2);
        }
        return $totalPrice;
    }

    /**
     * 获取每个商户订单总额
     * @param $cartInfo
     * @return int|string
     * @author: gyz
     * @Time: 2020/6/9 13:09
     */
    public static function getOrderTotalPriceEveryMer($cartInfo)
    {
        $totalPrice = [];
        foreach ($cartInfo as $cart){
            $totalPrice[$cart['mer_id']] = isset($totalPrice[$cart['mer_id']]) ? $totalPrice[$cart['mer_id']] : 0;
            $totalPrice[$cart['mer_id']] = bcadd($totalPrice[$cart['mer_id']],bcmul($cart['cart_num'],$cart['truePrice'],2),2);
        }
        return $totalPrice;
    }
    public static function getOrderCostPrice($cartInfo)
    {
        $costPrice=0;
        foreach ($cartInfo as $cart){
            $costPrice = bcadd($costPrice,bcmul($cart['cart_num'],$cart['costPrice'],2),2);
        }
        return $costPrice;
    }

    /**
     * 获取订单 预售尾款金额
     * @param $cartInfo
     * @return int|string
     * @author: gyz
     * @Time: 2020/6/1 10:23
     */
    public static function getOrderPresaleWkPrice($cartInfo)
    {
        $presaleWkPrice = 0;
        foreach ($cartInfo as $cart){
            $presaleWkPrice = bcadd($presaleWkPrice,bcmul($cart['cart_num'],$cart['presaleWkPrice'],2),2);
        }
        return $presaleWkPrice;
    }

    /**获取某个字段总金额
     * @param $cartInfo
     * @param $key 键名
     * @return int|string
     */
    public static function getOrderSumPrice($cartInfo,$key='vip_truePrice')
    {
        $SumPrice = 0;
        foreach ($cartInfo as $cart){
            $SumPrice = bcadd($SumPrice,bcmul($cart['cart_num'],$cart[$key],2),2);
        }
        return $SumPrice;
    }




    /**
     * 拼团
     * @param $cartInfo
     * @return array
     */
    public static function getCombinationOrderPriceGroup($cartInfo)
    {
        $storePostage = floatval(SystemConfigService::get('store_postage'))?:0;
        $storeFreePostage =  floatval(SystemConfigService::get('store_free_postage'))?:0;
        $totalPrice = self::getCombinationOrderTotalPrice($cartInfo);
        $costPrice = self::getCombinationOrderCostPrice($cartInfo);
        if(!$storeFreePostage) {
            $storePostage = 0;
        }else{
            foreach ($cartInfo as $cart){
                if(!StoreCombination::where('id',$cart['combination_id'])->value('is_postage'))
                    $storePostage = bcadd($storePostage,StoreCombination::where('id',$cart['combination_id'])->value('postage'),2);
            }
            if($storeFreePostage <= $totalPrice) $storePostage = 0;
        }
        return compact('storePostage','storeFreePostage','totalPrice','costPrice');
    }


    /**
     * 拼团价格
     * @param $cartInfo
     * @return float
     */
    public static function getCombinationOrderTotalPrice($cartInfo)
    {
        $totalPrice = 0;
        foreach ($cartInfo as $cart){
            if($cart['combination_id']){
                 $totalPrice = bcadd($totalPrice,bcmul($cart['cart_num'],StoreCombination::where('id',$cart['combination_id'])->value('price'),2),2);
            }
        }
        return (float)$totalPrice;
    }
    public static function getCombinationOrderCostPrice($cartInfo)
    {
        $costPrice = 0;
        foreach ($cartInfo as $cart){
            if($cart['combination_id']){
                $totalPrice = bcadd($costPrice,bcmul($cart['cart_num'],StoreCombination::where('id',$cart['combination_id'])->value('price'),2),2);
            }
        }
        return (float)$costPrice;
    }


    public static function cacheOrderInfo($uid,$cartInfo,$priceGroup,$other = [],$cacheTime = 600)
    {
        $key = md5(time());
        Cache::set('user_order_'.$uid.$key,compact('cartInfo','priceGroup','other'),$cacheTime);
        return $key;
    }

    public static function getCacheOrderInfo($uid,$key)
    {
        $cacheName = 'user_order_'.$uid.$key;
        if(!Cache::has($cacheName)) return null;
        return Cache::get($cacheName);
    }

    public static function clearCacheOrderInfo($uid,$key)
    {
        Cache::clear('user_order_'.$uid.$key);
    }

    /**
     *
     * @param $uid
     * @param $key
     * @param $addressId
     * @param $payType
     * @param bool $useIntegral
     * @param int $couponId
     * @param string $mark
     * @param int $combinationId
     * @param int $pinkId
     * @param int $seckill_id
     * @param int $bargainId
     * @param int $presale_id
     * @return bool|object
     * @author: gyz
     * @Time: 2020/6/9 10:56
     */
    public static function cacheKeyCreateOrder($uid,$key,$addressId,$payType,$useIntegral = false,$couponId = 0,$mark = '',$combinationId = 0,$pinkId = 0,$seckill_id=0,$bargainId = 0,$presale_id=0)
    {
        if(!array_key_exists($payType,self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        if(self::be(['unique'=>$key,'uid'=>$uid])) return self::setErrorInfo('请勿重复提交订单');
        $userInfo = User::getUserInfo($uid);
        if(!$userInfo) return  self::setErrorInfo('用户不存在!');
        $cartGroup = self::getCacheOrderInfo($uid,$key);
//        dump($cartGroup);die;
        if(!$cartGroup) return self::setErrorInfo('订单已过期,请刷新当前页面!');
        $cartInfo = $cartGroup['cartInfo'];
        $priceGroup = $cartGroup['priceGroup'];
        $other = $cartGroup['other'];
        $payPrice = (float)bcsub($priceGroup['totalPrice'],$priceGroup['presaleWkPrice'],2);
        $payPostage = $priceGroup['storePostage'];
        if(!$addressId) return self::setErrorInfo('请选择收货地址!');
        $addressInfo = UserAddress::where(['uid'=>$uid,'id'=>$addressId,'is_del'=>0])->find();
        if(empty($addressInfo)) return self::setErrorInfo('地址选择有误!');

        $mer_info = []; //每个商户的个性信息数组 用于拆弹

        //保存到数据库 为了 退款显示商品名称
        $mer_id = $cartInfo[0]['mer_id'];
        $store_names = '';
        foreach ($cartInfo as $k=>$v){
            $store_names .= $v['productInfo']['store_name'];
            $mer_id = $mer_id == $v['mer_id'] ? $mer_id : -1;  //-1多商户
        }
        $store_names = mb_substr($store_names,0,60).'...';
        //保存到数据库 为了 退款显示商品名称

        //使用优惠劵
        $res1 = true;
        if($couponId){
            $couponInfo = StoreCouponUser::validAddressWhere()->where('id',$couponId)->where('uid',$uid)->find();
            if(!$couponInfo) return self::setErrorInfo('选择的优惠劵无效!');
            if($couponInfo['use_min_price'] > $payPrice) return self::setErrorInfo("此优惠券需要花费{$couponInfo['use_min_price']}元以上才可使用！");

            //查找此优惠券对应的 最高价的 产品的 单价，对单价进行操作。一张优惠券只适用于单个商品。
//            if (!empty($couponInfo['coupon_products'])){
//
//            }
            $product_price_arr = [];
            $product_use_coupon_id_arr = explode(',',$couponInfo['coupon_products']); //能使用此优惠券的商品id
//            dump(empty($couponInfo['coupon_products']));die;
            foreach ($cartInfo as $k=>$v){
                if ($v['mer_id'] != $couponInfo['mer_id']) continue; //如果不是自己商铺的 就跳过

                if (!empty($couponInfo['coupon_products'])){
                    if (in_array($v['product_id'],$product_use_coupon_id_arr)){
                        $product_price_arr[$v['product_id']] = $v['truePrice'];
                    }
                }else{
                    $product_price_arr[$v['product_id']] = $v['truePrice'];
                }
            }

            $single_price = max($product_price_arr);

            if ($couponInfo['coupon_type'] == 1 && $couponInfo['coupon_price'] > 0){
                $deco_last_price = (float)bcsub($single_price,$couponInfo['coupon_price'],2); //抵扣后剩余的钱；
                $deco_last_price = $deco_last_price>0 ? $deco_last_price : 0;
                $deco_price = (float)bcsub($single_price,$deco_last_price,2); //抵扣掉的钱

                $payPrice = (float)bcsub($payPrice,$deco_price,2);
//                $payPrice = (float)bcsub($payPrice,$couponInfo['coupon_price'],2);
                $couponPrice = $deco_price;
//                $couponPrice = $couponInfo['coupon_price'];
            }elseif ($couponInfo['coupon_type'] == 2 && $couponInfo['coupon_discount'] >= 0 && $couponInfo['coupon_discount'] <= 100){
//                dump($payPrice);
                $deco_last_price = (float)bcmul($single_price,$couponInfo['coupon_discount'],2); //抵扣后剩余的钱；
                $deco_last_price = $deco_last_price>0 ? $deco_last_price : 0;
                $deco_price = (float)bcsub($single_price,$deco_last_price,2);//抵扣掉的钱

                $payPrice = (float)bcsub($payPrice,$deco_price,2);
//                $payPrice = (float)bcmul($payPrice_old,$couponInfo['coupon_discount']/100,2);
                $couponPrice = $deco_price;
            }
            $mer_info[$couponInfo['mer_id']]['coupon_id'] = $couponId;
            $mer_info[$couponInfo['mer_id']]['coupon_price'] = $couponPrice;
            $res1 = StoreCouponUser::useCoupon($couponId);
        }else{
            $couponId = 0;
            $couponPrice = 0;
        }
        if(!$res1) return self::setErrorInfo('使用优惠劵失败!');


        //分析检查各个产品 用于接下来逻辑的拆分订单
        $cartIds = [];
        $totalNum = 0;
        $gainIntegral = 0;
        foreach ($cartInfo as $cart){
            $cartIds[] = $cart['id'];
            $totalNum += $cart['cart_num'];
            $gainIntegral = bcadd($gainIntegral,$cart['productInfo']['give_integral'],2);

            $temmt = isset($mer_info[$cart['mer_id']]['total_num']) ? $mer_info[$cart['mer_id']]['total_num'] : 0;
            $mer_info[$cart['mer_id']]['total_num'] = $temmt + $cart['cart_num'];
            $mer_info[$cart['mer_id']]['cart_id'][] = $cart['id'];

            $mer_info[$cart['mer_id']]['total_price'] = $priceGroup['merTotalPrice'][$cart['mer_id']];  //总价
        }

        if (!empty($priceGroup['merPostage'])){
            foreach ($priceGroup['merPostage'] as $k=>$v){
                $mer_info[$k]['pay_postage'] = $v;
            }
        }

        //积分抵扣 如果不是自营产品 就不能使用积分，
        $res2 = true;
        if($useIntegral && $userInfo['integral'] > 0){
            $deductionPrice = bcmul($userInfo['integral'],$other['integralRatio'],2);
            if($deductionPrice < $payPrice){
                $payPrice = bcsub($payPrice,$deductionPrice,2);
                $usedIntegral = $userInfo['integral'];
                $res2 = false !== User::edit(['integral'=>0],$userInfo['uid'],'uid');
            }else{
                $deductionPrice = $payPrice;
                $usedIntegral = bcdiv($payPrice,$other['integralRatio'],2);
                $res2 = false !== User::bcDec($userInfo['uid'],'integral',$usedIntegral,'uid');
                $payPrice = 0;
            }
            $res2 = $res2 && false != UserBill::expend('积分抵扣',$uid,'integral','deduction',$usedIntegral,$key,bcsub($userInfo['integral'],$usedIntegral,2),'购买商品使用'.floatval($usedIntegral).'积分抵扣'.floatval($deductionPrice).'元');
        }else{
            $deductionPrice = 0;
            $usedIntegral = 0;
        }
        if(!$res2) return self::setErrorInfo('使用积分抵扣失败!');

        //是否包邮 积分不抵邮费
        if((isset($other['offlinePostage'])  && $other['offlinePostage'] && $payType == 'offline')) $payPostage = 0;
        $payPrice = bcadd($payPrice,$payPostage,2);


        $newOrderId = self::getNewOrderId(); //普通订单号
        $newPresaleOrderId = str_replace('wx','ps',$newOrderId); //预售订单号
        $orderInfo = [
            'uid'=>$uid,
            'order_id'=>$newOrderId,
            'real_name'=>$addressInfo['real_name'],
            'user_phone'=>$addressInfo['phone'],
            'user_address'=>$addressInfo['province'].' '.$addressInfo['city'].' '.$addressInfo['district'].' '.$addressInfo['detail'],
            'cart_id'=>$cartIds,
            'total_num'=>$totalNum,
            'total_price'=>$priceGroup['totalPrice'],
            'total_postage'=>$priceGroup['storePostage'],
            'coupon_id'=>$couponId,
            'coupon_price'=>$couponPrice,
            'pay_price'=>$payPrice,
            'pay_postage'=>$payPostage,
            'deduction_price'=>$deductionPrice,
            'paid'=>0,
            'pay_type'=>$payType,
            'use_integral'=>$usedIntegral,
            'gain_integral'=>$gainIntegral,
            'mark'=>htmlspecialchars($mark),
            'combination_id'=>$combinationId,
            'pink_id'=>$pinkId,
            'seckill_id'=>$seckill_id,
            'bargain_id'=>$bargainId,
            'cost'=>$priceGroup['costPrice'],
            'unique'=>$key,
            'store_names'=>$store_names,
            'presale_id'=>$presale_id,
            'presale_order_id'=>$newPresaleOrderId,
            'presale_paid'=>0,
            'presale_pay_price'=>$priceGroup['presaleWkPrice'],
            'mer_id' => $mer_id,
            'mer_info' => json_encode($mer_info),
        ];
//        dump($orderInfo);die;
        $order = self::set($orderInfo);
        if(!$order)return self::setErrorInfo('订单生成失败!');
        $res5 = true;
        foreach ($cartInfo as $cart){
            //减库存加销量
            if($combinationId) $res5 = $res5 && StoreCombination::decCombinationStock($cart['cart_num'],$combinationId);
            else if($seckill_id) $res5 = $res5 && StoreSeckill::decSeckillStock($cart['cart_num'],$seckill_id);
            else if($bargainId) $res5 = $res5 && StoreBargain::decBargainStock($cart['cart_num'],$bargainId);
            else $res5 = $res5 && StoreProduct::decProductStock($cart['cart_num'],$cart['productInfo']['id'],isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique']:'');

         }
        //保存购物车商品信息
        $res4 = false !== StoreOrderCartInfo::setCartInfo($order['id'],$cartInfo);
        //购物车状态修改
        $res6 = false !== StoreCart::where('id','IN',$cartIds)->update(['is_pay'=>1]);
        if(!$res4 || !$res5 || !$res6) return self::setErrorInfo('订单生成失败!');
        try{
            HookService::listen('store_product_order_create',$order,compact('cartInfo','addressId'),false,StoreProductBehavior::class);
        }catch (\Exception $e){
            return self::setErrorInfo($e->getMessage());
        }
        self::clearCacheOrderInfo($uid,$key);
        self::commitTrans();
        StoreOrderStatus::status($order['id'],'cache_key_create_order','订单生成');
        return $order;
    }

    public static function getNewOrderId()
    {
        $count = (int) self::where('add_time',['>=',strtotime(date("Y-m-d"))],['<',strtotime(date("Y-m-d",strtotime('+1 day')))])->count();
        return 'wx'.date('YmdHis',time()).(10000+$count+1);
    }

    public static function changeOrderId($orderId)
    {
        $ymd = substr($orderId,2,8);
        $key = substr($orderId,16);
        return 'wx'.$ymd.date('His').$key;
    }

    public static function jsPay($orderId,$field = 'order_id')
    {
        if(is_string($orderId))
            $orderInfo = self::where($field,$orderId)->find();
        else
            $orderInfo = $orderId;
        if(!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if($orderInfo['paid']) exception('支付已支付!');
        if($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        //为了获取商品名称
        $store_name = $orderInfo['store_names'];
        return WechatService::jsPay($openid,$orderInfo['order_id'],$orderInfo['pay_price'],'productgzh',$store_name);
    }

    /**
     * 预售的第二次支付
     * @param $orderId
     * @param string $field
     * @return array|string
     * @author: gyz
     * @Time: 2020/5/30 9:44
     */
    public static function jsPayPresale($orderId,$field = 'order_id')
    {
        if(is_string($orderId))
            $orderInfo = self::where($field,$orderId)->find();
        else
            $orderInfo = $orderId;
        if(!$orderInfo || !isset($orderInfo['presale_paid'])) exception('支付订单不存在!');
        if($orderInfo['presale_paid']) exception('支付已支付!');
        if($orderInfo['presale_pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        //为了获取商品名称
        $store_name = $orderInfo['store_names'];
        return WechatService::jsPay($openid,$orderInfo['presale_order_id'],$orderInfo['presale_pay_price'],'productgzh',$store_name);
    }

    public static function jsPayold($orderId,$field = 'order_id')
    {
        if(is_string($orderId))
            $orderInfo = self::where($field,$orderId)->find();
        else
            $orderInfo = $orderId;
        if(!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if($orderInfo['paid']) exception('支付已支付!');
        if($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid,$orderInfo['order_id'],$orderInfo['pay_price'],'productgzh',SystemConfigService::get('site_name'));
    }

    public static function yuePay($order_id,$uid)
    {
        $orderInfo = self::where('uid',$uid)->where('order_id',$order_id)->where('is_del',0)->find();
        if(!$orderInfo) return self::setErrorInfo('订单不存在!');
        if($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
//        if($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserInfo($uid);
        if($userInfo['now_money'] < $orderInfo['pay_price'])
            return self::setErrorInfo('余额不足'.floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid,'now_money',$orderInfo['pay_price'],'uid');
        $res2 = UserBill::expend('购买商品',$uid,'now_money','pay_product',$orderInfo['pay_price'],$orderInfo['id'],bcsub($userInfo['now_money'],$orderInfo['pay_price'],2),'余额支付'.floatval($orderInfo['pay_price']).'元购买商品');
        $res3 = self::paySuccess($order_id,'yue');
        try{
            HookService::listen('yue_pay_product',$userInfo,$orderInfo,false,PaymentBehavior::class);
        }catch (\Exception $e){
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 余额支付预售的第二次支付
     * @param $order_id
     * @param $uid
     * @return bool
     * @author: gyz
     * @Time: 2020/5/30 9:45
     */
    public static function yuePayPresale($order_id,$uid)
    {
        $orderInfo = self::where('uid',$uid)->where('order_id',$order_id)->where('is_del',0)->find();
        if(!$orderInfo) return self::setErrorInfo('订单不存在!');
        if($orderInfo['presale_paid']) return self::setErrorInfo('该订单已支付!');
//        if($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserInfo($uid);

        if($userInfo['now_money'] < $orderInfo['presale_pay_price'])
            return self::setErrorInfo('余额不足'.floatval($orderInfo['presale_pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid,'now_money',$orderInfo['presale_pay_price'],'uid');
        $res2 = UserBill::expend('购买商品',$uid,'now_money','pay_product',$orderInfo['presale_pay_price'],$orderInfo['id'],bcsub($userInfo['now_money'],$orderInfo['presale_pay_price'],2),'余额支付尾款'.floatval($orderInfo['presale_pay_price']).'元购买商品');
        $res3 = self::paySuccess($orderInfo['presale_order_id'],'yue');
        try{
            HookService::listen('yue_pay_product',$userInfo,$orderInfo,false,PaymentBehavior::class);
        }catch (\Exception $e){
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 微信支付 为 0元时 预售尾款不会为0
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayPrice($order_id,$uid){
        $orderInfo = self::where('uid',$uid)->where('order_id',$order_id)->where('is_del',0)->find();
        if(!$orderInfo) return self::setErrorInfo('订单不存在!');
        if($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserInfo($uid);
        self::beginTrans();
        $res1 = UserBill::expend('购买商品',$uid,'now_money','pay_product',$orderInfo['pay_price'],$orderInfo['id'],$userInfo['now_money'],'微信支付'.floatval($orderInfo['pay_price']).'元购买商品');
        $res2 = self::paySuccess($order_id,'weixin');
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    public static function yueRefundAfter($order)
    {

    }

    /**
     * 用户申请退款
     * @param $uni
     * @param $uid
     * @param string $refundReasongzh
     * @return bool
     */
    public static function orderApplyRefund($uni, $uid,$refundReasonWap = '',$refundReasonWapExplain = '',$refundReasonWapImg = array())
    {
        $order = self::getUserOrderDetail($uid,$uni);
        if(!$order) return self::setErrorInfo('支付订单不存在!');
        if($order['refund_status'] == 2) return self::setErrorInfo('订单已退款!');
        if($order['refund_status'] == 1) return self::setErrorInfo('正在申请退款中!');
        if($order['status'] == 1) return self::setErrorInfo('订单当前无法退款!');
        self::beginTrans();
        $res1 = false !== StoreOrderStatus::status($order['id'],'apply_refund','用户申请退款，原因：'.$refundReasonWap);
        $res2 = false !== self::edit(['refund_status'=>1,'refund_reason_wap'=>$refundReasonWap],$order['id'],'id');
        $res = $res1 && $res2;
        self::checkTrans($res);
        if(!$res)
            return self::setErrorInfo('申请退款失败!');
        else{
            try{
                HookService::afterListen('store_product_order_apply_refund',$order['id'],$uid,false,StoreProductBehavior::class);
            }catch (\Exception $e){}
            return true;
        }
    }

    /**
     * 支付成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function paySuccess($orderId,$paytype='weixin')
    {
//        dump($orderId);die;
        if (strpos($orderId,'ps') === false){ //找不到预售就是普通的
            $order = self::where('order_id',$orderId)->find();
//            dump($order);die;
            $now = time();

            $res1 = self::where('order_id',$orderId)->update(['paid'=>1,'pay_type'=>$paytype,'pay_time'=>$now]);//订单改为支付
            $cartInfo = self::getDb('StoreOrderCartInfo')->where('oid', $order['id'])->column('cart_info', 'unique') ?: [];
            foreach ($cartInfo as $k => &$cart) $cart = json_decode($cart, true);
//            $res2 = true;
//            foreach ($cartInfo as $k => &$cart) {  //减库存加销量 创建订单时已经减库存
//                if ($cart['combination_id']) $res2 = $res2 && StoreCombination::decCombinationStock($cart['cart_num'], $cart['combination_id']);
//                else if ($cart['seckill_id']) $res2 = $res2 && StoreSeckill::decSeckillStock($cart['cart_num'], $cart['seckill_id']);
//                else if ($cart['bargain_id']) $res2 = $res2 && StoreBargain::decBargainStock($cart['cart_num'], $cart['bargain_id']);
//                else $res2 = $res2 && StoreProduct::decProductStock($cart['cart_num'], $cart['productInfo']['id'], isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
//            }
            User::bcInc($order['uid'],'pay_count',1,'uid');
            $resPink = true;
            if($order->combination_id && $res1 && !$order->refund_status) $resPink = StorePink::createPink($order);//创建拼团
            $oid = self::where('order_id',$orderId)->value('id');
            StoreOrderStatus::status($oid,'pay_success','用户付款成功');
            HookService::afterListen('user_level',User::where('uid',$order['uid'])->find(),false,UserBehavior::class);
            $res = $res1 && $resPink;

            //判断是不是需要拆分订单
            $order = $order->toArray();
            if ($order['mer_id'] == -1){ //需要拆分
                $indata = [];
                $mer_info = json_decode($order['mer_info'],true);
                //拼装订单数组 批量插入
                foreach ($mer_info as $k=>$v){
//                    dump(isset($v['cart_id']));
//                    dump($v['cart_id']);
                    $temorder = $order;
                    unset($temorder['id']);
                    if(isset($v['total_price']) && isset($v['coupon_price'])){
                        $temorder['pay_price'] = bcsub($v['total_price'],$v['coupon_price'],2);
                    }else{
                        $temorder['pay_price'] = $v['total_price'];
                    }
                    $temorder['mer_id'] = $k;
                    $temorder['order_id'] = $order['order_id'].'_'.$k;
                    $temorder['order_id_father'] = $order['order_id'];
                    $temorder['unique'] = $order['unique'].'_'.$k;
                    $temorder['cart_id'] = isset($v['cart_id']) ? json_encode($v['cart_id']) : [];
                    $temorder['total_num'] = isset($v['total_num']) ? $v['total_num'] : 0;
                    $temorder['total_price'] = isset($v['total_price']) ? $v['total_price'] : 0;
                    $temorder['total_postage'] = isset($v['pay_postage']) ? $v['pay_postage'] : 0;
                    $temorder['pay_postage'] = isset($v['pay_postage']) ? $v['pay_postage'] : 0;
                    $temorder['coupon_id'] = isset($v['coupon_id']) ? $v['coupon_id'] : 0;
                    $temorder['coupon_price'] = isset($v['coupon_price']) ? $v['coupon_price'] : 0;
                    $temorder['paid'] = 1;
                    $temorder['pay_type'] = $paytype;
                    $temorder['pay_time'] = $now;
                    $indata[] = $temorder;
                }
                $res2 = self::insertAll($indata);
//                echo self::getLastSql();die;
                $res = $res && $res2;
            }

            return false !== $res;

//        WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($order['uid']),WechatTemplateService::ORDER_PAY_SUCCESS, [
//            'first'=>'亲，您购买的商品已支付成功',
//            'keyword1'=>$orderId,
//            'keyword2'=>$order['pay_price'],
//            'remark'=>'点击查看订单详情'
//        ],Url::build('gzh/My/order',['uni'=>$orderId],true,true));
//        WechatTemplateService::sendAdminNoticeTemplate([
//            'first'=>"亲,您有一个新订单 \n订单号:{$order['order_id']}",
//            'keyword1'=>'新订单',
//            'keyword2'=>'已支付',
//            'keyword3'=>date('Y/m/d H:i',time()),
//            'remark'=>'请及时处理'
//        ]);
        }else{ //预售订单
//            $oid = self::where('presale_order_id',$orderId)->value('id');
            $oinfo = self::where('presale_order_id',$orderId)->find();
            $oid = $oinfo['id'];
            $pay_price = (float)bcadd($oinfo['pay_price'],$oinfo['presale_pay_price'],2);
            $res1 = self::where('presale_order_id',$orderId)->update(['presale_paid'=>1,'pay_price'=>$pay_price,'presale_pay_type'=>$paytype,'presale_pay_time'=>time()]);
            StoreOrderStatus::status($oid,'pay_success','用户尾款支付成功');
            $res = $res1;
            return false !== $res;
        }
    }

    public static function createOrderTemplate($order)
    {
        $goodsName = StoreOrderCartInfo::getProductNameList($order['id']);
        WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($order['uid']),WechatTemplateService::ORDER_CREATE, [
            'first'=>'亲，您购买的商品已支付成功',
            'keyword1'=>date('Y/m/d H:i',$order['add_time']),
            'keyword2'=>implode(',',$goodsName),
            'keyword3'=>$order['order_id'],
            'remark'=>'点击查看订单详情'
        ],Url::build('/gzh/My/order',['uni'=>$order['order_id']],true,true));
        WechatTemplateService::sendAdminNoticeTemplate([
            'first'=>"亲,您有一个新订单 \n订单号:{$order['order_id']}",
            'keyword1'=>'新订单',
            'keyword2'=>'线下支付',
            'keyword3'=>date('Y/m/d H:i',time()),
            'remark'=>'请及时处理'
        ]);
    }

    public static function getUserOrderDetail($uid,$key)
    {
        return self::where('order_id|unique',$key)->where('uid',$uid)->where('is_del',0)->find();
    }


    /**
     * 订单发货
     * @param array $postageData 发货信息
     * @param string $oid orderID
     */
    public static function orderPostageAfter($postageData, $oid)
    {
        $order = self::where('id',$oid)->find();
        $openid = WechatUser::uidToOpenid($order['uid']);
        $url = Url::build('gzh/My/order',['uni'=>$order['order_id']],true,true);
        $group = [
            'first'=>'亲,您的订单已发货,请注意查收',
            'remark'=>'点击查看订单详情'
        ];
        if($postageData['delivery_type'] == 'send'){//送货
            $goodsName = StoreOrderCartInfo::getProductNameList($order['id']);
            $group = array_merge($group,[
                'keyword1'=>$goodsName,
                'keyword2'=>$order['pay_type'] == 'offline' ? '线下支付' : date('Y/m/d H:i',$order['pay_time']),
                'keyword3'=>$order['user_address'],
                'keyword4'=>$postageData['delivery_name'],
                'keyword5'=>$postageData['delivery_id']
            ]);
            WechatTemplateService::sendTemplate($openid,WechatTemplateService::ORDER_DELIVER_SUCCESS,$group,$url);

        }else if($postageData['delivery_type'] == 'express'){//发货
            $group = array_merge($group,[
                'keyword1'=>$order['order_id'],
                'keyword2'=>$postageData['delivery_name'],
                'keyword3'=>$postageData['delivery_id']
            ]);
            WechatTemplateService::sendTemplate($openid,WechatTemplateService::ORDER_POSTAGE_SUCCESS,$group,$url);
        }
    }

    public static function orderTakeAfter($order)
    {
        $openid = WechatUser::uidToOpenid($order['uid']);
        WechatTemplateService::sendTemplate($openid,WechatTemplateService::ORDER_TAKE_SUCCESS,[
            'first'=>'亲，您的订单已成功签收，快去评价一下吧',
            'keyword1'=>$order['order_id'],
            'keyword2'=>'已收货',
            'keyword3'=>date('Y/m/d H:i',time()),
            'keyword4'=>implode(',',StoreOrderCartInfo::getProductNameList($order['id'])),
            'remark'=>'点击查看订单详情'
        ],Url::build('My/order',['uni'=>$order['order_id']],true,true));
    }

    /**
     * 删除订单
     * @param $uni
     * @param $uid
     * @return bool
     */
    public static function removeOrder($uni, $uid)
    {
        $order = self::getUserOrderDetail($uid,$uni);
        if(!$order) return self::setErrorInfo('订单不存在!');
        $order = self::tidyOrder($order);
        if($order['_status']['_type'] != 0 && $order['_status']['_type']!= -2 && $order['_status']['_type'] != 4)
            return self::setErrorInfo('该订单无法删除!');
        if(false !== self::edit(['is_del'=>1],$order['id'],'id') &&
            false !==StoreOrderStatus::status($order['id'],'remove_order','删除订单'))
            return true;
        else
            return self::setErrorInfo('订单删除失败!');
    }


    /**
     *  用户确认收货
     * @param $uni
     * @param $uid
     */
    public static function takeOrder($uni, $uid)
    {
        $order = self::getUserOrderDetail($uid,$uni);
        if(!$order) return self::setErrorInfo('订单不存在!');
        $order = self::tidyOrder($order);
        if($order['_status']['_type'] != 2)  return self::setErrorInfo('订单状态错误!');
        self::beginTrans();
        if(false !== self::edit(['status'=>2],$order['id'],'id') &&
            false !== StoreOrderStatus::status($order['id'],'user_take_delivery','用户已收货')){
            try{
                HookService::listen('store_product_order_user_take_delivery',$order,$uid,false,StoreProductBehavior::class);
            }catch (\Exception $e){
                return self::setErrorInfo($e->getMessage());
            }
            self::commitTrans();
            return true;
        }else{
            self::rollbackTrans();
            return false;
        }
    }

    public static function tidyOrder($order,$detail = false)
    {
        if($detail == true && isset($order['id'])){
            if ($order['presale_id']){
                $presaleinfo = Db::name('store_presale')->where('id',  $order['presale_id'])->find();
                $order['weikuan'] = (float)bcmul($order['total_num'],($presaleinfo['pre_price']-$presaleinfo['price']),2);
                $order['dingjin'] = (float)bcmul($order['total_num'],$presaleinfo['price'],2);
            }
            if (empty($order['order_id_father'])){
                $cartInfo = self::getDb('StoreOrderCartInfo')->where('oid',$order['id'])->column('cart_info','unique')?:[];
            }else{
                $cids = $order['cart_id'];
                $cartInfo = self::getDb('StoreOrderCartInfo')->whereIn('cart_id',$cids)->column('cart_info','unique')?:[];
            }
//            foreach ($cartInfo as $k=>$cart){
//                $cartInfo[$k] = json_decode($cart, true);
//                $cartInfo[$k]['unique'] = $k;
//            }
//            $order['cartInfo'] = $cartInfo;
            $info=[];
            foreach ($cartInfo as $k=>$cart){
                $cart=json_decode($cart, true);
                $cart['unique']=$k;
                //新增是否评价字段
//                $cart['is_reply'] = self::getDb('store_product_reply')->where('unique',$k)->count();
                array_push($info,$cart);
                unset($cart);
            }
            $order['cartInfo'] = $info;
        }

        $status = [];
        if(!$order['paid'] && $order['pay_type'] == 'offline' && !$order['status'] >= 2){
            $status['_type'] = 9;
            $status['_title'] = '线下付款';
            $status['_msg'] = '等待商家处理,请耐心等待';
            $status['_class'] = 'nobuy';
        }else if(!$order['paid']){
            $status['_type'] = 0;
            $status['_title'] = '未支付';
            $status['_msg'] = '立即支付订单吧';
            $status['_class'] = 'nobuy';
        }else if($order['paid'] && !$order['presale_paid'] && $order['presale_id']){
            $status['_type'] = 0;
            $status['_title'] = '未支付尾款';
            $status['_msg'] = '立即支付订单吧';
            $status['_class'] = 'nobuy';
        }else if($order['refund_status'] == 1){
            $status['_type'] = -1;
            $status['_title'] = '申请退款中';
            $status['_msg'] = '商家审核中,请耐心等待';
            $status['_class'] = 'state-sqtk';
        }else if($order['refund_status'] == 2){
            $status['_type'] = -2;
            $status['_title'] = '已退款';
            $status['_msg'] = '已为您退款,感谢您的支持';
            $status['_class'] = 'state-sqtk';
        }else if(!$order['status']){
            if(isset($order['pink_id']) && !empty($order['pink_id'])){

                $whereid = $order['pink_id'];
                $pinfo = StorePink::where('id',$order['pink_id'])->find();
                $pinfo = empty($pinfo) ? [] : $pinfo->toArray();
                if ($pinfo['com_type'] == 1 || $pinfo['com_type']==4){
                    if ($pinfo['k_id']>0) $whereid = $pinfo['k_id'];
                }

                $model = new StorePink();
                $model = $model->alias('p');
                $model = $model->field('p.*,u.nickname,u.avatar');
                $model = $model->where('p.k_id|p.id',$whereid);
//        $model = $model->where('is_refund',0);//个人中心查不到
                $model = $model->join('__USER__ u','u.uid = p.uid');
                $model = $model->order('id asc');
                $list = $model->select();
                $list = empty($list) ? [] : $list->toArray();
                $has_people = count($list);
//        echo $model->getLastSql();die;

                $cominfo = StoreCombination::where('id', $list[0]['cid'])->find();
                if (empty($cominfo)) return false;
                $cominfo = $cominfo->toArray();

                $has_people_last = $cominfo['min_people'] - $has_people;

                $pink_info = [
                    'has_people' => $has_people,
                    'has_people_last' => $has_people_last,
                    'need_people' => $cominfo['min_people'],
//                    'cominfo' => $cominfo,
                    'people_list' => $list,
                ];

                if(StorePink::where('id',$order['pink_id'])->where('status',1)->count()){
                    $status['_type'] = 1;
                    $status['_title'] = '拼团中';
                    $status['_msg'] = '等待其他人参加拼团';
                    $status['_class'] = 'state-nfh';
                }else{
                    $status['_type'] = 1;
                    $status['_title'] = '未发货';
                    $status['_msg'] = '商家未发货,请耐心等待';
                    $status['_class'] = 'state-nfh';
                }
            }else{
                $status['_type'] = 1;
                $status['_title'] = '未发货';
                $status['_msg'] = '商家未发货,请耐心等待';
                $status['_class'] = 'state-nfh';
            }
        }else if($order['status'] == 1){
            $status['_type'] = 2;
            $status['_title'] = '待收货';
            $status['_msg'] = date('m月d日H时i分',StoreOrderStatus::getTime($order['id'],'delivery_goods')).'服务商已发货';
            $status['_class'] = 'state-ysh';
        }else if($order['status'] == 2){
            $status['_type'] = 3;
            $status['_title'] = '待评价';
            $status['_msg'] = '已收货,快去评价一下吧';
            $status['_class'] = 'state-ypj';
        }else if($order['status'] == 3){
            $status['_type'] = 4;
            $status['_title'] = '交易完成';
            $status['_msg'] = '交易完成,感谢您的支持';
            $status['_class'] = 'state-ytk';
        }
        if(isset($order['pay_type']))
            $status['_payType'] = isset(self::$payType[$order['pay_type']]) ? self::$payType[$order['pay_type']] : '其他方式';
        if(isset($order['delivery_type']))
            $status['_deliveryType'] = isset(self::$deliveryType[$order['delivery_type']]) ? self::$deliveryType[$order['delivery_type']] : '其他方式';
        $order['_status'] = $status;
        $order['_pink_info'] = isset($pink_info) ? $pink_info : [];
//        dump($order);die;
        $order['_pay_time']=isset($order['pay_time']) && $order['pay_time'] != null ? date('Y-m-d H:i:s',$order['pay_time']) : date('Y-m-d H:i:s',$order['add_time']);
        $order['_add_time']=isset($order['add_time']) ? (strstr($order['add_time'],'-')===false ? date('Y-m-d H:i:s',$order['add_time']) : $order['add_time'] ): '';
        $order['status_pic']='';
        return $order;
    }

    public static function statusByWhere($status,$uid=0,$model = null)
    {
//        $orderId = StorePink::where('uid',$uid)->where('status',1)->column('order_id','id');//获取正在拼团的订单编号
        if($model == null) $model = new self;
        if('' === $status)
            return $model;
        else if($status == 0)
//            return $model->where('paid',0)->where('status',0)->where('refund_status',0);
            return $model->where('(paid=0 and status=0 and refund_status=0) or (paid=1 and status=0 and refund_status=0 and presale_id>0 and presale_paid=0)');
        else if($status == 1)//待发货
//            return $model->where('paid',1)->where('status',0)->where('refund_status',0);
            return $model->where('(paid=1 and presale_id=0 and status=0 and refund_status=0 and mer_id>=0) or (paid=1 and presale_id>0 and presale_paid=1 and status=0 and refund_status=0 and mer_id>=0) ');
        else if($status == 2)
            return $model->where('paid',1)->where('status',1)->where('refund_status',0)->where('mer_id','>=',0);
        else if($status == 3)
            return $model->where('paid',1)->where('status',2)->where('refund_status',0)->where('mer_id','>=',0);
        else if($status == 4)
            return $model->where('paid',1)->where('status',3)->where('refund_status',0)->where('mer_id','>=',0);
        else if($status == -1)
            return $model->where('paid',1)->where('refund_status',1)->where('mer_id','>=',0);
        else if($status == -2)
            return $model->where('paid',1)->where('refund_status',2)->where('mer_id','>=',0);
        else if($status == -3)
            return $model->where('paid',1)->where('refund_status','IN','1,2')->where('mer_id','>=',0);
//        else if($status == 11){
//            return $model->where('order_id','IN',implode(',',$orderId));
//        }
        else
            return $model;
    }

    public static function getUserOrderList($uid,$status = '',$first = 1,$limit = 10)
    {
        $list = self::statusByWhere($status)->where('is_del',0)->where('uid',$uid)
//            ->field('combination_id,id,order_id,pay_price,total_num,total_price,pay_postage,total_postage,paid,status,refund_status,pay_type,coupon_price,deduction_price,pink_id,delivery_type,add_time')
            ->order('add_time DESC')->page((int)$first,(int)$limit)->select()->toArray();
        foreach ($list as $k=>$order){
            $list[$k] = self::tidyOrder($order,true);
        }
        return $list;
    }

    public static function searchUserOrder($uid,$order_id)
    {
        $order = self::where('uid',$uid)->where('order_id',$order_id)->where('is_del',0)->field('combination_id,id,order_id,pay_price,total_num,total_price,pay_postage,total_postage,paid,status,refund_status,pay_type,coupon_price,deduction_price,delivery_type')
            ->order('add_time DESC')->find();
        if(!$order)
            return false;
        else
            return self::tidyOrder($order->toArray(),true);

    }

    public static function orderOver($oid)
    {
        $res = self::edit(['status'=>'3'],$oid,'id');
        if(!$res) exception('评价后置操作失败!');
        StoreOrderStatus::status($oid,'check_order_over','用户评价');
    }

    public static function checkOrderOver($oid)
    {
        $uniqueList = StoreOrderCartInfo::where('oid',$oid)->column('unique');
        if(StoreProductReply::where('unique','IN',$uniqueList)->where('oid',$oid)->count() == count($uniqueList)){
            HookService::listen('store_product_order_over',$oid,null,false,StoreProductBehavior::class);
            self::orderOver($oid);
        }
    }


    public static function getOrderStatusNum($uid)
    {
        $noBuy = self::where('uid',$uid)->where('paid',0)->where('is_del',0)->where('pay_type','<>','offline')->where('refund_status',0)->count();
        $noPostageNoPink = self::where('o.uid',$uid)->alias('o')->where('o.paid',1)->where('o.pink_id',0)->where('o.is_del',0)->where('o.status',0)->where('o.pay_type','<>','offline')->where('o.refund_status',0)->count();
        $noPostageYesPink = self::where('o.uid',$uid)->alias('o')->join('StorePink p','o.pink_id = p.id')->where('p.status',2)->where('o.paid',1)->where('o.is_del',0)->where('o.status',0)->where('o.refund_status',0)->where('o.pay_type','<>','offline')->count();
        $noPostage = bcadd($noPostageNoPink,$noPostageYesPink,0);
        $noTake = self::where('uid',$uid)->where('paid',1)->where('is_del',0)->where('status',1)->where('pay_type','<>','offline')->where('refund_status',0)->count();
        $noReply = self::where('uid',$uid)->where('paid',1)->where('is_del',0)->where('status',2)->where('refund_status',0)->count();
        $noPink = self::where('o.uid',$uid)->alias('o')->join('StorePink p','o.pink_id = p.id')->where('p.status',1)->where('o.paid',1)->where('o.is_del',0)->where('o.status',0)->where('o.pay_type','<>','offline')->where('o.refund_status',0)->count();
        return compact('noBuy','noPostage','noTake','noReply','noPink');
    }

    public static function gainUserIntegral($order)
    {
        if($order['gain_integral'] > 0){
            $userInfo = User::getUserInfo($order['uid']);
            ModelBasic::beginTrans();
            $res1 = false != User::where('uid',$userInfo['uid'])->update(['integral'=>bcadd($userInfo['integral'],$order['gain_integral'],2)]);
            $res2 = false != UserBill::income('购买商品赠送积分',$order['uid'],'integral','gain',$order['gain_integral'],$order['id'],bcadd($userInfo['integral'],$order['gain_integral'],2),'购买商品赠送'.floatval($order['gain_integral']).'积分');
            $res = $res1 && $res2;
            ModelBasic::checkTrans($res);
            return $res;
        }
        return true;
    }

    /**
     * 获取当前订单中有没有拼团存在
     * @param $pid
     * @return int|string
     */
    public static function getIsOrderPink($pid){
        $uid = User::getActiveUid();
        return self::where('uid',$uid)->where('pink_id',$pid)->where('refund_status',0)->where('is_del',0)->count();
    }

    /**
     * 获取order_id
     * @param $pid
     * @return mixed
     */
    public static function getStoreIdPink($pid){
        $uid = User::getActiveUid();
        return self::where('uid',$uid)->where('pink_id',$pid)->where('is_del',0)->value('order_id');
    }

    /**
     * 删除当前用户拼团未支付的订单
     */
    public static function delCombination(){
        self::where('combination','GT',0)->where('paid',0)->where('uid',User::getActiveUid())->delete();
    }

    /*
     * 个人中心获取个人订单列表和订单搜索
     * @param int $uid 用户uid
     * @param int | string 查找订单类型
     * @param int $first 分页
     * @param int 每页显示多少条
     * @param string $search 订单号
     * @return array
     * */
    public static function getUserOrderSearchList($uid,$type,$page,$limit,$search)
    {
        if($search){
            $order = self::searchUserOrder($uid,$search)?:[];
            $list = $order == false ? [] : [$order];
        }else{
            $list = self::getUserOrderList($uid,$type,$page,$limit);
        }
        foreach ($list as $k=>$order){
            $list[$k] = self::tidyOrder($order,true);
            if($list[$k]['_status']['_type'] == 3){
                foreach ($order['cartInfo']?:[] as $key=>$product){
                    $list[$k]['cartInfo'][$key]['is_reply'] = StoreProductReply::isReply($product['unique'],'product');
                    $list[$k]['cartInfo'][$key]['add_time'] = date('Y-m-d H:i',$product['add_time']);
                }
            }
        }
        return $list;
    }

    /*
     * 累计消费
     * **/
    public static function getOrderStatusSum($uid)
    {
        return self::where(['uid'=>$uid,'is_del'=>0,'paid'=>1])->sum('pay_price');
    }

    /*
     * 获取某个用户的订单统计数据
     * @param int $uid 用户uid
     * */
    public static function getOrderData($uid)
    {
        $data['order_count']=self::where(['is_del'=>0,'paid'=>1,'uid'=>$uid,'refund_status'=>0])->count();
        $data['sum_price']=self::where(['is_del'=>0,'paid'=>1,'uid'=>$uid,'refund_status'=>0])->sum('pay_price');
        $data['unpaid_count']=self::statusByWhere(0,$uid)->where('is_del',0)->where('uid',$uid)->count();
        $data['unshipped_count']=self::statusByWhere(1,$uid)->where('is_del',0)->where('uid',$uid)->count();
        $data['received_count']=self::statusByWhere(2,$uid)->where('is_del',0)->where('uid',$uid)->count();
        $data['evaluated_count']=self::statusByWhere(3,$uid)->where('is_del',0)->where('uid',$uid)->count();
        $data['complete_count']=self::statusByWhere(4,$uid)->where('is_del',0)->where('uid',$uid)->count();
        return $data;
    }

    /**
     *
     * @param $uid
     * @return int|string
     * @auth:pyp
     * @date:2020/5/9 14:48
     */
    public static function getOrderCount($uid)
    {
        $order_count = self::where('uid',$uid)->where('paid',1)->where('status','in','2,3')->count();
        $total_price = self::where('uid',$uid)->where('paid',1)->where('status','in','2,3')->sum('pay_price');
        return compact('order_count','total_price');
    }

    public static function getUserSpreadOder($uid,$page = 1,$limit = 10)
    {
//        $cartInfo = self::alias('o')
//            ->join('StoreOrderCartInfo c','o.id=c.oid','right')
//            ->where('o.uid',$uid)
//            ->where('o.paid',1)
//            ->where('o.status','in','2,3')
//            ->where('o.is_del',0)
//            ->page((int)$page,(int)$limit)
//            ->column('c.cart_info');
//        foreach ($cartInfo as $k=>$cart){
//            $cartInfo[$k] = json_decode($cart, true);
//        }
//        return $cartInfo;


        $list = self::where('is_del',0)
            ->where('uid',$uid)
            ->where('paid',1)
            ->where('refund_status',0)
            ->field('add_time,seckill_id,bargain_id,combination_id,id,order_id,pay_price,total_num,total_price,pay_postage,total_postage,paid,status,refund_status,pay_type,coupon_price,deduction_price,pink_id,delivery_type')
            ->order('add_time DESC')->page((int)$page,(int)$limit)->select()->toArray();
        foreach ($list as $k=>$order){
            $list[$k] = self::tidyOrder($order,true);
        }

        return $list;
    }



    /*
     * 取消订单
     * @param string order_id 订单id
     * */
    public static function cancelOrder($order_id)
    {
        $order=self::where('order_id',$order_id)->find();
        if(!$order) return self::setErrorInfo('没有查到此订单');
        self::beginTrans();
        try{
            $res=self::RegressionIntegral($order) && self::RegressionCoupon($order);
            if($res){
                $order->is_del=1;
                self::commitTrans();
                return $order->save();
            }
        }catch (\Exception $e){
            self::rollbackTrans();
            return self::setErrorInfo(['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }
    }
    /*
     * 回退积分
     * @param array $order 订单信息
     * @return boolean
     * */
    public static function RegressionIntegral($order)
    {
        if($order['paid'] || $order['status']==-2 || $order['is_del']) return false;
        if($order['use_integral'] <= 0) return true;
        if((int)$order['status']!=-2 && (int)$order['refund_status']!=2 && $order['back_integral'] >= $order['use_integral'])
            return self::setErrorInfo('已退积分或该状态无法回退积分');
        $res=User::bcInc($order['uid'],'integral',$order['use_integral']);
        if(!$res) return self::setErrorInfo('回退积分增加失败');
        UserBill::income('积分回退',$order['uid'],'integral','deduction',$order['use_integral'],$order['unique'],User::where('uid',$order['uid'])->value('integral'),'购买商品失败,回退积分'.floatval($order['use_integral']));
        return self::where('order_id',$order['order_id'])->update(['back_integral'=>$order['use_integral']]);
    }
    /*
     * 回退优惠卷
     * @param array $order 订单信息
     * @return boolean
     * */
    public static function RegressionCoupon($order)
    {
        if($order['paid'] || $order['status']==-2 || $order['is_del']) return false;
        $res=true;
        if($order['coupon_id'] && StoreCouponUser::be(['id'=>$order['coupon_id'],'uid'=>$order['uid'],'status'=>1])){
            $res= $res && StoreCouponUser::where(['id'=>$order['coupon_id'],'uid'=>$order['uid']])->update(['status'=>0,'use_time'=>0]);
        }
        return $res;
    }

}