<?php
namespace app\gzh\controller;

use Api\Express;
use app\gzh\model\system\SystemGroupData;
use app\core\model\user\UserLevel;
use app\core\model\user\UserSign;
use app\core\model\routine\RoutineCode;//待完善
use app\core\model\routine\RoutineFormId;//待完善
use app\gzh\model\store\StoreBargain;
use app\gzh\model\store\StoreCombination;
use app\gzh\model\store\StoreCouponUser;
use app\gzh\model\store\StoreOrder;
use app\gzh\model\store\StoreOrderCartInfo;
use app\gzh\model\store\StoreProductRelation;
use app\gzh\model\store\StoreProductReply;
use app\gzh\model\store\StoreSeckill;
use app\gzh\model\user\User;
use app\gzh\model\user\UserNoticeSee;
use app\gzh\model\user\WechatUser;
use app\gzh\model\user\UserAddress;
use app\core\model\user\UserBill;
use app\gzh\model\user\UserExtract;
use app\gzh\model\user\UserNotice;
use app\gzh\model\user\UserRecharge;
use service\CacheService;
use app\core\util\GroupDataService;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UtilService;
use think\Request;
use think\Cache;


/**
 * 个人中心api接口
 * Class UserApi
 * @package app\gzh\controller
 *
 */
class UserApi extends AuthController
{

    /**
     * 获取用户信息 转换
     * @return mixed
     * gyz
     */
    public function get_user_info()
    {
        $user_info = $this->userInfo;
//        $wechat_info = WechatUser::getWechatInfo($user_info['uid']);
//        $user_info['subscribe'] = $wechat_info['subscribe'];
//        dump($wechat_info);die;
        unset($user_info['account']);
        unset($user_info['add_ip']);
        unset($user_info['pwd']);
        unset($user_info['spread_uid']);
        unset($user_info['last_ip']);
        unset($user_info['status']);

//        dump($this->request->isAjax());die;
//        if (request()->isAjax()) return JsonService::successfuljson($user_info);

        return $user_info;
    }

    /**
     * 单纯获取用户信息
     * gyz
     */
    public function get_user_info_only()
    {
        $res = $this->get_user_info();
        $title = SystemConfigService::get('wechat_share_title');
        $descript = SystemConfigService::get('wechat_share_synopsis');
        $img = SystemConfigService::get('wechat_share_img');

        $share = [
            'title' => $title,
            'descript' => $descript,
            'img' => $img,
        ];
        $res['share'] = $share;
        return JsonService::successfuljson($res);
    }


    /**
     * 获取用户所有地址
     * @author: gyz
     * @Time: 2020/4/30 10:50
     */
    public function user_address_list()
    {
        $page = input('page/d',1);
        $limit = input('limit/d',8);

        $list = UserAddress::getUserValidAddressList($this->uid,$page,$limit,'id,real_name,phone,province,city,district,detail,is_default');
        return JsonService::successfuljson($list);
    }
    /**
     * 添加 修改 收货地址
     * @return \think\response\Json
     */
    public function edit_user_address()
    {
        $request = Request::instance();
        if(!$request->isPost()) return JsonService::failjson('参数错误!');
        $addressInfo = UtilService::postMore([
//            ['address',[]],
            ['province',''],
            ['city',''],
            ['district',''],
            ['is_default',0],
            ['real_name',''],
            ['post_code',''],
            ['phone',''],
            ['detail',''],
            ['id',0]
        ],$request);
//        $addressInfo['province'] = $addressInfo['address']['province'];
//        $addressInfo['city'] = $addressInfo['address']['city'];
//        $addressInfo['district'] = $addressInfo['address']['district'];
        $addressInfo['is_default'] = $addressInfo['is_default'] == 1 ? 1 : 0;
        $addressInfo['uid'] = $this->uid;
//        unset($addressInfo['address']);

        if($addressInfo['id'] && UserAddress::be(['id'=>$addressInfo['id'],'uid'=>$this->uid,'is_del'=>0])){
            $id = $addressInfo['id'];
            unset($addressInfo['id']);
            if(UserAddress::edit($addressInfo,$id,'id')){
                if($addressInfo['is_default'])
                    UserAddress::setDefaultAddress($id,$this->uid);
                return JsonService::successfuljson();
            }else
                return JsonService::failjson('编辑收货地址失败!');
        }else{
            if($address = UserAddress::set($addressInfo)){
                if($addressInfo['is_default'])
                    UserAddress::setDefaultAddress($address->id,$this->uid);
                return JsonService::successfuljson(['id'=>$address->id]);
            }else
                return JsonService::failjson('添加收货地址失败!');
        }
    }
    /**
     * 获取一条用户地址
     * @param string $addressId 地址id
     * @return \think\response\Json
     */
    public function get_user_address()
    {
        $addressId = input('addressId/d',0);
//        $addressInfo = [];
//        if($addressId && is_numeric($addressId) && UserAddress::be(['is_del'=>0,'id'=>$addressId,'uid'=>$this->uid])){
//            $addressInfo = UserAddress::find($addressId);
//        }
        $addressInfo = UserAddress::where('is_del',0)->where('id',$addressId)->where('uid',$this->uid)->find();
        if (empty($addressInfo)) return JsonService::successfuljson('ok',[]);
        return JsonService::successfuljson($addressInfo);
    }

    /**
     * 获取默认地址
     * @return \think\response\Json
     */
    public function user_default_address()
    {
        $defaultAddress = UserAddress::getUserDefaultAddress($this->uid,'id,real_name,phone,province,city,district,detail,is_default');
        if($defaultAddress) return JsonService::successfuljson('ok',$defaultAddress);
        else return JsonService::successfuljson('empty',[]);
    }

    /**
     * 删除地址
     * @param string $addressId 地址id
     * @return \think\response\Json
     * gyz
     */
    public function remove_user_address()
    {
        $addressId = input('addressId/d',0);
        if(!$addressId) return JsonService::failjson('参数错误!');
        if(!UserAddress::be(['is_del'=>0,'id'=>$addressId,'uid'=>$this->uid]))
            return JsonService::failjson('地址不存在!');
        if(UserAddress::edit(['is_del'=>'1'],$addressId,'id'))
            return JsonService::successfuljson();
        else
            return JsonService::failjson('删除地址失败!');
    }

    /**
     * 个人中心 获取订单列表
     * @param string $type
     * @param int $first
     * @param int $limit
     * @param string $search
     * @return \think\response\Json
     */
    public function get_user_order_list()
    {
        list($type,$page,$limit,$search)=UtilService::getMore([
            ['type',''],
            ['page',1],
            ['limit',10],
            ['search',''],
        ],$this->request,true);
        return JsonService::successfuljson(StoreOrder::getUserOrderSearchList($this->uid,$type,$page,$limit,$search));
    }

    /**
     * 个人中心
     * @return \think\response\Json
     */
    public function my(){
        $this->userInfo['couponCount'] = StoreCouponUser::getUserValidCouponCount($this->uid);
        $this->userInfo['like'] = StoreProductRelation::getUserIdCollect($this->uid);;
        $this->userInfo['orderStatusNum'] = StoreOrder::getOrderStatusNum($this->uid);
        $this->userInfo['notice'] = UserNotice::getNotice($this->uid);
        $this->userInfo['recharge'] = UserBill::getRecharge($this->uid);//累计充值
        $this->userInfo['orderStatusSum'] = StoreOrder::getOrderStatusSum($this->uid);//累计消费
        $this->userInfo['extractTotalPrice'] = UserExtract::userExtractTotalPrice($this->uid);//累计提现
        $this->userInfo['statu'] = (int)SystemConfigService::get('store_brokerage_statu');
        $vipId=UserLevel::getUserLevel($this->uid);
        $this->userInfo['vip']=$vipId !==false ? true : false;
        if($this->userInfo['vip']){
            $this->userInfo['vip_id']=$vipId;
            $this->userInfo['vip_icon']=UserLevel::getUserLevelInfo($vipId,'icon');
            $this->userInfo['vip_name']=UserLevel::getUserLevelInfo($vipId,'name');
        }
        unset($this->userInfo['pwd']);
        return JsonService::successfuljson($this->userInfo);
    }

    /*
     * 查找用户消费充值记录
     *
     * */
    public function get_user_bill_list($page=1,$limit=8,$type=0)
    {
        return JsonService::successfuljson(UserBill::getUserBillList($this->uid,$page,$limit,$type));
    }

    /*
     * 获取当前登录的用户信息
     * */
    public function get_my_user_info()
    {
        list($isSgin,$isIntegral,$isall)=UtilService::getMore([
            ['isSgin',0],
            ['isIntegral',0],
            ['isall',0],
        ],$this->request,true);
        //是否统计签到
        if($isSgin || $isall){
            $this->userInfo['sum_sgin_day']=UserSign::getSignSumDay($this->uid);
            $this->userInfo['is_day_sgin']=UserSign::getToDayIsSign($this->uid);
            $this->userInfo['is_YesterDay_sgin']=UserSign::getYesterDayIsSign($this->uid);
            if(!$this->userInfo['is_day_sgin'] && !$this->userInfo['is_YesterDay_sgin']){
                $this->userInfo['sign_num']=0;
            }
        }
        //是否统计积分使用情况
        if($isIntegral || $isall){
            $this->userInfo['sum_integral']=UserBill::getRecordCount($this->uid,'integral','sign,system_add,gain');
            $this->userInfo['deduction_integral']=UserBill::getRecordCount($this->uid,'integral','deduction') ? : 0;
            $this->userInfo['today_integral']=UserBill::getRecordCount($this->uid,'integral','sign,system_add,gain','today');
        }
        unset($this->userInfo['pwd']);
        $this->userInfo['integral']=$this->userInfo['integral'];
        if(!$this->userInfo['is_promoter']){
            $this->userInfo['is_promoter']=(int)SystemConfigService::get('store_brokerage_statu') == 2 ? true : false;
        }
        return JsonService::successfuljson($this->userInfo);
    }

    /**
     * 个人中心 积分使用记录
     * @param int $first
     * @param int $limit
     * @return \think\response\Json
     */
    public function user_integral_list($page = 0,$limit = 8)
    {
        return JsonService::successfuljson(UserBill::userBillList($this->uid,$page,$limit));

    }

    /**
     * 个人中心 用户确认收货
     * @param string $uni
     * @return \think\response\Json
     */
    public function user_take_order($uni = '')
    {
        if(!$uni) return JsonService::failjson('参数错误!');

        $res = StoreOrder::takeOrder($uni,$this->uid);
        if($res)
            return JsonService::successfuljson();
        else
            return JsonService::failjson(StoreOrder::getErrorInfo());
    }

    /**
     * 获取佣金记录
     * @param int $page
     * @param int $limit
     * @param int $type
     * @auth:pyp
     * @date:2020/5/9 11:37
     */
    public function get_user_bill_spread_list($page=1,$limit=10)
    {
//        return JsonService::successfuljson(UserBill::getNowMoneySpreadList($this->uid,$page,$limit));
        return JsonService::successfuljson(UserBill::getSpreadBilllist($this->uid,$page,$limit));
    }

    /**
     * TODO 获取推广人列表
     * @param int $first
     * @param int $limit
     * @param int $type
     * @param int $keyword
     * @param string $order
     */
    public function user_spread_list($uid = 0,$page = 1,$limit = 10)
    {
        if ($uid == 0) $uid = $this->uid;
//        if (Cache::has('user_spread_'.$page.$limit)){
//            $data = Cache::get('user_spread_'.$page.$limit);
//        }else{
            $data['list'] = User::getUserSpreadList($uid,$page,$limit);
            $data['spread_count'] = $this->userInfo['spread_count'];
//            Cache::set('user_spread_'.$page.$limit,$data,60);
//        }

        return JsonService::successfuljson($data);
    }

    /**
     * 下级用户的订单列表
     * @param $uid
     * @auth:pyp
     * @date:2020/5/9 15:58
     */
    public function user_spread_order($uid,$page = 1,$limit = 10)
    {
        if (!$uid) return JsonService::failjson('数据错误');
        $list = StoreOrder::getUserSpreadOder($uid,$page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 下级用户的 下级  （废弃：跟user_spread_list一样）
     * @param $uid
     * @auth:pyp
     * @date:2020/5/9 15:59
     */
    public function user_spread_user_spread($uid,$page = 1,$limit = 10)
    {
        if (!$uid) return JsonService::failjson('数据错误');
        $list = User::getUserSpreadList($uid,$page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 个人中心 订单详情页
     * @param string $order_id
     * @return \think\response\Json
     */
    public function get_order($uni = ''){
        if($uni == '') return JsonService::failjson('参数错误');
        $order = StoreOrder::getUserOrderDetail($this->uid,$uni);
        if (!empty($order)) {
            $order = $order->toArray();
            $order['add_time_y'] = date('Y-m-d',$order['add_time']);
            $order['add_time_h'] = date('H:i:s',$order['add_time']);

        }
        if(!$order) return JsonService::failjson('订单不存在');
        return JsonService::successfuljson(StoreOrder::tidyOrder($order,true,true));
    }

    /*
     * 个人中心 查物流
     * @param int $uid 用户id
     * @param string $uni 订单id或者订单唯一键
     * @return json
     */
    public function express($uni = '')
    {
        if(!$uni || !($order = StoreOrder::getUserOrderDetail($this->uid,$uni))) return JsonService::failjson('查询订单不存在!');
        if($order['delivery_type'] != 'express' || !$order['delivery_id']) return JsonService::failjson('该订单不存在快递单号!');
        $cacheName = $uni.$order['delivery_id'];
        CacheService::rm($cacheName);
        $result = CacheService::get($cacheName,null);
        if($result === NULL){
            $result = Express::query($order['delivery_id']);
            if(is_array($result) &&
                isset($result['result']) &&
                isset($result['result']['deliverystatus']) &&
                $result['result']['deliverystatus'] >= 3)
                $cacheTime = 0;
            else
                $cacheTime = 1800;
            CacheService::set($cacheName,$result,$cacheTime);
        }
        return JsonService::successfuljson([ 'order'=>StoreOrder::tidyOrder($order,true), 'express'=>$result ? $result : []]);
    }

    /**
     * 个人中心 订单 评价订单
     * @param string $unique
     * @return \think\response\Json
     */
    public function user_comment_product($unique = '')
    {
        if(!$unique) return JsonService::failjson('参数错误!');
        $cartInfo = StoreOrderCartInfo::where('unique',$unique)->find();
        $uid = $this->uid;
        if(!$cartInfo || $uid != $cartInfo['cart_info']['uid']) return JsonService::failjson('评价产品不存在!');
        if(StoreProductReply::be(['oid'=>$cartInfo['oid'],'unique'=>$unique]))
            return JsonService::failjson('该产品已评价!');
        $group = UtilService::postMore([
            ['comment',''],['pics',[]],['product_score',5],['service_score',5]
        ],Request::instance());
        $group['comment'] = htmlspecialchars(trim($group['comment']));
        if($group['product_score'] < 1) return JsonService::failjson('请为产品评分');
        else if($group['service_score'] < 1) return JsonService::failjson('请为商家服务评分');
        if($cartInfo['cart_info']['combination_id']) $productId = $cartInfo['cart_info']['product_id'];
        else if($cartInfo['cart_info']['seckill_id']) $productId = $cartInfo['cart_info']['product_id'];
        else if($cartInfo['cart_info']['bargain_id']) $productId = $cartInfo['cart_info']['product_id'];
        else $productId = $cartInfo['product_id'];
        $group = array_merge($group,[
            'uid'=>$uid,
            'oid'=>$cartInfo['oid'],
            'unique'=>$unique,
            'product_id'=>$productId,
            'reply_type'=>'product'
        ]);
        StoreProductReply::beginTrans();
        $res = StoreProductReply::reply($group,'product');
        if(!$res) {
            StoreProductReply::rollbackTrans();
            return JsonService::failjson('评价失败!');
        }
        try{
//            HookService::listen('store_product_order_reply',$group,$cartInfo,false,StoreProductBehavior::class);
            StoreOrder::checkOrderOver($cartInfo['oid']);
        }catch (\Exception $e){
            StoreProductReply::rollbackTrans();
            return JsonService::failjson($e->getMessage());
        }
        StoreProductReply::commitTrans();
        return JsonService::successfuljson();
    }

    /**
     * 个人中心 删除订单
     * @param string $uni
     * @return \think\response\Json
     */
    public function user_remove_order($uni = '')
    {
        if(!$uni) return JsonService::failjson('参数错误!');
        $res = StoreOrder::removeOrder($uni,$this->uid);
        if($res)
            return JsonService::successfuljson();
        else
            return JsonService::failjson(StoreOrder::getErrorInfo());
    }

    /*
     * 获取用户签到记录列表
     *
     * */
//    public function get_sign_list($page=1,$limit=10)
//    {
//        return JsonService::successfuljson(UserSign::getSignList($this->uid,$page,$limit));
//    }
    public function get_sign_list($date = '')
    {

        if (empty($date)) $date = date('Y-m',time());

        $start_time = strtotime($date);
        $end_time = strtotime("$date + 1 month - 1 second");
//        dump($start_time);
//        dump($end_time);die;
        $list = UserSign::getSignListWithTime($this->uid,$start_time,$end_time);
        $allday = UserSign::getSignSumDay($this->uid);
        $data = [
            'list' => $list,
            'allday' => $allday,
            'monthday' => count($list)
        ];
        return JsonService::successfuljson($data);
    }

    /**
     * 用户签到
     * @return \think\response\Json
     */
    public function user_sign()
    {
        $signed = UserSign::getToDayIsSign($this->uid);
        if($signed) return JsonService::failjson('已签到');
        if(false !== $integral = UserSign::sign($this->uid))
            return JsonService::successfuljson('签到获得'.floatval($integral).'积分',['integral'=>$integral]);
        else
            return JsonService::failjson(UserSign::getErrorInfo('签到失败'));
    }



    /**
     *  个人中心 充值
     * @param int $price
     * @return \think\response\Json
     */
    public function user_wechat_recharge($price = 0)
    {
        if(!$price || $price <=0) return JsonService::failjson('参数错误');
        $storeMinRecharge = SystemConfigService::get('store_user_min_recharge');
        if($price < $storeMinRecharge) return JsonService::failjson('充值金额不能低于'.$storeMinRecharge);
        $list = SystemGroupData::getAllValue('recharge_marketing');
        $money = 0;
        if (!empty($list)){
            $recharge_price = array_column($list,'price');
            array_multisort($recharge_price,SORT_DESC,$list);
            foreach ($list as $k=>$v){
                if ($price >= $v['price']) {
                    $money = $v['money'];
                    break;
                }
            }
        }
        $rechargeOrder = UserRecharge::addRecharge($this->uid,$price,$money);
        if(!$rechargeOrder) return JsonService::failjson('充值订单生成失败!');
        try{
            return JsonService::successfuljson(UserRecharge::jsPay($rechargeOrder));
        }catch (\Exception $e){
            return JsonService::failjson($e->getMessage());
        }
    }

    /**
     * 个人中心 余额使用记录
     * @param int $first
     * @param int $limit
     * @return \think\response\Json
     */
    public function user_balance_list($first = 0,$limit = 8)
    {
        return JsonService::successfuljson(UserBill::userBillList($this->uid,$first,$limit,'now_money'));
    }

    //---------------------------------

    /*
     * 获取签到按月份查找
     * @param int $page 页码
     * @param int $limit 显示条数
     * @return json
     * */
    public function get_sign_month_list($page=1,$limit=10)
    {
        return JsonService::successfuljson(UserSign::getSignMonthList($this->uid,$page,$limit));
    }

    /**
     * 获取用户信息
     * @param int $userId 用户uid
     * @return \think\response\Json
     */
    public function get_user_info_uid($userId = 0){
        if(!$userId) return JsonService::failjson('参数错误');
        $res = User::getUserInfo($userId);
        if($res) return JsonService::successfuljson($res);
        else return JsonService::failjson(User::getErrorInfo());
    }

    /**
     * 获取用户手机号码
     * @param Request $request
     * @return \think\response\Json
     */
    public function bind_mobile(Request $request){
        list($iv,$cache_key,$encryptedData) = UtilService::postMore([
            ['iv',''],
            ['cache_key',''],
            ['encryptedData',''],
        ],$request,true);
        $iv  = urldecode(urlencode($iv));
        try{
            if(!Cache::has('eb_api_code_'.$cache_key)) return JsonService::failjson('获取手机号失败');
            $session_key=Cache::get('eb_api_code_'.$cache_key);
            $userInfo = \app\core\util\MiniProgramService::encryptor($session_key,$iv,$encryptedData);
            if(!empty($userInfo['purePhoneNumber'])){
                if(User::edit(['phone'=>$userInfo['purePhoneNumber']],$this->uid))
                    return JsonService::successfuljson('绑定成功',['phone'=>$userInfo['purePhoneNumber']]);
                else
                    return JsonService::failjson('绑定失败');
            }else
                return JsonService::failjson('获取手机号失败');
        }catch (\Exception $e){
            return JsonService::failjson('error',$e->getMessage());
        }
    }





    /**
     * 个人中心 获取一级推荐人
     * @param int $first
     * @param int $limit
     * @return \think\response\Json
     */
    public function get_spread_list($first = 0,$limit = 20)
    {
        return JsonService::successfuljson(User::getSpreadList($this->uid,$first,$limit));
    }

    /**
     * 个人中心 获取二级推荐人
     * @param int $first
     * @param int $limit
     * @return \think\response\Json
     */
    public function get_spread_list_two($two_uid=0,$first = 0,$limit = 20)
    {
        return JsonService::successfuljson(User::getSpreadList($two_uid,$first,$limit));
    }



    /**
     * 修改用户通知为已查看
     * @param $nid
     * @return \think\response\Json
     */
    public function see_notice($nid)
    {
        UserNotice::seeNotice($this->uid,$nid);
        return JsonService::successfuljson();
    }
    /*
     * 用户提现申请
     * @param array
     * @return \think\response\Json
     * */
    public function user_extract()
    {
        $data=UtilService::postMore([
            ['alipay_code',''],
            ['extract_type',''],
            ['money',0],
            ['name',''],
            ['bankname',''],
            ['cardnum',''],
        ],$this->request);
        if(UserExtract::userExtract($this->userInfo,$data))
            return JsonService::successfuljson('申请提现成功!');
        else
            return JsonService::failjson(UserExtract::getErrorInfo('提现失败'));
    }
    /**
     * 用户下级的订单
     * @param int $first
     * @param int $limit
     * @return json
     */
    public function subordinateOrderlist($first = 0, $limit = 8)
    {
        list($xUid,$status)=UtilService::postMore([
            ['uid',''],
            ['status',''],
        ],$this->request,true);
        switch ($status){
            case 0:
                $type='';
                break;
            case 1:
                $type=4;
                break;
            case 2:
                $type=3;
                break;
            default:
                return JsonService::failjson();
        }
        return JsonService::successfuljson(StoreOrder::getSubordinateOrderlist($xUid,$this->uid,$type,$first,$limit));
    }

    /**
     * 个人中心 用户下级的订单
     * @param int $first
     * @param int $limit
     * @return json
     */
    public function subordinateOrderlistmoney()
    {
        $request = Request::instance();
        $lists=$request->param();
        $status = $lists['status'];
        $type = '';
        if($status == 1) $type = 4;
        elseif($status == 2) $type = 3;
        $arr = User::where('spread_uid',$this->uid)->column('uid');
        $list = StoreOrder::getUserOrderCount(implode(',',$arr),$type);
        $price = [];
//        if(!empty($list)) foreach ($list as $k=>$v) $price[]=$v['pay_price'];
        if(!empty($list)) foreach ($list as $k=>$v) $price[]=$v;
        $cont = count($list);
        $sum = array_sum($price);
        return JsonService::successfuljson(['cont'=>$cont,'sum'=>$sum]);
    }

    /*
     * 用户提现记录列表
     * @param int $first 截取行数
     * @param int $limit 展示条数
     * @return json
     */
    public function extract($first = 0,$limit = 8)
    {
        return JsonService::successfuljson(UserExtract::extractList($this->uid,$first,$limit));
    }

    /**
     * 用户通知
     * @param int $page
     * @param int $limit
     * @return \think\response\Json
     */
    public function get_notice_list($page = 0, $limit = 8)
    {
        $list = UserNotice::getNoticeList($this->uid,$page,$limit);
        return JsonService::successfuljson($list);
    }

    /*
    * 昨日推广佣金
     * @return json
    */
    public function yesterday_commission()
    {
        return JsonService::successfuljson(UserBill::yesterdayCommissionSum($this->uid));
    }

    /*
     * 累计已提金额
     * @return json
     */
    public function extractsum()
    {
        return JsonService::successfuljson(UserExtract::extractSum($this->uid));
    }

    /**
     * 绑定推荐人
     * @param Request $request
     * @return \think\response\Json
     */
    public function spread_uid(Request $request){
        $data = UtilService::postMore(['spread_uid',0],$request);
        if($data['spread_uid']){
            if(!$this->userInfo['spread_uid']){
                $res = User::edit(['spread_uid'=>$data['spread_uid']],$this->uid);
                if($res) return JsonService::successfuljson('绑定成功');
                else return JsonService::successfuljson('绑定失败');
            }else return JsonService::failjson('已存在被推荐人');
        }else return JsonService::failjson('没有推荐人');
    }

    /**
     * 设置为默认地址
     * @param string $addressId
     * @return \think\response\Json
     */
    public function set_user_default_address($addressId = '')
    {
        if(!$addressId || !is_numeric($addressId)) return JsonService::failjson('参数错误!');
        if(!UserAddress::be(['is_del'=>0,'id'=>$addressId,'uid'=>$this->uid]))
            return JsonService::failjson('地址不存在!');
        $res = UserAddress::setDefaultAddress($addressId,$this->uid);
        if(!$res)
            return JsonService::failjson('地址不存在!');
        else
            return JsonService::successfuljson();
    }

    /**
     * 获取分销二维码
     * @return \think\response\Json
     */
    public  function get_code(){
        header('content-type:image/jpg');
        if(!$this->uid) return JsonService::failjson('授权失败，请重新授权');
        $path = makePathToUrl('routine/code');
        if($path == '')
            return JsonService::failjson('生成上传目录失败,请检查权限!');
        $picname = $path.'/'.$this->uid.'.jpg';
        $domain = SystemConfigService::get('site_url').'/';
        $domainTop = substr($domain,0,5);
        if($domainTop != 'https') $domain = 'https:'.substr($domain,5,strlen($domain));
        if(file_exists($picname)) return JsonService::successfuljson($domain.$picname);
        else{
            $res = RoutineCode::getCode($this->uid,$picname);
            if($res) file_put_contents($picname,$res);
            else return JsonService::failjson('二维码生成失败');
        }
        return JsonService::successfuljson($domain.$picname);
    }

    /*
     * 修改用户信息
     * */
    public function edit_user($formid=''){
        list($avatar,$nickname)=UtilService::postMore([
            ['avatar',''],
            ['nickname',''],
        ],$this->request,true);
        RoutineFormId::SetFormId($formid,$this->uid);
        if(User::editUser($avatar,$nickname,$this->uid))
            return JsonService::successfuljson('修改成功');
        else
            return JsonService::failjson('');
    }

    /*
     * 获取活动是否存在
     * */
    public function get_activity()
    {
        $data['is_bargin']=StoreBargain::validBargain() ? true : false;
        $data['is_pink']=StoreCombination::getPinkIsOpen() ? true : false;
        $data['is_seckill']=StoreSeckill::getSeckillCount() ? true : false;
        return JsonService::successfuljson($data);
    }

    /**
     * TODO 获取记录总和
     * @param int $type
     */
    public function get_record_list_count($type = 3)
    {
        $count = 0;
        if($type == 3) $count = UserBill::getRecordCount($this->uid, 'now_money', 'brokerage');
        else if($type == 4) $count = UserExtract::userExtractTotalPrice($this->uid);//累计提现
        $count = $count ? $count : 0;
        JsonService::successfuljson('',$count);
    }

    /**
     * TODO 获取订单返佣记录
     * @param int $first
     * @param int $limit
     * @param string $category
     * @param string $type
     */
    public function get_record_order_list($page = 0,$limit = 8,$category = 'now_money', $type = 'brokerage'){
        $data['list'] = [];
        $data['count'] = 0;
        $data['list'] = UserBill::getRecordList($this->uid,$page,$limit,$category,$type);
        $count = UserBill::getRecordOrderCount($this->uid, $category, $type);
        $data['count'] = $count ? $count : 0;
        if(!count($data['list'])) return JsonService::successfuljson([]);
        foreach ($data['list'] as $key=>&$value){
            $value['child'] = UserBill::getRecordOrderListDraw($this->uid, $value['time'],$category, $type);
            $value['count'] = count($value['child']);
        }
        return JsonService::successfuljson($data);
    }

    /**
     * 分销二维码海报生成
     */
    public function user_spread_banner_list(){
        header('content-type:image/jpg');
        try{
            $routineSpreadBanner = GroupDataService::getData('routine_spread_banner');
            if(!count($routineSpreadBanner)) return JsonService::failjson('暂无海报');
            $pathCode = makePathToUrl('routine/spread/code',3);
            if($pathCode == '') return JsonService::failjson('生成上传目录失败,请检查权限!');
            $picName = $pathCode.DS.$this->uid.'.jpg';
            $picName = trim(str_replace(DS, '/',$picName,$loop));
            $res = RoutineCode::getShareCode($this->uid, 'spread', '', $picName);
            if($res) file_put_contents($picName,$res);
            else return JsonService::failjson('二维码生成失败');
            $res = true;
            $url = SystemConfigService::get('site_url').'/';
            $domainTop = substr($url,0,5);
            if($domainTop != 'https') $url = 'https:'.substr($url,5,strlen($url));
            $pathCode = makePathToUrl('routine/spread/poster',3);
            $filelink=[
                'Bold'=>'public/static/font/SourceHanSansCN-Bold.otf',
                'Normal'=>'public/static/font/SourceHanSansCN-Normal.otf',
            ];
            if(!file_exists($filelink['Bold'])) return JsonService::failjson('缺少字体文件Bold');
            if(!file_exists($filelink['Normal'])) return JsonService::failjson('缺少字体文件Normal');
            foreach ($routineSpreadBanner as $key=>&$item){
                $config = array(
                    'image'=>array(
                        array(
                            'url'=>ROOT_PATH.$picName,     //二维码资源
                            'stream'=>0,
                            'left'=>114,
                            'top'=>790,
                            'right'=>0,
                            'bottom'=>0,
                            'width'=>120,
                            'height'=>120,
                            'opacity'=>100
                        )
                    ),
                    'text'=>array(
                        array(
                            'text'=>$this->userInfo['nickname'],
                            'left'=>250,
                            'top'=>840,
                            'fontPath'=>ROOT_PATH.$filelink['Bold'],     //字体文件
                            'fontSize'=>16,             //字号
                            'fontColor'=>'40,40,40',       //字体颜色
                            'angle'=>0,
                        ),
                        array(
                            'text'=>'邀请您加入'.SystemConfigService::get('site_name'),
                            'left'=>250,
                            'top'=>880,
                            'fontPath'=>ROOT_PATH.$filelink['Normal'],     //字体文件
                            'fontSize'=>16,             //字号
                            'fontColor'=>'40,40,40',       //字体颜色
                            'angle'=>0,
                        )
                    ),
                    'background'=>$item['pic']
                );
                $filename = ROOT_PATH.$pathCode.'/'.$item['id'].'_'.$this->uid.'.png';
                $res = $res && UtilService::setSharePoster($config,$filename);
                if($res) $item['poster'] = $url.$pathCode.'/'.$item['id'].'_'.$this->uid.'.png';
            }
            if($res) return JsonService::successfuljson($routineSpreadBanner);
            else return JsonService::failjson('生成图片失败');
        }catch (\Exception $e){
            return JsonService::failjson('生成图片时，系统错误',['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }
    }

    /**
     * 站内信列表
     * @auth:pyp
     * @date:2020/5/16 17:48
     */
    public function notice_list()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = UserNotice::getNoticeList($this->uid,$page,$limit);
        return JsonService::successfuljson($list);
    }

    public function read_notice()
    {
        $nid = input('get.nid/d',0);
        if (!$nid) return JsonService::failjson('数据错误');
        $detail = UserNotice::get($nid);
        if (empty($detail)) return JsonService::failjson('数据错误');
        $count = UserNoticeSee::getReadCount($this->uid,$nid);
        $res = 1;
        if (!$count){
            $data['uid'] = $this->uid;
            $data['nid'] = $nid;
            $data['add_time'] = time();
            $res = UserNoticeSee::set($data);
        }
        if ($res){
            return JsonService::successfuljson($detail);
        }else{
            return JsonService::failjson('数据错误');
        }

    }
};