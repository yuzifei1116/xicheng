<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\mini\controller;


use app\core\model\ad\Ad;
use app\core\model\shop\ShopCart;
use app\core\model\shop\ShopCategory;
use app\core\model\shop\ShopComment;
use app\core\model\shop\ShopOrder;
use app\core\model\shop\ShopProduct;
use app\core\model\user\User;
use service\JsonService;

class ShopApi extends AuthController
{
    /*
    * 白名单不验证token 如果传入token执行验证获取信息，没有获取到用户信息
    */
    public static function whiteList()
    {
        return [
            'shop_cate_list',
            'shop_list',
            'product_detail',
//            'buy',
//            'comment',
//            'order_list',
        ];
    }

    /**
     * 商品分类列表
     */
    public function shop_cate_list()
    {
        $list = ShopCategory::getCagteList();
        return JsonService::successfuljson($list);
    }

    public function shop_list()
    {
        $cate_id = input('get.cate_id/d',0);
        $store_name = input('get.store_name','');
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',4);
        if ($cate_id) {
            $cate = ShopCategory::get($cate_id);
            if (empty($cate)) JsonService::fail('数据错误');
        }

        $data['list'] = ShopProduct::getProductList($cate_id,$store_name,$page,$limit);
        $data['ad'] = Ad::getAd(2);
        return JsonService::successful($data);
    }

    /**
     * 商品详情
     */
    public function product_detail()
    {
        $id = input('get.id/d',0);
        if (!$id) return JsonService::fail('数据错误');
        $product = ShopProduct::get(['id'=>$id,'is_del'=>0,'is_show'=>1]);
        if (empty($product)) JsonService::fail('数据不存在');
        $product['image'] = request()->domain().$product['image'];

        //处理轮播图
        $slider_image = json_decode($product['slider_image'],true);

        foreach ($slider_image as $k=>&$v){
            $v = request()->domain().$v;
        }
        $product['slider_image'] = $slider_image;
        return JsonService::successfuljson($product);
    }

    /**
     * 加入购物车
     */
    public function add_cart()
    {
        $product_id = input('get.id/d',0);
        $num = input('get.num/d',1);
        if (!$product_id || !$num) return JsonService::failjson('参数错误');
        if ($num < 1) return JsonService::failjson('请输入添加的数量');
        if (!ShopProduct::be($product_id)) return JsonService::failjson('没有此商品');
        $uid = $this->uid;
        $cart = ShopCart::get(['uid'=>$uid,'product_id'=>$product_id,'is_pay'=>0]);
        if (!$cart){
            $data = [
                'uid'=>$uid,
                'product_id'=>$product_id,
                'num'=>$num,
                'add_time'=>time(),
            ];
            $res = ShopCart::set($data);
        }else{
            $res = ShopCart::where('id',$cart['id'])->setInc('num',$num);
        }

        if ($res){
            return JsonService::successfuljson('添加成功');
        }else{
            return JsonService::failjson('添加失败');
        }
    }

    /**
     * 编辑购物车
     */
    public function set_cart()
    {
        $id = input('get.id/d',0);//购物车id
        $type = input('get.type',1); //1加 0减
        if (!$id) return JsonService::failjson('参数错误');
        $cart = ShopCart::get($id);

        if ($type){
            $res = ShopCart::where('id',$id)->setInc('num',1);
        }else{
            if ($cart['num'] <= 1) return JsonService::failjson('数量已到最小，不能再减');
            $res = ShopCart::where('id',$id)->setDec('num',1);
        }
        if ($res){
            return JsonService::successfuljson('编辑成功');
        }else{
            return JsonService::failjson('编辑失败');
        }
    }

    public function cart_list()
    {
        $list = ShopCart::getCartList($this->uid);
        return JsonService::successfuljson($list);
    }

    public function confirm_order()
    {
        $cid = input('get.cid','');//购物车id
        $product_id = input('get.product_id/d',0);//购物车id
        $num = input('get.num/d',1);//数量
        if (!$cid && !$product_id) return JsonService::failjson('参数错误');
        $uid = $this->uid;
        if ($cid){
            $data['cart_info'] = ShopCart::getConfirmOrder($cid,$uid);
        }else{
            if (!$num) return JsonService::failjson('请选择商品数量');
            $data = [
                'uid'=>$uid,
                'product_id'=>$product_id,
                'num'=>$num,
                'is_new'=>1,
                'add_time'=>time(),
            ];
            $cart = ShopCart::set($data);
            $data['cart_info'] = ShopCart::getConfirmOrder($cart->id,$uid);
        }
        $key = ShopCart::cacheOrderInfo($uid,$data['cart_info']);
        $data['integral'] = $this->userInfo['integral'];
        return JsonService::successfuljson($data);
    }

    /**
     * 积分兑换
     */
    public function buy()
    {
        $product_id = input('get.id/d',0);
        $num = input('get.num/d',1);
        if (!$product_id) return JsonService::failjson('数据错误');
        $product = ShopProduct::get(['id'=>$product_id,'is_del'=>0,'is_show'=>1]);
        if (empty($product)) return JsonService::failjson('商品不存在');
        if ($product['stock'] < $num) return JsonService::failjson('库存不足');
        //验证积分
        $total_integral = bcmul($product['integral'],$num,2); //总积分
        if ($total_integral <= 0) return JsonService::failjson('此商品无需支付');
        $userInfo = User::get($this->uid);
        if ($userInfo['integral'] < $total_integral) return JsonService::failjson('积分不足');

        //验证库存
        if (empty($product)) return JsonService::failjson('数据不存在');
        if ($product['stock'] < 1) return JsonService::failjson('库存不足');

        ShopProduct::beginTrans();
        //创建订单
        $res1 = $this->create_order($product,$total_integral,$num);
        //减库存 加销量
        $res2 = ShopProduct::where('id',$product_id)->setDec('stock',$num);
        $res3 = ShopProduct::where('id',$product_id)->setInc('sales',$num);
        //减积分
        $res4 = User::where('uid',$this->uid)->setDec('integral',$total_integral);
        $res = $res1 && $res2 && $res3 && $res4;
        ShopProduct::checkTrans($res);
        if ($res){
            return JsonService::successfuljson('兑换成功');
        }else{
            return JsonService::failjson('兑换失败,请稍后再试');
        }
    }

    /**
     * 创建订单
     */
    public function create_order($product,$total_integral,$num = 0)
    {
        if (empty($product) || !$num) return false;
        $data = [
            'order_id' => 'jf'.date('YmdHis').mt_rand(100000,999999),
            'product_id'=>$product['id'],
            'uid'=>$this->uid,
            'total_num'=>$num,
            'total_integral'=>$total_integral,
            'add_time'=>time()
        ];
        return ShopOrder::set($data);
    }

    /**
     * 评论
     */
    public function comment()
    {
        $oid = input('post.oid/d',0);
        $content = input('post.content','');
        if (!$oid) return JsonService::failjson('数据错误');
        if ($content == '') return JsonService::failjson('请输入评论内容');
        if (strlen($content) > 240) return JsonService::failjson('评论内容80字以内');
        $order = ShopOrder::get($oid);
        if (empty($order)) return JsonService::failjson('数据不存在');
        if ($order['uid'] != $this->uid) JsonService::failjson('数据错误');
        if ($order['status'] != 1) return JsonService::failjson('数据错误');
        ShopComment::beginTrans();
        $res1 = ShopComment::set(['order_id'=>$order['order_id'],'uid'=>$this->uid,'content'=>$content,'add_time'=>time()]);
        $res2 = ShopOrder::edit(['status'=>2],$oid);
        $res = $res1 && $res2;
        ShopComment::checkTrans($res);
        if ($res){
            return JsonService::successfuljson('评论成功');
        }else{
            return JsonService::failjson('评论失败');
        }
    }

    /**
     * 积分商城订单列表
     */
    public function order_list()
    {
        $type = input('get.type/d',0); //0全部 1待取货 2待评价 3已完成
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $order = ShopOrder::getOderList($this->uid,$type,$page,$limit);
        return JsonService::successfuljson($order);
    }
}