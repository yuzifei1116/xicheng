<?php

namespace app\admin\model\merchant;

use service\PHPExcelService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\store\StoreCategory as CategoryModel;
use app\admin\model\order\StoreOrder;
use app\admin\model\system\SystemConfig;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class MerchantList extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public static function MerchantList($where)
    {
        $data = self::setWhere($where)
            ->where('is_del',0)
            ->order('recommend desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as &$v){
            if ($v['status'] == 0){
                $v['_status'] = '禁止';
            }elseif($v['status'] == 1){
                $v['_status'] = '正常';
            }elseif($v['status'] == 2){
                $v['_status'] = '审核中';
            }elseif ($v['status'] == 3){
                $v['_status'] = '未通过';
            }
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

    public static function setWhere($where,$alias='',$model=null)
    {
        $model=is_null($model) ? new self() : $model;
        if($alias){
            $model=$model->alias($alias);
            $alias.='.';
        }
        if(isset($where['real_name']) && $where['real_name']!='') $model=$model->where("{$alias}real_name",$where['real_name']);
        if(isset($where['recommend']) && $where['recommend']!='') $model=$model->where("{$alias}recommend",$where['recommend']);
        if(isset($where['status']) && $where['status']!='') $model=$model->where("{$alias}status",$where['status']);
        return $model;
    }

    public static function SaveExport($where)
    {
        $data = self::setWhere($where)
            ->where('is_del',0)
            ->select();
        if ($data->isEmpty()) return [];
        return $data->toArray();
    }

    public static function getMerchantList()
    {
        $list = self::where('is_del',0)->where('status',1)->field('id,mer_name')->select();
        if ($list->isEmpty()) return [];
        return $list->toArray();
    }

    public static function getGrade($id)
    {
        return self::where('id',$id)->value('grade');
    }
}