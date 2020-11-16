<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/18
 * Time: 15:15
 */

namespace app\gzh\controller;


use app\core\model\user\UserBill;
use app\gzh\model\shop\ShopOrder;
use app\gzh\model\shop\ShopProduct;
use app\gzh\model\user\User;
use app\gzh\model\user\UserAddress;
use service\JsonService;

class ShopApi extends AuthController
{
    /**
     * 商品列表
     * @auth:pyp
     * @date:2020/5/18 15:41
     */
    public function product_list()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = ShopProduct::getProductList($page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 商品详情
     * @auth:pyp
     * @date:2020/5/18 15:42
     */
    public function product_detail()
    {
        $id = input('get.id/d',0);
        if (!$id) return JsonService::failjson('数据错误');
        $detail = ShopProduct::get($id);
        if (empty($detail)) return JsonService::failjson('数据错误');
        $detail->slider_image = json_decode($detail->slider_image,1);
        return JsonService::successfuljson($detail);
    }

    /**
     * 购买商品
     * @auth:pyp
     * @date:2020/5/18 15:54
     */
    public function buy()
    {
        $id = input('post.id/d',0);
        $num = input('post.num/d',0);
        $mark = input('post.mark','');
        $addr_id = input('post.addr_id/d',0);
        if (!$id || !$num || !$addr_id) return JsonService::failjson('数据错误');
        $detail = ShopProduct::get($id);
        if (empty($detail)) return JsonService::failjson('数据错误');
        $addr = UserAddress::get($addr_id);
        if (empty($addr)) return JsonService::failjson('地址错误');
        //订单
        $time = time();
        $userInfo = $this->userInfo;
        $data['order_id'] = 'jf'.date('YmdHis').mt_rand(100000,999999);
        $data['uid'] = $userInfo['uid'];
        $data['product_id'] = $id;
        $data['user_name'] = $addr['real_name'];
        $data['user_phone'] = $addr['phone'];
        $data['user_address'] = $addr['province'].$addr['city'].$addr['district'].$addr['detail'];
        $data['total_num'] = $num;
        $data['total_integral'] = bcmul($detail['integral'],$num,2);
        $data['mark'] = $mark;
        $data['status'] = 0;
        $data['is_del'] = 0;
        $data['add_time'] = $time;
        //日志
        $bill['uid'] = $userInfo['uid'];
        $bill['link_id'] = $data['order_id'];
        $bill['pm'] = 0;
        $bill['title'] ='购买商品';
        $bill['category'] = 'integral';
        $bill['type'] = 'pay_product';
        $bill['number'] = $data['total_integral'];
        $bill['mark'] = '积分支付'.$data['total_integral'].'购买商品';
        $bill['status'] = 1;
        $bill['add_time'] = $time;
        ShopOrder::beginTrans();
        $res1 = ShopOrder::set($data);
        $res2 = UserBill::set($bill);
        $res3 = User::edit(['integral'=>bcsub($userInfo['integral'],$data['total_integral'],2)],$userInfo['uid']);
//        $res4 = ShopProduct::decStock($id,$num); //减库存
//        $res5 = ShopProduct::addSales($id,$num);//加销量
        $res4 = ShopProduct::where('id',$id)->setDec('stock',$num);
        $res5 = ShopProduct::where('id',$id)->setInc('sales',$num);
        $res = $res1 && $res2 && $res3 && $res4 && $res5;
        ShopOrder::checkTrans($res);
        if ($res){
            return JsonService::successfuljson('购买成功');
        }else{
            return JsonService::failjson('数据错误,购买失败');
        }

    }

    /**
     * 订单列表
     * @auth:pyp
     * @date:2020/5/18 17:06
     */
    public function order_list()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = ShopOrder::getOrderList($this->uid,$page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 收货
     * @auth:pyp
     * @date:2020/5/18 17:07
     */
    public function user_take_order()
    {
        $id = input('get.id/d',0);
        if (!$id) return JsonService::failjson('数据错误');
        $order = ShopOrder::get($id);
        if (empty($order)) return JsonService::failjson('数据错误');
        if ($order['uid'] != $this->uid) return JsonService::failjson('数据错误');
        if ($order['status'] == 2) return JsonService::failjson('不能重复收货');
        $res = ShopOrder::edit(['status'=>2],$id);
        if ($res){
            return JsonService::successfuljson('收货成功');
        }else{
            return JsonService::failjson('数据错误,收货失败');
        }
    }

    /**
     * 删除订单
     * @auth:pyp
     * @date:2020/5/18 17:17
     */
    public function order_del()
    {
        $id = input('get.id/d',0);
        if (!$id) return JsonService::failjson('数据错误');
        $order = ShopOrder::get($id);
        if (empty($order)) return JsonService::failjson('数据错误');
        if ($order['uid'] != $this->uid) return JsonService::failjson('数据错误');
        $res = ShopOrder::edit(['is_del'=>1],$id);
        if ($res){
            return JsonService::successfuljson('删除成功');
        }else{
            return JsonService::failjson('数据错误,删除失败');
        }
    }

    /**
     * 订单详情
     * @auth:pyp
     * @date:2020/5/19 9:17
     */
    public function order_detail()
    {
        $id = input('get.id/d',0);
        if (!$id) return JsonService::failjson('数据错误');
        $order = ShopOrder::get($id);
        if (empty($order)) return JsonService::failjson('数据错误');
        if ($order['uid'] != $this->uid) return JsonService::failjson('数据错误');
        $detail = ShopOrder::getOrderDetail($id);
        return JsonService::successfuljson($detail);
    }
}