<?php
namespace app\gzh\controller;

use app\core\model\user\UserBill;
use app\gzh\model\store\StoreOrder;
use app\gzh\model\store\StorePink;
use app\gzh\model\store\StoreProduct;
use app\core\util\GroupDataService;
use service\HttpService;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UtilService;
use service\CacheService;
use think\Cache;
use service\HookService;
use basic\ModelBasic;
use app\gzh\model\user\User;
use app\admin\model\order\StoreOrderStatus;
use behavior\admin\OrderBehavior;
use behavior\wechat\PaymentBehavior;
use think\Db;


/**
 * 异步接口
 * Class PublicApi
 * @package app\gzh\controller
 *
 */
class AsynApi extends GzhBasic
{


    /**
     * 删除 复原 未支付订单 1小时执行一次
     * @author: gyz
     * @Time: 2020/6/1 13:37
     */
    public function del_no_pay_order()
    {

    }


    /**
     * 检查拼团时间是否到期，每隔30min检查一次,
     * 超时没完成的 修改成3，未完成。
     * 阶梯团 到时间的修改未到最大阶梯，检查达到了哪个阶梯，修改status=2，退款
     * 大团 到时间 没满 就退款
     * 小团 是否24小时自动成团，
     *
     */
    public function check_pink_date()
    {
        //大团 过期就是status=3未完成，并退款。
        $pinkovertime = StorePink::getOverEndTime1(1);
//        dump($pinkovertime);die;
        foreach ($pinkovertime as $k=>$v){
            StorePink::changeStatus($v['id'],3);//修改状态
            $res_refund1 = self::updateRefundY($v['order_id_key'],$v['price'],1);
            if ($res_refund1) {
                StorePink::changeIsRefund($v['id'],1);//修改退款状态
            }
        }
    }


    /**
     * 异步接口 退款处理
     * @param Request $request
     * @param $id
     */
    public static function updateRefundY($id,$refund_price,$com_type)
    {
        if ($refund_price == 0) return true;
        if ($com_type == 4){
            $data['type'] = 2;
        }elseif ($com_type < 4){
            $data['type'] = 1;
        }
        $data['refund_price'] = $refund_price;
//        $data = Util::postMore([
//            'refund_price',
//            ['type',1],
//        ],$request);
        if(!$id) return false;
        $product = StoreOrder::get($id);
        if(!$product) return false;//Json::fail('数据不存在!');
        if($product['refund_price'] > 0) return true;//Json::fail('已退完支付金额!不能再退款了');
//        if(!$data['refund_price']) return Json::fail('请输入退款金额');
        $refund_price = $data['refund_price'];
        $data['refund_price'] = bcadd($data['refund_price'],$product['refund_price'],2);
        $bj = bccomp((float)$product['pay_price'],(float)$data['refund_price'],2);
        if($bj < 0) return false;//Json::fail('退款金额大于支付金额，请修改退款金额');
        if($data['type'] == 1){
            $data['refund_status'] = 2;
        }else if($data['type'] == 2){
            $data['refund_status'] = 0;
        }
        $type =  $data['type'];
        unset($data['type']);
        $refund_data['pay_price'] = $product['pay_price'];
        $refund_data['refund_price'] = $refund_price;
        if($product['pay_type'] == 'weixin'){

            //查找订单退款表 有几个就拼上几
            $count = Db::name('store_order_refund')->where('order_id',$product['order_id'])->count();
            $count = $count+1;
            $refund_data['refund_id'] = $product['order_id'].$count;

            $order_id = empty($product['order_id_father']) ? $product['order_id'] : $product['order_id_father'];

            if($product['is_channel']){//小程序
                try{
                    HookService::listen('routine_pay_order_refund',$order_id,$refund_data,true,PaymentBehavior::class);
                }catch(\Exception $e){
                    return false;//Json::fail($e->getMessage());
                }
            }else{
                try{
                    HookService::listen('wechat_pay_order_refund',$order_id,$refund_data,true,PaymentBehavior::class);
                }catch(\Exception $e){
                    return false;//Json::fail($e->getMessage());
                }
            }
        }else if($product['pay_type'] == 'yue'){
            ModelBasic::beginTrans();
            $usermoney = User::where('uid',$product['uid'])->value('now_money');
            $res1 = User::bcInc($product['uid'],'now_money',$refund_price,'uid');
            $res2 = $res2 = UserBill::income('商品退款',$product['uid'],'now_money','pay_product_refund',$refund_price,$product['id'],bcadd($usermoney,$refund_price,2),'订单退款到余额'.floatval($refund_price).'元');
            try{
                HookService::listen('store_order_yue_refund',$product,$refund_data,false,OrderBehavior::class);
            }catch (\Exception $e){
                ModelBasic::rollbackTrans();
                return false;//Json::fail($e->getMessage());
            }
            $res = $res1 && $res2;
            ModelBasic::checkTrans($res);
            if(!$res) return false;//Json::fail('余额退款失败!');
        }
        $resEdit = StoreOrder::edit($data,$id);
        if($resEdit){
            $data['type'] = $type;
//            if($data['type'] == 1)  StorePink::setRefundPink($id);
            HookService::afterListen('store_product_order_refund_y',$data,$id,false,OrderBehavior::class);
            StoreOrderStatus::setStatus($id,'refund_price','退款给用户'.$refund_price.'元');
            ModelBasic::commitTrans();
            return true;//Json::successful('修改成功!');
        }else{
            StoreOrderStatus::setStatus($id,'refund_price','退款给用户'.$refund_price.'元失败');
            return false;//Json::successful('修改失败!');
        }
    }




}