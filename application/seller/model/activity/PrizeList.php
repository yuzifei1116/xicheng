<?php

namespace app\seller\model\activity;
use app\seller\model\system\SystemUserLevel;
use traits\ModelTrait;
use basic\ModelBasic;
/**
 * model
 * Class User
 * @package app\seller\model\user
 */
class PrizeList extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where,$alias='',$userAlias='u.',$model=null)
    {
        $model=is_null($model) ? new self() : $model;
        if($alias){
            $model=$model->alias($alias);
            $alias.='.';
        }
        if(isset($where['prize_name']) && $where['prize_name']!='') $model=$model->where("{$alias}prize_name",$where['prize_name']);
        return $model;
    }

    public static function getPrizeList($where,$aid)
    {
        $data=self::setWhere($where,'p')
            ->join('PrizeAct a','p.act_id=a.id','left')
            ->where('p.act_id',$aid)
            ->field('p.*,a.title')
            ->order('order desc,id desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();
        foreach ($data as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

}