<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\activity;


use basic\ModelBasic;
use traits\ModelTrait;

class Activity extends ModelBasic
{
    use ModelTrait;

    public static function getActivityList($where)
    {
        $data = self::order('id desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){
            $v['time'] = date('Y.m.d',$v['start_time']) . ' - ' . date('Y.m.d',$v['end_time']);
            if ($v['status'] == 0) {
                $v['_status'] = '未上线';
            }elseif ($v['status'] == 1){
                $v['_status'] = '已上线';
            }else{
                $v['_status'] = '已下线';
            }
        }
        $count = self::count();
        return compact('count','data');
    }

    /**
     *门票列表
     */
    public static function getTicketList($page,$limit)
    {
        $list = self::where('status',1)
            ->where('start_time','<',time())
            ->where('end_time','>',time())
            ->field('id,title,image,price,original_price,sales,ficti')
            ->order('start_time asc')
            ->page($page,$limit)
            ->cache('ticket_list_'.$page.$limit,60)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as &$v){
            $v['image'] = request()->domain().$v['image'];
        }
        return $list;
    }

    /**
     * 活动列表
     */
    public static function ActivityList()
    {
        return self::select();
    }
}