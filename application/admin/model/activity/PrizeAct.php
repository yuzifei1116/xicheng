<?php

namespace app\admin\model\activity;
use app\admin\model\system\SystemUserLevel;
use traits\ModelTrait;
use basic\ModelBasic;
/**
 * model
 * Class User
 * @package app\admin\model\user
 */
class PrizeAct extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where,$alias='',$userAlias='u.',$model=null)
    {
        $model=is_null($model) ? new self() : $model;
        if($alias){
            $model=$model->alias($alias);
            $alias.='.';
        }
        if(isset($where['title']) && $where['title']!='') $model=$model->where("{$alias}title",$where['title']);
        if(isset($where['status']) && $where['status']!='') $model=$model->where("{$alias}status",$where['status']);
        return $model;
    }

    public static function getActList($where)
    {
        $data=self::setWhere($where)
            ->where('is_del',0)
            ->order('order desc,id desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();
        foreach ($data as $k=>&$v){
            $v['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
            $v['end_time'] = date('Y-m-d H:i:s',$v['end_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

}