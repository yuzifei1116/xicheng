<?php


namespace app\gzh\model\user;


use basic\ModelBasic;
use traits\ModelTrait;

class UserBill extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function income($title,$uid,$category,$type,$number,$link_id = 0,$balance = 0,$mark = '',$status = 1)
    {
        $pm = 1;
        return self::set(compact('title','uid','link_id','category','type','number','balance','mark','status','pm'));
    }

    public static function expend($title,$uid,$category,$type,$number,$link_id = 0,$balance = 0,$mark = '',$status = 1)
    {
        $pm = 0;
        return self::set(compact('title','uid','link_id','category','type','number','balance','mark','status','pm'));
    }

    public static function prizeUserBill($act_id,$uid,$use_score,$time)
    {
        $data['uid'] = $uid;
        $data['link_id'] = $act_id;
        $data['pm'] = 0;
        $data['title'] = '抽奖消耗';
        $data['category'] = 'operation';
        $data['type'] = 'prize';
        $data['number'] = $use_score;
        $data['status'] = 1;
        $data['mark'] = '抽奖消耗'.$use_score.'积分';
        $data['add_time'] = $time;
        return self::set($data);
    }
}