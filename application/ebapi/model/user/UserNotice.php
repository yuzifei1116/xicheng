<?php


namespace app\ebapi\model\user;

use app\admin\model\user\UserNoticeSee;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 用户通知
 * Class UserNotice
 * @package app\ebapi\model\user
 */
class UserNotice extends ModelBasic
{
    use ModelTrait;
    public static function getNotice($uid){
        $count_notice = self::where('uid','like',"%,$uid,%")->where("is_send",1)->count();
        $see_notice = UserNoticeSee::where("uid",$uid)->count();
        return $count_notice-$see_notice;
    }
    /**
     * @return array
     */
    public static function getNoticeList($uid,$page,$limit){
        $list = self::where("is_send",1)
            ->field('id,title,send_time,content')
            ->page($page,$limit)
            ->order('send_time desc')
            ->select();
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['status'] = UserNoticeSee::where("uid",$uid)->where('nid',$v['id'])->count();
            $v['send_time'] = date('Y-m-d H:i:s',$v['send_time']);
        }
        return $list;
    }
    /**
     * @return array
     */
    public static function seeNotice($uid,$nid){
        if(UserNoticeSee::where("uid",$uid)->where("nid",$nid)->count() <= 0){
            $data["nid"] = $nid;
            $data["uid"] = $uid;
            $data["add_time"] = time();
            UserNoticeSee::set($data);
        }
    }
}