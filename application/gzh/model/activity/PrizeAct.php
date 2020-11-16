<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 10:13
 */

namespace app\gzh\model\activity;

use basic\ModelBasic;
use traits\ModelTrait;

class PrizeAct extends ModelBasic
{
    use ModelTrait;

    public static function getList($page,$limit)
    {
        $list = self::where('status',1)
            ->where('is_del',0)
            ->order('order desc')
            ->page($page,$limit)
            ->cache('activity_list_'.$page.'_'.$limit,30)
            ->select();
        if ($list->isEmpty()) return [];
        $list =  $list->toArray();
        foreach ($list as $k=>&$v){
            $v['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
            $v['end_time'] = date('Y-m-d H:i:s',$v['end_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        return $list;
    }

    public static function getActivityDetail($act_id)
    {
        $detail = self::where('is_del',0)->where('status',1)->where('id',$act_id)->find();
        if (empty($detail)) return [];
        $detail['start_time'] = date('Y-m-d H:i:s',$detail['start_time']);
        $detail['end_time'] = date('Y-m-d H:i:s',$detail['end_time']);
        $detail['add_time'] = date('Y-m-d H:i:s',$detail['add_time']);
        $detail['prize'] = PrizeList::getPrizeList($detail['id']);
        return $detail;
    }

    public static function getScore($id)
    {
        return self::where('id',$id)->value('use_score');
    }
}