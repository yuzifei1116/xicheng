<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 10:13
 */

namespace app\gzh\model\activity;

use app\gzh\model\user\UserAddress;
use basic\ModelBasic;
use traits\ModelTrait;

class PrizeUser extends ModelBasic
{
    use ModelTrait;

    public static function prizeRecord($act_id,$userInfo,$result,$time)
    {
        $data['act_id'] = $act_id;
        $data['prize_id'] = $result['prize_id'];
        $data['prize_name'] = $result['prize_name'];
        $data['prize_image'] = $result['image'];
        $data['uid'] = $userInfo['uid'];
        $data['avatar'] = $userInfo['avatar'];
        $data['username'] = $userInfo['nickname'];
//        $data['tel'] = $userInfo['phone'];
//        $data['addr'] = UserAddress::getAddr($userInfo['uid']);
        $data['status'] = 1;
        $data['add_time'] = $time;
        return self::set($data);
    }

    public static function getPrizeUserCount($uid,$aid)
    {
        return self::where('uid',$uid)->where('act_id',$aid)->count();
    }


    /**
     * 获取某用户的中奖信息 列表
     * @param $uid
     * @param int $page
     * @param int $limit
     * @return array
     * @author: gyz
     * @Time: 2020/5/23 16:54
     */
    public static function getUserPrize($uid,$page=1,$limit=10)
    {
        $res = self::where('uid',$uid)
            ->field('id,prize_name,prize_image,status')
            ->page($page,$limit)
            ->select();

        $res = $res->isEmpty() ? [] : $res->toArray();
        return $res;
    }

    /**
     * 修改中奖信息 的地址。。。
     * @param $uid
     * @param $my_prize_id
     * @param $data
     * @return $this
     * @author: gyz
     * @Time: 2020/5/23 16:54
     */
    public static function updatePrizeInfo($uid,$my_prize_id,$data)
    {
        $res = self::where('uid',$uid)->where('id',$my_prize_id)->update($data);
        return $res;
    }


}