<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\stay;


use basic\ModelBasic;
use traits\ModelTrait;

class StayRoom extends ModelBasic
{
    use ModelTrait;

    public static function getRoomList($where)
    {
        $data = self::setWhere($where,self::alias('r')->join('stay_type t','r.type_id=t.id','left'),'r.')
            ->field('r.*,t.title as type_title,t.status as type_status')
            ->order('r.number asc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as &$v){
            if (!$v['breakfast']){
                $v['breakfast'] = '无';
            }else{
                $v['breakfast'] = '有';
            }
        }
        $count = self::setWhere($where,self::alias('r')->join('stay_type t','r.type_id=t.id','left'),'r.')->count();
        return compact('count','data');
    }

    public static function setWhere($where,$model=null,$r)
    {
        if ($model === null) $model = new self();
        if (isset($where['type_id']) && $where['type_id'] != 0) $model = $model->where($r.'type_id',$where['type_id']);
        if (isset($where['status']) && $where['status'] != '') $model = $model->where($r.'status',$where['status']);
        if (isset($where['number']) && $where['number'] != '') $model = $model->where($r.'number',$where['number']);
        return $model;
    }

    /**
     *房间列表
     */
    public static function roomList($type_id,$start_time,$end_time,$num,$page,$limit)
    {
        $model = new self();
        if ($type_id) $model = $model->where('type_id',$type_id);
        $list = $model->where('status','in','0,1')
            ->order('sort desc,id desc')
            ->page($page,$limit)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as $k=>&$v){
            $v['image'] = request()->domain().$v['image'];
            if (!$v['breakfast']){ //早餐
                $v['breakfast'] = '无';
            }else{
                $v['breakfast'] = '有';
            }
            $res = StayInfo::getReserveRoom($v['id'],$start_time,$end_time,$num); //查看此房间是否可以预定 预定返回true
            if (!$res){
                unset($list[$k]);
            }
        }
        return $list;
    }

    /**
     *房间列表
     */
    public static function roomPreferentialList($start_time,$end_time,$num,$page,$limit)
    {
        $list = self::where('status','in','0,1')
            ->where('preferential',1)
            ->order('sort desc,id desc')
            ->page($page,$limit)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as $k=>&$v){
            $v['image'] = request()->domain().$v['image'];
            if (!$v['breakfast']){
                $v['breakfast'] = '无';
            }else{
                $v['breakfast'] = '有';
            }
            $res = StayInfo::getReserveRoom($v['id'],$start_time,$end_time,$num); //查看此房间是否可以预定 预定返回true
            if (!$res){
                unset($list[$k]);
            }
        }
        return $list;
    }
}