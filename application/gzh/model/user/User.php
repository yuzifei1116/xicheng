<?php


namespace app\gzh\model\user;


use app\gzh\model\store\StoreCoupon;
use app\gzh\model\store\StoreCouponUser;
use app\gzh\model\store\StoreOrder;
use app\gzh\model\system\SystemNewGift;
use app\gzh\model\system\SystemPullNew;
use basic\ModelBasic;
use app\core\util\SystemConfigService;
use think\Request;
use think\response\Redirect;
use think\Session;
use think\Url;
use traits\ModelTrait;

class User extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time','add_ip','last_time','last_ip'];

    protected function setAddTimeAttr($value)
    {
        return time();
    }

    protected function setAddIpAttr($value)
    {
        return Request::instance()->ip();
    }

    protected function setLastTimeAttr($value)
    {
        return time();
    }

    protected function setLastIpAttr($value)
    {
        return Request::instance()->ip();
    }

    public static function setWechatUser($wechatUser,$spread_uid = 0)
    {
        return self::set([
            'account'=>'wx'.$wechatUser['uid'].time(),
            'pwd'=>md5(123456),
            'nickname'=>$wechatUser['nickname']?:'',
            'avatar'=>$wechatUser['headimgurl']?:'',
            'spread_uid'=>$spread_uid,
            'uid'=>$wechatUser['uid'],
            'user_type'=>'wechat',
            'now_money'=>$wechatUser['now_money'],
            'integral'=>$wechatUser['integral']
        ]);

    }

    public static function updateWechatUser($wechatUser,$uid)
    {
        return self::edit([
            'nickname'=>$wechatUser['nickname']?:'',
            'avatar'=>$wechatUser['headimgurl']?:''
        ],$uid,'uid');
    }

    public static function setSpreadUid($uid,$spreadUid)
    {
        return self::where('uid',$uid)->update(['spread_uid'=>$spreadUid]);
    }


    public static function getUserInfo($uid)
    {
        $userInfo = self::where('uid',$uid)->find();
        if(!$userInfo) exception('读取用户信息失败!');
        return $userInfo->toArray();
    }

    /**
     * 获得当前登陆用户UID
     * @return int $uid
     */
    public static function getActiveUid()
    {
        $uid = null;
        if(Session::has('loginUid','gzh')) $uid = Session::get('loginUid','gzh');
        if(!$uid && Session::has('loginOpenid','gzh') && ($openid = Session::get('loginOpenid','gzh')))
            $uid = WechatUser::openidToUid($openid);
        if(!$uid) exit(exception('请登陆!!'));
        return $uid;
    }

    /** //TODO 一级返佣
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerage($orderInfo)
    {
        $userInfo = User::getUserInfo($orderInfo['uid']);
        if(!$userInfo || !$userInfo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ? : 1;//获取后台分销类型
        if($storeBrokerageStatu == 1){
            if(!User::be(['uid'=>$userInfo['spread_uid'],'is_promoter'=>1])) return true;
        }
        $brokerageRatio = (SystemConfigService::get('store_brokerage_ratio') ?: 0)/100;
        if($brokerageRatio <= 0) return true;
        $cost = isset($orderInfo['cost']) ? $orderInfo['cost'] : 0;//成本价
        if($cost > $orderInfo['pay_price']) return true;//成本价大于支付价格时直接返回
        $brokeragePrice = bcmul(bcsub($orderInfo['pay_price'],$cost,2),$brokerageRatio,2);
        //返佣之后余额
        $orderInfo['pay_price'] = bcsub($orderInfo['pay_price'],$orderInfo['pay_postage'],2);
        $balance = bcsub($userInfo['now_money_spread'],$brokeragePrice,2);
        if($brokeragePrice <= 0) return true;
        $mark = $userInfo['nickname'].'成功消费'.floatval($orderInfo['pay_price']).'元,奖励推广佣金'.floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('获得推广佣金',$userInfo['spread_uid'],'now_money_spread','brokerage',$brokeragePrice,$orderInfo['id'],$balance,$mark);
        $res2 = self::bcInc($userInfo['spread_uid'],'now_money_spread',$brokeragePrice,'uid');
        $res = $res1 && $res2;
        self::checkTrans($res);
        if($res) self::backOrderBrokerageTwo($orderInfo);
        return $res;
    }

    /**
     * //TODO 二级推广
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerageTwo($orderInfo){
        $userInfo = User::getUserInfo($orderInfo['uid']);
        $userInfoTwo = User::getUserInfo($userInfo['spread_uid']);
        if(!$userInfoTwo || !$userInfoTwo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ? : 1;//获取后台分销类型
        if($storeBrokerageStatu == 1){
            if(!User::be(['uid'=>$userInfoTwo['spread_uid'],'is_promoter'=>1]))  return true;
        }
        $brokerageRatio = (SystemConfigService::get('store_brokerage_two') ?: 0)/100;
        if($brokerageRatio <= 0) return true;
        $cost = isset($orderInfo['cost']) ? $orderInfo['cost'] : 0;//成本价
        if($cost > $orderInfo['pay_price']) return true;//成本价大于支付价格时直接返回
        $brokeragePrice = bcmul(bcsub($orderInfo['pay_price'],$cost,2),$brokerageRatio,2);
        //返佣之后余额
        $balance = bcsub($userInfo['now_money_spread'],$brokeragePrice,2);
        if($brokeragePrice <= 0) return true;
        $mark = '二级推广人'.$userInfo['nickname'].'成功消费'.floatval($orderInfo['pay_price']).'元,奖励推广佣金'.floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('获得推广佣金',$userInfoTwo['spread_uid'],'now_money_spread','brokerage',$brokeragePrice,$orderInfo['id'],$balance,$mark);
        $res2 = self::bcInc($userInfoTwo['spread_uid'],'now_money_spread',$brokeragePrice,'uid');
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     *根据分享的$spreadUid 获取上级的用户信息
     * pyp
     */
    public static function getUserBySpreadUid($spreadUid)
    {
        $user = self::where('uid',$spreadUid)->find();
        return $user;
    }

//    public static function updateSpread($spreadUid,$userInfo)
//    {
//        User::edit(['spread_uid'=>$spreadUid],$userInfo['uid'],'uid');
////        拉新营销
//        if (Cache::has('spread_uid')) {
//            $spreadUid = Cache::get('spread_uid');
//            $user = User::getUserBySpreadUid($spreadUid);
//            if ($user) {
//                $data = SystemPullNew::getPullNew();
//                if (!empty($data) && isset($data['status']) && $data['status'] == 1){
//                    $now_money = bcadd($data['give_now_money'],$user['now_money'],2);
//                    $integral = bcadd($data['give_integral'],$user['integral'],2);
//                    User::edit(['now_money'=>$now_money,'integral'=>$integral],$user['uid']);
//                }
//            }
//        }
//    }

    /**
     * 获取用户下级推广人
     * @param int $uid  当前用户
     * @param int $grade 等级  0  一级 1 二级
     * @param string $orderBy  排序
     * @return array|bool|void
     */
    public static function getUserSpreadGrade($uid = 0,$grade = 0,$orderBy = '',$keyword = '',$offset = 0,$limit = 20){
        if(!$uid) return [];
        $gradeGroup = [0,1];
        if(!in_array($grade,$gradeGroup)) return self::setErrorInfo('等级错误');
        $userStair = self::where('spread_uid',$uid)->column('uid');
        if(!count($userStair)) return [];
        if($grade == 0) return self::getUserSpreadCountList(implode(',',$userStair),$orderBy,$keyword,$offset,$limit);
        $userSecondary = self::where('spread_uid','in',implode(',',$userStair))->column('uid');
        return self::getUserSpreadCountList(implode(',',$userSecondary),$orderBy,$keyword,$offset,$limit);
    }

    /**
     * 获取用户下级列表
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     * @auth:pyp
     * @date:2020/5/9 15:03
     */
    public static function getUserSpreadList($uid,$page,$limit)
    {
//        $uids = self::where('spread_uid',$uid)->column('uid');
//        if (!count($uids)) return [];
//        $list = self::getUserSpreadCountList(implode(',',$uids),$page,$limit);
//        return $list;

        $model = new self;
        $model = $model->where('spread_uid',$uid);
        $model = $model->field("uid,nickname,avatar,from_unixtime(add_time,'%Y/%m/%d') as time,spread_count");
        $model = $model->page($page,$limit);
        $list = $model->select();
//        dump(self::getLastSql());die;
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['order'] = StoreOrder::getOrderCount($v['uid']);
        }
        return $list;
    }

    /**
     * 获取团队信息
     * @param string $uid
     * @param string $orderBy
     * @param string $keyword
     */
    public static function getUserSpreadCountList($uid,$page = 1,$limit = 20)
    {
        $model = new self;
//        $model = $model->alias(' u');
//        $model = $model->join('StoreOrder o','u.uid=o.uid','LEFT');
        $model = $model->where('uid','IN',$uid);
        $model = $model->field("uid,nickname,avatar,from_unixtime(add_time,'%Y/%m/%d') as time,spread_count");
        $model = $model->group('uid');
        $model = $model->order('uid asc');
        $model = $model->page($page,$limit);
        $list = $model->select();
//        dump(self::getLastSql());die;
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['order'] = StoreOrder::getOrderCount($v['uid']);
        }
        return $list;
    }

    /**
     * TODO 获取推广人数 一级
     * @param int $uid
     * @return bool|int|string
     */
    public static function getSpreadCount($uid = 0){
        if(!$uid) return false;
        return self::where('spread_uid',$uid)->count();
    }

    /**
     * TODO 获取推广人数 二级
     * @param int $uid
     * @return bool|int|string
     */
    public static function getSpreadLevelCount($uid = 0){
        if(!$uid) return false;
        $uidSubordinate = self::where('spread_uid',$uid)->column('uid');
        if(!count($uidSubordinate)) return 0;
        return self::where('spread_uid','IN',implode(',',$uidSubordinate))->count();
    }


    /**
     * 新人礼
     * @auth:pyp
     * @date:2020/5/14 14:45
     */
    public static function newGift($wechatInfo)
    {
        $data = SystemNewGift::getNewGift(); //查询新人礼

        if (!empty($data) && isset($data['status']) && $data['status'] == 1){ //不为空, status=1
            $wechatInfo['now_money'] = $data['give_now_money'];
            $wechatInfo['integral'] = $data['give_integral'];

            //赠送积分明细
            UserBill::income('新人礼',$wechatInfo['uid'],'integral','new_gift',$data['give_integral'],0,$data['give_integral'],'新人赠送积分' . $data['give_integral']);

            //赠送余额明细
            UserBill::income('新人礼',$wechatInfo['uid'],'now_money','new_gift',$data['give_now_money'],0,$data['give_now_money'],'新人赠送余额' . $data['give_now_money'] . '元');

            //赠送优惠券
            if ($data['give_coupon'] != ''){
                $coupon_list = json_decode($data['give_coupon'],true);
                foreach ($coupon_list as $k=>$v){
                    $coupon = StoreCoupon::get($k);
                    if ($coupon['status'] != 1) continue;
                    if ($coupon['is_del'] == 1) continue;
                    if ($coupon['is_limit'] == 1 && $coupon['coupon_num'] < 1) continue;
                    if ($coupon['time_type'] == 2 && $coupon['coupon_end_time'] < time()) continue;
                    if ($coupon['is_limit'] == 1 && $coupon['coupon_num']>0){
                        $num = $coupon['coupon_num'] >= $v ? $v : $coupon['coupon_num'];
                    }elseif ($coupon['is_limit'] == 0){
                        $num = $v;
                    }else{
                        continue;
                    }
                    for ($i = 0; $i < $num; $i++) {
                        $time = time();
                        $user_coupon['cid'] = $coupon['id'];
                        $user_coupon['uid'] = $wechatInfo['uid'];
                        $user_coupon['type'] = 'new_gift_send';
                        $user_coupon['coupon_title'] = $coupon['title'];
                        $user_coupon['coupon_price'] = $coupon['coupon_price'];
                        $user_coupon['use_min_price'] = $coupon['use_min_price'];
                        $user_coupon['coupon_type'] = $coupon['coupon_type'];
                        $user_coupon['self_can_get'] = $coupon['self_can_get'];
                        $user_coupon['self_max_num'] = $coupon['self_max_num'];
                        $user_coupon['is_fail'] = 0;
                        $user_coupon['status'] = 0;
                        $user_coupon['coupon_products'] = $coupon['coupon_products'];
                        $user_coupon['coupon_discount'] = $coupon['coupon_discount'];
                        if ($coupon['time_type'] == 1) {
                            $user_coupon['coupon_long_time'] = $coupon['coupon_long_time'];
                            $user_coupon['coupon_start_time'] = $time;
                            $start_time = strtotime(date('Y-m-d', $time));
                            $user_coupon['coupon_end_time'] = $coupon['coupon_long_time'] * 3600 * 24 + $start_time - 1;
                        } elseif ($coupon['time_type'] == 2) {
                            $user_coupon['coupon_start_time'] = $time;
                            $user_coupon['coupon_end_time'] = $coupon['coupon_end_time'];
                        }
                        $user_coupon['add_time'] = $time;
                        StoreCouponUser::set($user_coupon);
                    }
                }
            }

        }
        return $wechatInfo;
    }

    /**
     * 拉新营销 给上级用户增加积分、余额
     * pyp
     */
    public static function updateSp($spreadUid)
    {
        $user = User::getUserBySpreadUid($spreadUid);
        if (!empty($user)) {
            $data = SystemPullNew::getPullNew();
            if (!empty($data) && isset($data['status']) && $data['status'] == 1){
                $now_money = bcadd($data['give_now_money'],$user['now_money'],2);
                $integral = bcadd($data['give_integral'],$user['integral'],2);
                User::edit(['now_money'=>$now_money,'integral'=>$integral],$user['uid']);

                //赠送积分明细
                UserBill::income('拉新营销',$user['uid'],'integral','pull_new',$data['give_integral'],0,$integral,'拉新营销赠送积分' . $data['give_integral']);

                //赠送余额明细
                UserBill::income('拉新营销',$user['uid'],'now_money','pull_new',$data['give_now_money'],0,$now_money,'拉新人赠送余额' . $data['give_now_money'] . '元');

                //赠送优惠券
                if ($data['give_coupon'] != ''){
                    $coupon_list = json_decode($data['give_coupon'],true);
                    foreach ($coupon_list as $k=>$v){
                        $coupon = StoreCoupon::get($k);
                        if ($coupon['status'] != 1) continue;
                        if ($coupon['is_del'] == 1) continue;
                        if ($coupon['is_limit'] == 1 && $coupon['coupon_num'] < 1) continue;
                        if ($coupon['time_type'] == 2 && $coupon['coupon_end_time'] < time()) continue;
                        if ($coupon['is_limit'] == 1 && $coupon['coupon_num']>0){
                            $num = $coupon['coupon_num'] >= $v ? $v : $coupon['coupon_num'];
                        }elseif ($coupon['is_limit'] == 0){
                            $num = $v;
                        }else{
                            continue;
                        }
                        for ($i = 0; $i < $num; $i++) {
                            $time = time();
                            $user_coupon['cid'] = $coupon['id'];
                            $user_coupon['uid'] = $user['uid'];
                            $user_coupon['type'] = 'new_gift_send';
                            $user_coupon['coupon_title'] = $coupon['title'];
                            $user_coupon['coupon_price'] = $coupon['coupon_price'];
                            $user_coupon['use_min_price'] = $coupon['use_min_price'];
                            $user_coupon['coupon_type'] = $coupon['coupon_type'];
                            $user_coupon['self_can_get'] = $coupon['self_can_get'];
                            $user_coupon['self_max_num'] = $coupon['self_max_num'];
                            $user_coupon['is_fail'] = 0;
                            $user_coupon['status'] = 0;
                            $user_coupon['coupon_products'] = $coupon['coupon_products'];
                            $user_coupon['coupon_discount'] = $coupon['coupon_discount'];
                            if ($coupon['time_type'] == 1) {
                                $user_coupon['coupon_long_time'] = $coupon['coupon_long_time'];
                                $user_coupon['coupon_start_time'] = $time;
                                $start_time = strtotime(date('Y-m-d', $time));
                                $user_coupon['coupon_end_time'] = $coupon['coupon_long_time'] * 3600 * 24 + $start_time - 1;
                            } elseif ($coupon['time_type'] == 2) {
                                $user_coupon['coupon_start_time'] = $time;
                                $user_coupon['coupon_end_time'] = $coupon['coupon_end_time'];
                            }
                            $user_coupon['add_time'] = $time;
                            StoreCouponUser::set($user_coupon);
                        }
                    }
                }
            }
        }
    }
}