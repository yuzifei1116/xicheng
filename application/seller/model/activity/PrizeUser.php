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
class PrizeUser extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where,$alias='',$model=null)
    {
        $model=is_null($model) ? new self() : $model;
        if($alias){
            $model=$model->alias($alias);
            $alias.='.';
        }
        if(isset($where['username']) && $where['username']!='') $model=$model->where("{$alias}username|uid",'like',"%$where[username]%");
        if(isset($where['status']) && $where['status']!='') $model=$model->where("{$alias}status",$where['status']);
        return $model;
    }

    public static function getPrizeUser($where,$aid)
    {
        $data=self::setWhere($where,'u')
            ->join('PrizeList p','u.prize_id=p.id','left')
            ->where('u.act_id',$aid)
            ->where('p.act_id',$aid)
            ->field('u.*,p.prize_name')
            ->order('u.id desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();
        foreach ($data as $k=>&$v){
            if ($v['status'] == 1){
                $v['_status'] = '未领取';
            }elseif ($v['status'] == 2){
                $v['_status'] = '已领取';
            }elseif ($v['status'] == 3){
                $v['_status'] = '作废';
            }
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

    public static function SaveExport($where)
    {
        $data=self::setWhere($where,'u')
            ->join('PrizeList p','u.prize_id=p.id','left')
            ->join('PrizeAct a','u.act_id=a.id','left')
            ->where('u.act_id',$where['aid'])
            ->where('p.act_id',$where['aid'])
            ->where('a.id',$where['aid'])
            ->field('u.*,p.prize_name,a.title')
            ->order('u.id desc')
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();
        foreach ($data as $k=>&$v){
            if ($v['status'] == 1){
                $v['_status'] = '未领取';
            }elseif ($v['status'] == 2){
                $v['_status'] = '已领取';
            }elseif ($v['status'] == 3){
                $v['_status'] = '作废';
            }
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }

        return $data;
    }
}