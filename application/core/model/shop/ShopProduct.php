<?php

namespace app\core\model\shop;

use service\JsonService;
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

    public static function getProductList($cate_id,$store_name,$page,$limit)
    {
        $model = self::where('is_del',0)
            ->where('is_show',1);
        if ($cate_id) $model = $model->where('cate_id',$cate_id);
        if ($store_name != '') $model = $model->where('store_name','like',"%$store_name%");

        $list = $model->order('order desc,id desc')
            ->page($page,$limit)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as &$v){
            $v['image'] = request()->domain() . $v['image'];
        }
        return $list;
    }
}