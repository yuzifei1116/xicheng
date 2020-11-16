<?php


namespace app\ebapi\controller;


use app\core\model\activity\Activity;
use app\core\model\ad\Ad;
use app\core\model\banner\Banner;
use app\core\model\coupon\Coupon;
use app\core\model\coupon\CouponUse;
use app\core\model\menu\Menu;
use app\core\model\stay\StayComment;
use app\core\model\stay\StayInfo;
use app\core\model\stay\StayRoom;
use app\core\model\stay\StayType;
use app\core\model\user\User;
use app\core\model\user\UserBill;
use app\core\util\GroupDataService;
use app\core\util\SystemConfigService;
use service\JsonService;

class StayApi extends AuthController
{
    /*
    * 白名单不验证token 如果传入token执行验证获取信息，没有获取到用户信息
    */
    public static function whiteList()
    {
        return [
            'stay_type',
            'room_list',
            'stay_preferential',
            'room_detail',
            'order_list',
            'comment',
        ];
    }

    /**
     * 房间类型列表
     */
    public function stay_type()
    {
        $activity = StayType::getRoomTypeList(1); //TODO 房间类型列表
        return JsonService::successfuljson($activity);
    }

    /**
     * 房间列表
     */
    public function room_list()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',4);
        $type_id = input('get.type_id/d',0); //房间类型
        $start_time = input('get.start_time',date('Y-m-d')); //预定开始时间
        $end_time = input('get.end_time',date('Y-m-d',strtotime(date('Y-m-d')) + 24*3600)); //预定结束时间
        $start_time = strtotime($start_time) + 12*3600;
        $end_time = strtotime($end_time) + 12*3600;
        $num = (int)(($end_time - $start_time) / (3600*24)); //预定的几天
        $room = StayRoom::roomList($type_id,$start_time,$end_time,$num,$page,$limit); //TODO 房间列表
        return JsonService::successfuljson($room);
    }

    /**
     * 房间列表-超值优惠
     */
    public function stay_preferential()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',4);
        $start_time = input('get.start_time',date('Y-m-d')); //预定开始时间
        $end_time = input('get.end_time',date('Y-m-d',strtotime(date('Y-m-d')) + 24*3600)); //预定结束时间
        $start_time = strtotime($start_time) + 12*3600;
        $end_time = strtotime($end_time) + 12*3600;
        $num = (int)(($end_time - $start_time) / (3600*24)); //预定的几天
        $activity = StayRoom::roomPreferentialList($start_time,$end_time,$num,$page,$limit); //TODO 房间列表 超值优惠
        return JsonService::successfuljson($activity);
    }

    /**
     * 房间详情
     */
    public function room_detail()
    {
        $id = input('get.id/d',0);
        if (!$id) return JsonService::failjson('数据错误');
        $room = StayRoom::get($id);
        if (empty($room)) return JsonService::failjson('数据不存在');
        $room['image'] = request()->domain() . $room['image'];
        if (!$room['breakfast']){ //早餐
            $room['breakfast'] = '无';
        }else{
            $room['breakfast'] = '有';
        }
        return JsonService::successfuljson($room);
    }

    /**
     * 支付
     */
    public function pay()
    {
        $id = input('get.id/d',0);
        $integral = input('get.integral/d',0); //使用积分
        $coupon_id = input('get.coupon_id/d',0); //使用优惠券
        $start_time = input('get.start_time',''); //预定开始时间
        $end_time = input('get.end_time',''); //预定结束时间

        //判断用户积分
        $userInfo = User::get($this->uid);
        if ($userInfo['integral'] < $integral) return JsonService::failjson('积分不足');

        if (!$id) return JsonService::failjson('数据错误');
        $room = StayRoom::get($id);
        if (empty($room)) JsonService::failjson('数据错误');

        //验证此房间是否被预定
        $start_time = strtotime($start_time) + 12*3600;
        $end_time = strtotime($end_time) + 12*3600;
        $num = (int)(($end_time - $start_time) / (3600*24)); //预定的几天
        if (!$num) return JsonService::failjson('最少需要预定一天');
        $res = StayInfo::getReserveRoom($id,$start_time,$end_time,$num); //可以预定为true
        if (!$res) return JsonService::failjson('此房间已被预定');

        $order = $this->create_order($room,$integral,$coupon_id,$start_time,$end_time,$num);
        $info = ['oid'=>$order['id']];
        if ($order['pay_price'] <= 0){
            if (StayInfo::jsPayPrice($order['order_id'], $this->userInfo['uid'])){
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
    public function create_order($room,$integral,$coupon_id,$start_time,$end_time,$num)
    {
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

        //积分抵扣$integral_deduction = 0; //TODO 积分抵扣金额
        if ($integral){
            $integral_ratio = SystemConfigService::get('integral_ratio'); //积分抵扣比例
            $integral_deduction = bcmul($integral,$integral_ratio,2); //抵扣金额
        }

        $money = bcmul($room['price'],$num,2); //房间总金额
        $pay_price = $money; //支付金额
        if ($discount_price && $money >= $discount_price) $pay_price = bcsub($money,$discount_price,2);
        if ($integral_deduction && $pay_price >= $integral_deduction) $pay_price = bcsub($money,$integral_deduction,2);
        $data = [
            'order_id'=>'ro'.date('YmdHis').mt_rand(100000,999999),
            'type_id'=>$room['type_id'],
            'room_id'=>$room['id'],
            'number'=>$room['number'],
            'uid'=>$this->uid,
            'nickname'=>$this->userInfo['nickname'],
            'day_num'=>$num,
            'money'=>$money,
            'discount_price'=>$discount_price,
            'integral_deduction'=>$discount_price,
            'coupon_id'=>$coupon_id,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'add_time'=>time()
        ];

        StayInfo::beginTrans();
        $order = StayInfo::edit($data);
        $res2 = true;
        $res22 = true;
        $res3 = true;
        $res33 = true;
        if ($coupon_id){ //使用优惠券
            $res2 = CouponUse::edit(['is_use'=>1],$coupon_use['id']);
            $res22 = UserBill::expend($this->uid,'购买门票','activity','coupon',0,$order['id']);
        }
        if ($integral){ //减积分
            $res3 = User::where('uid',$this->uid)->setDec('integral',$integral);
            $res33 = UserBill::expend($this->uid,'购买门票','activity','integral',$integral,$order['id']);
        }

        $res = $order && $res2 && $res22 && $res3 && $res33;
        StayInfo::checkTrans($res);
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
        $type = input('get.type/d',0); //0全部 1待付款 2待评论
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = StayInfo::infoList($this->uid,$type,$page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 订单详情
     * @throws \think\exception\DbException
     */
    public function order_detail()
    {
        $oid = input('get.oid/d',0);
        if (!$oid) return JsonService::failjson('数据错误');
        $order = StayInfo::get($oid);
        if (empty($order)) return JsonService::failjson('订单不存在');
        $order['_room'] = StayRoom::get($order['room_id']);
        if ($order['paid']){
            $order['_paid'] = '已支付';
        }else{
            $order['_paid'] = '未支付';
        }
        if ($order['status'] == 0){
            $order['_status'] = '未入住';
        }elseif($order['status'] == 1){
            $order['_status'] = '已入住';
        }elseif($order['status'] == 2){
            $order['_status'] = '已退房';
        }elseif($order['status'] == 3){
            $order['_status'] = '待评论';
        }elseif($order['status'] == 4){
            $order['_status'] = '已完成';
        }elseif($order['status'] == 5){
            $order['_status'] = '已退款';
        }elseif($order['status'] == 6){
            $order['_status'] = '已取消';
        }
        $order['start_time'] = date('Y-m-d H:i:s',$order['start_time']);
        $order['end_time'] = date('Y-m-d H:i:s',$order['end_time']);
        $order['pay_time'] = date('Y-m-d H:i:s',$order['pay_time']);
        $order['add_time'] = date('Y-m-d H:i:s',$order['add_time']);
        if ($order['check_in_time']) $order['check_in_time'] = date('Y-m-d H:i:s',$order['check_in_time']);
        if ($order['check_out_time']) $order['check_out_time'] = date('Y-m-d H:i:s',$order['check_out_time']);
    }

    /**
     * 取消订单
     * @throws \think\exception\DbException
     */
    public function cancel_order()
    {
        $oid = input('get.oid/d',0);
        if (!$oid) return JsonService::failjson('数据错误');
        $order = StayInfo::get($oid);
        if (empty($order)) return JsonService::failjson('订单不存在');
        if ($order['paid']) return JsonService::failjson('数据错误');
        if ($order['status'] == 6) return JsonService::failjson('订单已取消');
        if (StayInfo::edit(['status'=>3],$oid)){
            return JsonService::successful('取消成功');
        }else{
            return JsonService::failjson('取消失败');
        }
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
        $order = StayInfo::get($oid);
        if (empty($order)) return JsonService::failjson('数据不存在');
        if ($order['uid'] != $this->uid) JsonService::failjson('数据错误');
        if ($order['status'] != 3) return JsonService::failjson('请退房之后,再评论');
        StayComment::beginTrans();
        $res1 = StayComment::set(['room_id'=>$order['room_id'],'uid'=>$this->uid,'content'=>$content,'add_time'=>time()]);
        $res2 = StayInfo::edit(['status'=>4],$oid);
        $res = $res1 && $res2;
        StayComment::checkTrans($res);
        if ($res){
            return JsonService::successfuljson('评论成功');
        }else{
            return JsonService::failjson('评论失败');
        }
    }

    /**
     * 查看二维码
     * @return string|void
     * @throws \think\exception\DbException
     */
    public function qrcode()
    {
        $order_id = input('get.order_id/d',0);
        $order = StayInfo::get(['order_id'=>$order_id]);
        if (empty($order)) return JsonService::failjson('数据不存在');
        if ($order['uid'] != $this->uid) return JsonService::failjson('数据错误');
        if ($order['status'] != 0 || !$order['paid']) return JsonService::failjson('数据错误');

        $dir = 'public/qrcode/stay/'.date('Y/m/d');
        if(!is_dir($dir)){
            // 创建文件夹
            mkdir($dir,777,true);
        }
        $file = $dir.'/'.$order_id.'.png';
        self::qr_code($order_id,$file);
        return JsonService::successfuljson(request()->domain() . '/'.$file);
    }

    /**
     * 创建永久二维码
     * @param $id
     * @param $type
     */
    public function qr_code($order_id,$file)
    {
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel = 'L';  //容错级别
        $matrixPointSize = 5;      //生成图片大小
        \QRcode::png($order_id,$file, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}