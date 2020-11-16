<?php

namespace behavior\wechat;

use app\admin\model\system\SystemNewGift;
use app\admin\model\system\SystemPullNew;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use app\wap\model\user\WechatUser;
use service\JsonService;
use think\Cache;
use think\Cookie;
use think\Request;

class UserBehavior
{
    /**
     * 公众号授权成功后
     * @param $userInfo
     */
    public static function gzhOauthAfter($spreadUid,$wechatInfo)
    {
        $openid = $wechatInfo['openid'];
//        echo "调试中";
//        var_dump($wechatInfo);
        $wechatInfo['nickname'] = filterEmoji($wechatInfo['nickname']);
        Cookie::set('is_login',1);
        if(isset($wechatInfo['unionid']) && $wechatInfo['unionid'] != '' && WechatUser::be(['unionid'=>$wechatInfo['unionid']])){
            WechatUser::edit($wechatInfo,$wechatInfo['unionid'],'unionid');
            $uid = WechatUser::where('unionid',$wechatInfo['unionid'])->value('uid');
            if(!User::be(['uid'=>$uid])){
                $wechatInfo = WechatUser::where('uid',$uid)->find();
                User::setWechatUser($wechatInfo);
            }else{
                User::updateWechatUser($wechatInfo,$uid);
            }
        }else if(WechatUser::be(['openid'=>$wechatInfo['openid']])){
            WechatUser::edit($wechatInfo,$wechatInfo['openid'],'openid');
            User::updateWechatUser($wechatInfo,WechatUser::openidToUid($wechatInfo['openid']));
        }else{
            if(isset($wechatInfo['subscribe_scene'])) unset($wechatInfo['subscribe_scene']);
            if(isset($wechatInfo['qr_scene'])) unset($wechatInfo['qr_scene']);
            if(isset($wechatInfo['qr_scene_str'])) unset($wechatInfo['qr_scene_str']);
            $wechatInfo = WechatUser::set($wechatInfo);

            //新人礼
            $data = SystemNewGift::getNewGift(); //查询新人礼
            if (!empty($data) && isset($data['status']) && $data['status'] == 1){ //不为空, status=1
                $wechatInfo['now_money'] = $data['give_now_money'];
                $wechatInfo['integral'] = $data['give_integral'];

                //赠送积分明细
                UserBill::income('新人礼',$wechatInfo['uid'],'integral','new_gift',$data['give_integral'],0,$data['give_integral'],'新人赠送积分' . $data['give_integral']);

                //赠送余额明细
                UserBill::income('新人礼',$wechatInfo['uid'],'now_money','new_gift',$data['give_now_money'],0,$data['give_now_money'],'新人赠送余额' . $data['give_now_money'] . '元');
            }else{
                $wechatInfo['now_money'] = 0;
                $wechatInfo['integral'] = 0;
            }
            User::setWechatUser($wechatInfo,$spreadUid);
        }
        User::where('uid',WechatUser::openidToUid($openid))
            ->limit(1)->update(['last_time'=>time(),'last_ip'=>Request::instance()->ip()]);
    }
    /**
     * 微信授权成功后
     * @param $userInfo
     */
    public static function wechatOauthAfter($openid,$wechatInfo)
    {
//        echo "调试中";
//        var_dump($wechatInfo);
        $wechatInfo['nickname'] = filterEmoji($wechatInfo['nickname']);
        Cookie::set('is_login',1);
        if(isset($wechatInfo['unionid']) && $wechatInfo['unionid'] != '' && WechatUser::be(['unionid'=>$wechatInfo['unionid']])){
            WechatUser::edit($wechatInfo,$wechatInfo['unionid'],'unionid');
            $uid = WechatUser::where('unionid',$wechatInfo['unionid'])->value('uid');
            if(!User::be(['uid'=>$uid])){
                $wechatInfo = WechatUser::where('uid',$uid)->find();
                User::setWechatUser($wechatInfo);
            }else{
                User::updateWechatUser($wechatInfo,$uid);
            }
        }else if(WechatUser::be(['openid'=>$wechatInfo['openid']])){
            WechatUser::edit($wechatInfo,$wechatInfo['openid'],'openid');
            User::updateWechatUser($wechatInfo,WechatUser::openidToUid($wechatInfo['openid']));
        }else{
            if(isset($wechatInfo['subscribe_scene'])) unset($wechatInfo['subscribe_scene']);
            if(isset($wechatInfo['qr_scene'])) unset($wechatInfo['qr_scene']);
            if(isset($wechatInfo['qr_scene_str'])) unset($wechatInfo['qr_scene_str']);
            $wechatInfo = WechatUser::set($wechatInfo);

            //新人礼
            $data = SystemNewGift::getNewGift(); //查询新人礼
            if (!empty($data) && isset($data['status']) && $data['status'] == 1){ //不为空, status=1
                $wechatInfo['now_money'] = $data['give_now_money'];
                $wechatInfo['integral'] = $data['give_integral'];

                //赠送积分明细
                UserBill::income('新人礼',$wechatInfo['uid'],'integral','new_gift',$data['give_integral'],0,$data['give_integral'],'新人赠送积分' . $data['give_integral']);

                //赠送余额明细
                UserBill::income('新人礼',$wechatInfo['uid'],'now_money','new_gift',$data['give_now_money'],0,$data['give_now_money'],'新人赠送余额' . $data['give_now_money'] . '元');
            }else{
                $wechatInfo['now_money'] = 0;
                $wechatInfo['integral'] = 0;
            }
            User::setWechatUser($wechatInfo);
        }
        User::where('uid',WechatUser::openidToUid($openid))
            ->limit(1)->update(['last_time'=>time(),'last_ip'=>Request::instance()->ip()]);
    }

    /**
     * 拉新营销 给上级用户增加积分、余额
     * @param $spreadUid 上级用户 uid
     * pyp
     */
    public static function updateSp($spreadUid)
    {
//        拉新营销
        if ($spreadUid) {
            $user = User::getUserBySpreadUid($spreadUid);
            if ($user) {
                $data = SystemPullNew::getPullNew();
                if (!empty($data) && isset($data['status']) && $data['status'] == 1){
                    $now_money = bcadd($data['give_now_money'],$user['now_money'],2);
                    $integral = bcadd($data['give_integral'],$user['integral'],2);
                    User::edit(['now_money'=>$now_money,'integral'=>$integral],$user['uid']);

                    //赠送积分明细
                    UserBill::income('拉新营销',$user['uid'],'integral','pull_new',$data['give_integral'],0,$integral,'拉新营销赠送积分' . $data['give_integral']);

                    //赠送余额明细
                    UserBill::income('拉新营销',$user['uid'],'now_money','pull_new',$data['give_now_money'],0,$now_money,'拉新人赠送余额' . $data['give_now_money'] . '元');
                }
            }
        }
    }

}