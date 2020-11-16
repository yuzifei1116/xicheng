<?php

namespace app\admin\model\shop;

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
class ShopProduct extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public static function ProductList($where)
    {
        $data = self::setWhere($where,self::alias('p')->join('shop_category c','p.cate_id=c.id','left'),'p.')
            ->field('p.*,c.cate_name')
            ->where('p.is_del',0)
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $count=self::setWhere($where,self::alias('p')->join('shop_category c','p.cate_id=c.id','left'),'p.')->count();
        return compact('data','count');
    }

    public static function setWhere($where,$model=null,$p)
    {
        $model=is_null($model) ? new self() : $model;
        if(isset($where['store_name']) && $where['store_name']!='') $model=$model->where("{$p}store_name|id",'like',"%$where[store_name]%");
        if(isset($where['is_show']) && $where['is_show']!='') $model=$model->where("{$alias}is_show",$where['is_show']);
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
}