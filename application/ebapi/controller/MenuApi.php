<?php

namespace app\ebapi\controller;


use app\core\model\coupon\Coupon;
use app\core\model\coupon\CouponUse;
use app\core\model\menu\Menu;
use app\core\model\menu\MenuCart;
use app\core\model\menu\MenuCate;
use app\core\model\menu\MenuComment;
use app\core\model\menu\MenuOrder;
use app\core\model\user\User;
use app\core\model\user\UserBill;
use app\core\util\SystemConfigService;
use app\gzh\controller\Test;
use service\JsonService;

class MenuApi extends AuthController
{
    public static function whiteList()
    {
        return [
            'menu',
            'menu_cate_list',
            // 'set_cart',
            // 'cart_list',
            // 'clear_cart',
            // 'menu_info',
            // 'buy',
            // 'order_list',
            // 'comment',
        ];
    }

    /**
     * 美食列表
     */
    public function menu()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',4);
        $menu = Menu::getMenuList($page,$limit); //TODO 美食列表
        return JsonService::successfuljson($menu);
    }

    /**
     * 美食分类列表
     */
    public function menu_cate_list()
    {
//        $table_num = input('get.table_num/d',0); //桌号
        $list = MenuCate::getCateList();
//        foreach ($list as $k=>&$v){
//            $menu = Menu::get(['cate_id'=>$v['id'],'status'=>1]) ?: [];
//            $menu['image'] = request()->domain() . $menu['image'];
//            $v['_children'] = $menu;
//        }
        return JsonService::successfuljson($list);
    }

    /**
     * 加入购物车
     */
    public function set_cart()
    {
        $menu_id = input('get.menu_id/d',0);
        $type = input('get.type/d',1); // 1加  0减
        if (!$menu_id) return JsonService::failjson('请选择菜品');
        $menu = Menu::get($menu_id);
        if (empty($menu)) return JsonService::failjson('数据错误');
        if (!$menu['status'] || $menu['is_del']) return JsonService::failjson('数据错误');
        $cart = MenuCart::get(['uid'=>$this->uid,'menu_id'=>$menu_id,'is_pay'=>0]);
        if (empty($cart)){
            $data = ['uid'=>$this->uid,'menu_id'=>$menu_id,'num'=>1,'is_pay'=>0,'add_time'=>time()];
            $res = MenuCart::set($data);
        }else{
            if ($type){
                $res = MenuCart::where('id',$cart['id'])->setInc('num');
            }else{
                if ($cart['num'] > 1){
                    $res = MenuCart::where('id',$cart['id'])->setDec('num');
                }else{
                    $res = MenuCart::del($cart['id']);
                }
            }
        }
        if ($res){
            return JsonService::successfuljson();
        }else{
            return JsonService::failjson('系统错误');
        }
    }

    /**
     * 购物车加减
     */
    public function cart_add_sub()
    {
        $id = input('get.id/d',0); 

        $type = input('get.type/d'); // 1加  0减

        if(!$id) return JsonService::failjson('请选择菜品');
        
        if(!$type) return JsonService::failjson('数据错误');

        if($type == 1) $res = MenuCart::where('id',$cart['id'])->setDec('num');
            else $res = MenuCart::where('id',$id)->delete();

        if ($res){
            return JsonService::successfuljson();
        }else{
            return JsonService::failjson('系统错误');
        }
    }

    /**
     * 支付-优惠券
     */
    public function cart_pay()
    {
        //待重写
        $table_num = input('get.table_num/d',0); //桌号
        $people_num = input('get.people_num/d',0); //人数
        $integral = input('get.integral/d',0); //使用积分
        $coupon_id = input('get.coupon_id/d',0); //使用优惠券
        $mark = input('get.mark',''); //使用优惠券

        if (!$table_num) return JsonService::failjson('请输入桌号');
        if (!$people_num) return JsonService::failjson('请输入人数');
        $carts = MenuCart::getCartList($this->uid);
        if (empty($carts)) return JsonService::failjson('请先添加菜品');
        $userInfo = User::get($this->uid);
        if ($userInfo['integral'] < $integral) return JsonService::failjson('积分不足');

        //创建订单
        $order = $this->create_order($carts,$table_num,$people_num,$integral,$coupon_id,$mark);
        $info = ['oid'=>$order['id']];
        if ($order['pay_price'] <= 0){
            if (MenuOrder::jsPayPrice($order['order_id'], $this->userInfo['uid'])){
                return JsonService::status('success', '微信支付成功',$info);
            }else{
                return JsonService::status('pay_error','微信支付失败');
            }
        }else{ //微信支付
            return JsonService::status('success', '微信支付成功',$info);
        }
    }

    /**
     * 购物车列表
     */
    public function cart_list()
    {
        $list = MenuCart::getCartList($this->uid);
        return JsonService::successfuljson($list);
    }

    /**
     * 清空购物车
     */
    public function clear_cart()
    {
        $ids = MenuCart::where('uid',$this->uid)->where('is_pay',0)->column('id');
        if (empty($ids)) return JsonService::failjson('购物车为空');
        if (MenuCart::where('uid',$this->uid)->delete($ids)){
            return JsonService::successfuljson('已清空');
        }else{
            return JsonService::failjson('操作失败');
        }
    }

    /**
     * 菜单信息
     */
    public function menu_info()
    {
        $table_num = input('get.table_num/d',0); //桌号
        $menu_info = MenuCart::getMenuInfo($this->uid);
        return JsonService::successfuljson('ok',$menu_info,200,$table_num);
    }

    /**
     * 付款
     */
    public function buy()
    {
        $table_num = input('get.table_num/d',0); //桌号
        $people_num = input('get.people_num/d',0); //人数
        $integral = input('get.integral/d',0); //使用积分
        $coupon_id = input('get.coupon_id/d',0); //使用优惠券
        $mark = input('get.mark',''); //使用优惠券

        if (!$table_num) return JsonService::failjson('请输入桌号');
        if (!$people_num) return JsonService::failjson('请输入人数');
        $carts = MenuCart::getCartList($this->uid);
        if (empty($carts)) return JsonService::failjson('请先添加菜品');
        $userInfo = User::get($this->uid);
        if ($userInfo['integral'] < $integral) return JsonService::failjson('积分不足');

        //创建订单
        $order = $this->create_order($carts,$table_num,$people_num,$integral,$coupon_id,$mark);
        $info = ['oid'=>$order['id']];
        if ($order['pay_price'] <= 0){
            if (MenuOrder::jsPayPrice($order['order_id'], $this->userInfo['uid'])){
                return JsonService::status('success', '微信支付成功',$info);
            }else{
                return JsonService::status('pay_error','微信支付失败');
            }
        }else{ //微信支付
            return JsonService::status('success', '微信支付成功',$info);
        }


    }

    /**
     * 创建订单
     */
    public function create_order($carts,$table_num,$people_num,$integral,$coupon_id,$mark='')
    {
        $cart_ids = '';
        $people_price = SystemConfigService::get('people_price'); //餐位费 每人
        $money = bcmul($people_num,$people_price,2);//总金额
        foreach ($carts as $k=>$v){
            $cart_ids .= $v['id'];
            $money = bcadd($money,bcmul($v['price'],$v['num'],2),2);
        }

        //优惠券抵扣
        $discount_price = 0; //TODO 优惠券抵扣金额
        if ($coupon_id){
            $coupon = Coupon::get(['id'=>$coupon_id,'is_del'=>0,'status'=>1]);
            if (empty($coupon)) return JsonService::failjson('没有此优惠券');
            if ($coupon['start_time'] > time() || $coupon['end_time'] < time()) return JsonService::failjson('此优惠券暂不能使用');
            $coupon_use = CouponUse::get(['coupon_id'=>$coupon_id,'uid'=>$this->uid,'is_use'=>0]);
            if (empty($coupon_use)) return JsonService::failjson('请先领取优惠券');
            $discount_price = $coupon['money'];
        }

        //积分抵扣
        $integral_deduction = 0; //TODO 积分抵扣金额
        if ($integral){
            $integral_ratio = SystemConfigService::get('integral_ratio'); //积分抵扣比例
            $integral_deduction = bcmul($integral,$integral_ratio,2); //抵扣金额
        }

        //菜单支付金额
        $pay_price = $money; //支付金额
        if ($discount_price && $money >= $discount_price) $pay_price = bcsub($money,$discount_price,2);
        if ($integral_deduction && $pay_price >= $integral_deduction) $pay_price = bcsub($money,$integral_deduction,2);

        $data = [
            'order_id'=>'me'.date('YmdHis').mt_rand(100000,999999),
            'uid'=>$this->uid,
            'table_num'=>$table_num,
            'people_num'=>$people_num,
            'people_total_price'=>bcmul($people_price,$people_num,2),
            'menu'=>$cart_ids,
            'money'=>$money,
            'pay_price'=>$pay_price,
            'discount_price'=>$discount_price,
            'coupon_id'=>$coupon_id,
            'integral_deduction'=>$discount_price,
            'mark'=>$mark,
            'add_time'=>time()
        ];
        MenuOrder::beginTrans();
        $order = MenuOrder::set($data);
        $res2 = true;
        $res22 = true;
        $res3 = true;
        $res33 = true;
        if ($coupon_id){ //使用优惠券
            $res2 = CouponUse::edit(['is_use'=>1],$coupon_use['id']);
            $res22 = UserBill::expend($this->uid,'点餐','menu','coupon',0,$order['id']);
        }
        if ($integral){ //减积分
            $res3 = User::where('uid',$this->uid)->setDec('integral',$integral);
            $res33 = UserBill::expend($this->uid,'点餐','menu','integral',$integral,$order['id']);
        }
        $res = $order && $res2 && $res22 && $res3 && $res33;
        MenuOrder::checkTrans($res);
         if ($res){
             return $order;
         }else{
             return JsonService::failjson('创建订单失败');
         }
    }

    /**
     * 订单列表
     */
    public function order_list()
    {
        $type = input('get.type/d',0); //0全部 1待付款 2待评价 3已完成
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = MenuOrder::OrderList($this->uid,$type,$page,$limit);
        return JsonService::successfuljson($list);
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
        $order = MenuOrder::get($oid);
        if (empty($order)) return JsonService::failjson('数据不存在');
        if ($order['uid'] != $this->uid) JsonService::failjson('数据错误');
        if ($order['status'] != 1) return JsonService::failjson('数据错误');
        MenuComment::beginTrans();
        $res1 = MenuComment::set(['oid' => $order['id'], 'order_id' => $order['order_id'], 'uid' => $this->uid, 'content' => $content, 'add_time' => time()]);
        $res2 = MenuOrder::edit(['status' => 2], $oid);
        $res = $res1 && $res2;
        MenuComment::checkTrans($res);
        if ($res) {
            return JsonService::successfuljson('评论成功');
        } else {
            return JsonService::failjson('评论失败');
        }
    }
}