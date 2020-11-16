<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 10:13
 */

namespace app\gzh\model\activity;

use app\gzh\model\user\User;
use app\gzh\model\user\UserAddress;
use app\gzh\model\user\UserBill;
use basic\ModelBasic;
use traits\ModelTrait;

class PrizeList extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public static function getPrizeList($id)
    {
        return self::where('act_id',$id)->field('id,prize_name,image,order')->order('order desc')->select();
    }

    public static function prizeDraw($act_id,$userInfo,$detail,$conf)
    {
        //抽奖概率
//        $conf = self::prize_conf($act_id);
//        if (empty($conf)) return [];
        //获取抽奖结果
        $result = self::getPrizeDrawResult($conf);
        //判断是否减库存
        $is_limit = self::where('id',$result['prize_id'])->value('is_limit');
        $time = time();
        self::beginTrans();
        $res1 = $res3 = $res4 = 1;
        if ($is_limit==1) $res1 = self::where('id',$result['prize_id'])->setDec('number',1); //减库存数量
        $res2 = PrizeUser::prizeRecord($act_id,$userInfo,$result,$time);
        if ($detail['use_score']) {
            $res3 = UserBill::prizeUserBill($act_id,$userInfo['uid'],$detail['use_score'],$time);
            $integral = bcsub($userInfo['integral'],$detail['use_score'],2);
            $res4 = User::edit(['integral'=>$integral],$userInfo['uid']);
        }
        $res = $res1 && $res2 && $res3 && $res4;
        self::checkTrans($res);
        if ($res){
            return $result;
        }else{
            return $res;
        }

    }

    /**
     * 抽奖概率
     * @param $act_id
     * @return array
     * @auth:pyp
     * @date:2020/5/16 13:47
     */
    public static function prize_conf($act_id)
    {
        return self::where('act_id',$act_id)->where('is_limit=0 OR number>=1')->column('id,prize_name,image,probability');
    }

    /**
     * 获取抽奖结果
     * @param $conf
     * @auth:pyp
     * @date:2020/5/16 13:50
     */
    public static function getPrizeDrawResult($conf)
    {
        $result = array();
        foreach ($conf as $k=>$v){
            $arr[$k] = $v['probability'];
        }
        $probability_sum = array_sum($arr);
        asort($arr);
        foreach ($arr as $k => $v) {
            $rand = mt_rand(1,$probability_sum);
            if ($rand <= $v){
                $result['prize_id'] = $conf[$k]['id'];
                $result['prize_name'] = $conf[$k]['prize_name'];
                $result['image'] = $conf[$k]['image'];
                break;
            }else{
                $probability_sum -= $v;
            }
        }
        return $result;
    }
}