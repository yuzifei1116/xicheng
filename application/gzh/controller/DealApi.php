<?php
namespace app\gzh\controller;

use app\core\model\routine\RoutineFormId;//待完善
use app\core\model\user\UserLevel;
use app\gzh\model\merchant\MerchantList;
use app\gzh\model\store\StorePresale;
use app\gzh\model\store\StoreSeckill;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UtilService;
use think\Db;
use think\Request;
use app\core\behavior\GoodsBehavior;//待完善
use app\gzh\model\store\StoreCouponUser;
use app\gzh\model\store\StoreOrder;
use app\gzh\model\store\StoreProductAttrValue;
use app\gzh\model\store\StoreCart;
use app\gzh\model\user\User;
use app\gzh\model\store\StorePink;
use app\core\util\WechatService;
use app\core\util\GroupDataService;
use app\gzh\model\store\StoreCombination;


use app\core\model\user\UserBill;
use app\core\model\system\SystemUserLevel;
use app\core\model\system\SystemUserTask;
use app\gzh\model\store\StoreCategory;
use app\gzh\model\store\StoreCouponIssue;
use app\gzh\model\store\StoreProduct;
use service\HttpService;
use service\UploadService;
use service\CacheService;
use think\Cache;

/**
 * 交易相关接口 api接口
 * Class AuthApi
 * @package app\gzh\controller
 *
 */
class DealApi extends AuthController
{

    /**
     * 拼团 秒杀 砍价 加入到购物车
     * @param string $productId
     * @param int $cartNum
     * @param string $uniqueId
     * @param int $combinationId
     * @param int $secKillId
     * @return \think\response\Json
     */
    public function now_buy()
    {
        $productId = input('productId/d',0);
        $cartNum = input('cartNum/d',1);
        $uniqueId = input('uniqueId','');
        $combinationId = input('combinationId/d',0);
        $secKillId = input('secKillId/d',0);
        $bargainId = input('bargainId/d',0);
        $presaleId = input('presaleId/d',0);
        $mer_id = StoreProduct::where('id',$productId)->value('mer_id');
        if ($combinationId>0) $mer_id = StoreCombination::where('id',$productId)->value('mer_id');
        if ($secKillId>0) $mer_id = StoreSeckill::where('id',$productId)->value('mer_id');
        if ($presaleId>0) $mer_id = StorePresale::where('id',$productId)->value('mer_id');

        if (!$productId) return JsonService::failjson('参数错误');
//        if ($bargainId && StoreBargainUserHelp::getSurplusPrice($bargainId, $this->userInfo['uid'])) return JsonService::failjson('请先砍价');
        $res = StoreCart::setCart($this->userInfo['uid'], $productId, $cartNum, $uniqueId, 'product', 1, $combinationId, $secKillId, $bargainId,$presaleId,$mer_id);
        if (!$res) return JsonService::failjson(StoreCart::getErrorInfo());
        else  return JsonService::successfuljson('ok', ['cartId' => $res->id]);
    }

    /**
     * 订单页面
     * @param Request $request
     * @return \think\response\Json
     */
    public function confirm_order()
    {
//        $data = UtilService::postMore(['cartId'], $request);
//        $cartId = $data['cartId'];
        $cartId = input('cartId',''); 
        if (!is_string($cartId) || !$cartId) return JsonService::failjson('请提交购买的商品');
        $cartGroup = StoreCart::getUserProductCartList($this->userInfo['uid'], $cartId, 1);
        if (count($cartGroup['invalid'])) return JsonService::failjson($cartGroup['invalid'][0]['productInfo']['store_name'] . '已失效!');
        if (!$cartGroup['valid']) return JsonService::failjson('请提交购买的商品');
        $cartInfo = $cartGroup['valid'];
        $priceGroup = StoreOrder::getOrderPriceGroup($cartInfo);
        $other = [
            'offlinePostage' => SystemConfigService::get('offline_postage'),
            'integralRatio' => SystemConfigService::get('integral_ratio')
        ];
        $usableCoupon = StoreCouponUser::beUsableCoupon($this->userInfo['uid'], $priceGroup['totalPrice']);
        $cartIdA = explode(',', $cartId);
        if (count($cartIdA) > 1) {
            $seckill_id = 0;
            $combination_id = 0;
            $presale_id = 0;
        }else {
            $seckillinfo = StoreCart::where('id', $cartId)->find();

            $seckill_id = (int)$seckillinfo['seckill_id'] > 0 ? $seckillinfo['seckill_id'] : 0;
            $combination_id = (int)$seckillinfo['combination_id'] > 0 ? $seckillinfo['combination_id'] : 0;
            $presale_id = (int)$seckillinfo['presale_id'] > 0 ? $seckillinfo['presale_id'] : 0;

//            if ((int)$seckillinfo['seckill_id'] > 0) {
//                $seckill_id = $seckillinfo['seckill_id'];
//            }else {
//                $seckill_id = 0;
//            }
//            if ((int)$seckillinfo['combination_id'] > 0) {
//                $combination_id = $seckillinfo['combination_id'];
//            }else {
//                $combination_id = 0;
//            }
//
//            if ((int)$seckillinfo['presale_id'] > 0) {
//                $presale_id = $seckillinfo['presale_id'];
//            }else {
//                $presale_id = 0;
//            }
        }
        $data['usableCoupon'] = $usableCoupon;
        $data['seckill_id'] = $seckill_id;
        $data['combination_id'] = $combination_id;
        $data['presale_id'] = $presale_id;
        $data['cartInfo'] = $cartInfo;
        $data['priceGroup'] = $priceGroup;
        $data['orderKey'] = StoreOrder::cacheOrderInfo($this->userInfo['uid'], $cartInfo, $priceGroup, $other);
        $data['offlinePostage'] = $other['offlinePostage'];
        $vipId=UserLevel::getUserLevel($this->uid);
        $this->userInfo['vip']=$vipId !==false ? true : false;
        if($this->userInfo['vip']){
            $this->userInfo['vip_id']=$vipId;
            $this->userInfo['discount']=UserLevel::getUserLevelInfo($vipId,'discount');
        }
        $data['userInfo']=$this->userInfo;
        $data['integralRatio'] = $other['integralRatio'];
        return JsonService::successfuljson($data);
    }

    /**
     * 创建订单
     * @param string $key
     * @return \think\response\Json
     */
    public function create_order($key = '')
    {
        if (!$key) return JsonService::failjson('参数错误!');
        if (StoreOrder::be(['order_id|unique' => $key, 'uid' => $this->userInfo['uid'], 'is_del' => 0]))
            return JsonService::status('extend_order', '订单已生成', ['orderId' => $key, 'key' => $key]);
        list($addressId, $couponId, $payType, $useIntegral, $mark, $combinationId, $pinkId, $seckill_id, $formId, $bargainId,$presale_id) = UtilService::postMore([
            'addressId', 'couponId', 'payType', 'useIntegral', 'mark', ['combinationId', 0], ['pinkId', 0], ['seckill_id', 0], ['formId', ''], ['bargainId', ''],['presale_id',0]
        ], Request::instance(), true);
        $payType = strtolower($payType);
//        if ($bargainId) StoreBargainUser::setBargainUserStatus($bargainId, $this->userInfo['uid']); //修改砍价状态
//        if ($pinkId) if (StorePink::getIsPinkUid($pinkId, $this->userInfo['uid'])) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经在该团内不能再参加了', ['orderId' => StoreOrder::getStoreIdPink($pinkId, $this->userInfo['uid'])]);
//        if ($pinkId) if (StoreOrder::getIsOrderPink($pinkId, $this->userInfo['uid'])) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经参加该团了，请先支付订单', ['orderId' => StoreOrder::getStoreIdPink($pinkId, $this->userInfo['uid'])]);
        if ($combinationId){
            //检查新开团状态，能否再开团，团类型。。。。
            $com_res = StoreCombination::checkComStatus($combinationId,$this->userInfo['uid']);
            if ($com_res === false){
                return JsonService::status('PAY_ERROR', StoreCombination::getErrorInfo());
            }
        }
        $order = StoreOrder::cacheKeyCreateOrder($this->userInfo['uid'], $key, $addressId, $payType, $useIntegral, $couponId, $mark, $combinationId, $pinkId, $seckill_id, $bargainId,$presale_id);
        $orderId = $order['order_id'];
        $info = compact('orderId', 'key');
        if ($orderId) {
            switch ($payType) {
                case "weixin":
                    $orderInfo = StoreOrder::where('order_id', $orderId)->find();
                    if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
                    if ($orderInfo['paid']) exception('支付已支付!');
                    //如果支付金额为0
                    if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                        //创建订单jspay支付
                        if (StoreOrder::jsPayPrice($orderId, $this->userInfo['uid'], $formId))
                            return JsonService::status('success', '微信支付成功', $info);
                        else
                            return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                    } else {
//                        RoutineFormId::SetFormId($formId, $this->uid);
                        try {
                            $jsConfig = StoreOrder::jsPay($orderId); //创建订单jspay
//                            if(isset($jsConfig['package']) && $jsConfig['package']){
//                                $package=str_replace('prepay_id=','',$jsConfig['package']);
//                                for($i=0;$i<3;$i++){
//                                    RoutineFormId::SetFormId($package, $this->uid);
//                                }
//                            }
                        } catch (\Exception $e) {
                            return JsonService::status('pay_error', $e->getMessage(), $info);
                        }
                        $info['jsConfig'] = $jsConfig;
                        return JsonService::status('wechat_pay', '订单创建成功', $info);
                    }
                    break;
                case 'yue':
                    if (StoreOrder::yuePay($orderId, $this->userInfo['uid']))
                        return JsonService::status('success', '余额支付成功', $info);
                    else {
                        $errorinfo = StoreOrder::getErrorInfo();
                        if (is_array($errorinfo))
                            return JsonService::status($errorinfo['status'], $errorinfo['msg'], $info);
                        else
                            return JsonService::status('pay_error', $errorinfo);
                    }
                    break;
                case 'offline':
                    RoutineFormId::SetFormId($formId, $this->uid);
                    //                RoutineTemplate::sendOrderSuccess($formId,$orderId);//发送模板消息
                    return JsonService::status('success', '订单创建成功', $info);
                    break;
            }
        } else return JsonService::failjson(StoreOrder::getErrorInfo('订单生成失败!'));
    }

    /**
   * 获取订单支付状态
   * @param string ordre_id 订单id
   * @return json
   * */
    public function get_order_pay_info()
    {
        $order_id = input('order_id','');

        if ($order_id == '') return JsonService::failjson('缺少参数');
        return JsonService::successfuljson(StoreOrder::tidyOrder(StoreOrder::where('order_id', $order_id)->find()));
    }

    //TODO 支付订单
    /**
     * 支付订单
     * @param string $uni
     * @return \think\response\Json
     */
    public function pay_order($uni = '', $paytype = 'weixin')
    {
        if (!$uni) return JsonService::failjson('参数错误!');
        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni);
        if (!$order) return JsonService::failjson('订单不存在!');


        $is_weikuan = 0; //是否是支付尾款
        if ($order['presale_id']){ //预售
            if ($order['presale_paid']) return JsonService::failjson('该订单已支付!');
            if ($order['paid'] && !$order['presale_paid']){
                $is_weikuan = 1;

                //判断是否在尾款支付时间内
//                $res_presale = StorePresale::where('id',$order['presale_id'])->field('wk_start_time,wk_stop_time')->find();
                $res_presale = Db::name('store_presale')->where('id',$order['presale_id'])->field('wk_start_time,wk_stop_time')->find();

                $now = time();
                if ($res_presale['wk_start_time']>$now){
                    return JsonService::failjson('未到尾款支付时间');
                }elseif($res_presale['wk_stop_time']<$now){
                    return JsonService::failjson('已经超过尾款支付时间');
                }

            }
        }else{
            if ($order['paid']) return JsonService::failjson('该订单已支付!');
        }
        if ($order['pink_id']) if (StorePink::isPinkStatus($order['pink_id'])) return JsonService::failjson('该订单已失效!');
        $order['pay_type'] = $paytype; //重新支付选择支付方式
        switch ($order['pay_type']) {
            case 'weixin':
                try {
                    if ($is_weikuan) { //预售
                        $jsConfig = StoreOrder::jsPayPresale($order); //订单列表发起支付
                    }else{
                        $jsConfig = StoreOrder::jsPay($order); //订单列表发起支付
                    }
                } catch (\Exception $e) {
                    return JsonService::failjson($e->getMessage());
                }
                return JsonService::status('wechat_pay', ['jsConfig' => $jsConfig, 'order_id' => $order['order_id']]);
                break;
            case 'yue':
                if ($is_weikuan) { //预售
                    $res = StoreOrder::yuePayPresale($order['order_id'], $this->userInfo['uid']);
                }else{
                    $res = StoreOrder::yuePay($order['order_id'], $this->userInfo['uid']);
                }

                if ($res) {
                    return JsonService::successfuljson('余额支付成功');
                }else {
                    $error = StoreOrder::getErrorInfo();
                    return JsonService::failjson(is_array($error) && isset($error['msg']) ? $error['msg'] : $error);
                }
                break;
            case 'offline':
                StoreOrder::createOrderTemplate($order);
                return JsonService::successfuljson('订单创建成功');
                break;
        }
    }

    /**
     * 支付预售的订单
     * @param string $uni
     * @param string $paytype
     * @author: gyz
     * @Time: 2020/5/30 9:39
     */
//    public function pay_order_presale($uni = '', $paytype = 'weixin')
//    {
//        if (!$uni) return JsonService::failjson('参数错误!');
//        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni);
//        if (!$order) return JsonService::failjson('订单不存在!');
//        if ($order['presale_paid']) return JsonService::failjson('该订单已支付!');
////        if ($order['pink_id']) if (StorePink::isPinkStatus($order['pink_id'])) return JsonService::failjson('该订单已失效!');
//        $order['pay_type'] = $paytype; //重新支付选择支付方式
//        switch ($order['pay_type']) {
//            case 'weixin':
//                try {
//                    $jsConfig = StoreOrder::jsPayPresale($order); //订单列表发起支付
//                } catch (\Exception $e) {
//                    return JsonService::failjson($e->getMessage());
//                }
//                return JsonService::status('wechat_pay', ['jsConfig' => $jsConfig, 'order_id' => $order['order_id']]);
//                break;
//            case 'yue':
//                if ($res = StoreOrder::yuePayPresale($order['order_id'], $this->userInfo['uid']))
//                    return JsonService::successfuljson('余额支付成功');
//                else {
//                    $error = StoreOrder::getErrorInfo();
//                    return JsonService::failjson(is_array($error) && isset($error['msg']) ? $error['msg'] : $error);
//                }
//                break;
//            case 'offline':
//                StoreOrder::createOrderTemplate($order);
//                return JsonService::successfuljson('订单创建成功');
//                break;
//        }
//
//    }




    //----------------------------------------------------------------------





    /*
     * 未支付的订单取消订单回退积分,回退优惠券,回退库存
     * @param string $order_id 订单id
     * */
    public function cancel_order($order_id = '')
    {
        if (StoreOrder::cancelOrder($order_id))
            return JsonService::successfuljson('取消订单成功');
        else
            return JsonService::failjson(StoreOrder::getErrorInfo());
    }

    /**
     * 申请退款
     * @param string $uni
     * @param string $text
     * @return \think\response\Json
     */
    public function apply_order_refund(Request $request)
    {
        $data = UtilService::postMore([
            ['text', ''],
            ['refund_reason_wap_img', ''],
            ['refund_reason_wap_explain', ''],
            ['uni', '']
        ], $request);
        $uni = $data['uni'];
        unset($data['uni']);
        if ($data['refund_reason_wap_img']) $data['refund_reason_wap_img'] = explode(',', $data['refund_reason_wap_img']);
        if (!$uni || $data['text'] == '') return JsonService::failjson('参数错误!');
        $res = StoreOrder::orderApplyRefund($uni, $this->userInfo['uid'], $data['text'], $data['refund_reason_wap_explain'], $data['refund_reason_wap_img']);
        if ($res)
            return JsonService::successfuljson();
        else
            return JsonService::failjson(StoreOrder::getErrorInfo());
    }

    /**
     * 再来一单
     * @param string $uni
     */
    public function order_details($uni = '')
    {

        if (!$uni) return JsonService::failjson('参数错误!');
        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni);
        if (!$order) return JsonService::failjson('订单不存在!');
        $order = StoreOrder::tidyOrder($order, true);
        $res = array();
        foreach ($order['cartInfo'] as $v) {
            if ($v['combination_id']) return JsonService::failjson('拼团产品不能再来一单，请在拼团产品内自行下单!');
            else  $res[] = StoreCart::setCart($this->userInfo['uid'], $v['product_id'], $v['cart_num'], isset($v['productInfo']['attrInfo']['unique']) ? $v['productInfo']['attrInfo']['unique'] : '', 'product', 0, 0);
        }
        $cateId = [];
        foreach ($res as $v) {
            if (!$v) return JsonService::failjson('再来一单失败，请重新下单!');
            $cateId[] = $v['id'];
        }
        return JsonService::successfuljson('ok', implode(',', $cateId));
    }
    /**
     * 购物车库存修改
     * @param int $cartId
     * @param int $cartNum
     */
    public function set_buy_cart_num($cartId = 0, $cartNum = 0)
    {
        if (!$cartId) return JsonService::failjson('参数错误');
        $res = StoreCart::edit(['cart_num' => $cartNum], $cartId);
        if ($res) return JsonService::successfuljson();
        else return JsonService::failjson('修改失败');
    }



    /*
     * 获取小程序订单列表统计数据
     *
     * */
    public function get_order_data()
    {
        return JsonService::successfuljson(StoreOrder::getOrderData($this->uid));
    }
    /**
     * 过度查$uniqueId
     * @param string $productId
     * @param int $cartNum
     * @param string $uniqueId
     * @return \think\response\Json
     */
    public function unique()
    {
        $productId = $_GET['productId'];
        if (!$productId || !is_numeric($productId)) return JsonService::failjson('参数错误');
        $uniqueId = StoreProductAttrValue::where('product_id', $productId)->value('unique');
        $data = $this->set_cart($productId, $cartNum = 1, $uniqueId);
        if ($data == true) {
            return JsonService::successfuljson('ok');
        }
    }

    /*
     * 再来一单
     *
     * */
    public function again_order($uni = ''){
        if(!$uni) return JsonService::failjson('参数错误!');
        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'],$uni);
        if(!$order) return JsonService::failjson('订单不存在!');
        $order = StoreOrder::tidyOrder($order,true);
        $res = array();
        foreach ($order['cartInfo'] as $v) {
            if($v['combination_id']) return JsonService::failjson('拼团产品不能再来一单，请在拼团产品内自行下单!');
            else if($v['bargain_id']) return JsonService::failjson('砍价产品不能再来一单，请在砍价产品内自行下单!');
            else if($v['seckill_id']) return JsonService::failjson('秒杀产品不能再来一单，请在砍价产品内自行下单!');
            else $res[] = StoreCart::setCart($this->userInfo['uid'], $v['product_id'], $v['cart_num'], isset($v['productInfo']['attrInfo']['unique']) ? $v['productInfo']['attrInfo']['unique'] : '', 'product', 0, 0);
        }
        $cateId = [];
        foreach ($res as $v){
            if(!$v) return JsonService::failjson('再来一单失败，请重新下单!');
            $cateId[] = $v['id'];
        }
        return JsonService::successfuljson('ok',implode(',',$cateId));
    }

    /**
     * 获取退款理由
     */
    public function get_refund_reason(){
        $reason = SystemConfigService::get('stor_reason')?:[];//退款理由
        $reason = str_replace("\r\n","\n",$reason);//防止不兼容
        $reason = explode("\n",$reason);
        return $this->successful($reason);
    }



}
