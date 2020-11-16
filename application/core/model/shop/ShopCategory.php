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
class ShopCategory extends ModelBasic
{
    use ModelTrait;

    public static function getCagteList()
    {
        $list = self::where('is_del',0)
            ->order('sort desc')
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        return $list;
    }
}