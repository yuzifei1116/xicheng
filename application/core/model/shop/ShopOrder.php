<?php

namespace app\core\model\shop;

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

    public static function getOderList($uid,$type = 0,$page,$limit)
    {
        $model = new self();
        $model = $model->alias('o')->join('shop_product p','o.product_id=p.id','left');
        if ($type == 1) $model = $model->where('o.status',0);
        if ($type == 2) $model = $model->where('o.status',1);
        if ($type == 3) $model = $model->where('o.status',2);
        $list = $model->where('uid',$uid)
            ->field('o.*,p.store_name,p.image,p.integral')
            ->order('o.add_time desc')
            ->page($page,$limit)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $v['image'] = request()->domain() . $v['image'];
        }
        return $list;
    }
}