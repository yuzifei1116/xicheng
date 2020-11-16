<?php

namespace app\core\model\menu;

use basic\ModelBasic;
use traits\ModelTrait;

class MenuComment extends ModelBasic
{
    use ModelTrait;

    public static function commentList($where)
    {
        $data = self::setWhere($where,self::alias('c')->join('user u','c.uid=u.uid','left'),'c.','u.')
            ->field('c.*,u.nickname')
            ->order('add_time desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $count = self::setWhere($where,self::alias('c')->join('user u','c.uid=u.uid','left'),'c.','u.')->count();
        return compact('count','data');
    }

    public static function setWhere($where,$model=null,$c,$u)
    {
        if ($model === null) $model = new self();
        if (isset($where['nickname']) && $where['nickname'] != '') $model = $model->where($u.'nickname','like',"%$where[nickname]%");
        if (isset($where['add_time']) && $where['add_time'] != ''){
            list($startTime, $endTime) = explode(' - ', $where['add_time']);
            $model = $model->where($c.'add_time', '>', strtotime($startTime));
            $model = $model->where($c.'add_time', '<', strtotime($endTime)+24*3600);
        }
        return $model;
    }
}