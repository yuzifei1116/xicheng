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
class ShopOrder extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public static function OrderList($where)
    {
        $data = self::setWhere($where,'o')
            ->join('ShopProduct p','o.product_id=p.id','left')
            ->join('user u','o.uid=u.uid','left')
            ->field('o.*,p.store_name,p.image,p.integral,u.nickname,u.phone')
            ->order('o.add_time desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
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
        if(isset($where['order_id']) && $where['order_id']!='') $model=$model->where("{$alias}order_id",'like',"%$where[order_id]%");
        if(isset($where['status']) && $where['status']!='') $model=$model->where("{$alias}status",$where['status']);
        return $model;
    }

    public static function SaveExport($where)
    {
        $data = self::setWhere($where,'o')
            ->join('ShopProduct p','o.product_id=p.id','left')
            ->field('o.*,p.store_name,p.integral')
            ->order('o.add_time desc')
            ->select();
        if ($data->isEmpty()) return [];
        return $data->toArray();
    }
}