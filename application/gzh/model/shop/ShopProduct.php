<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 10:13
 */

namespace app\gzh\model\shop;

use app\gzh\model\user\UserAddress;
use basic\ModelBasic;
use traits\ModelTrait;

class ShopProduct extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public static function getProductList($page,$limit)
    {
        $list = self::where('is_show',1)
            ->where('is_del',0)
            ->field('id,store_name,image,unit_name,integral,IFNULL(sales,0) + IFNULL(ficti,0) as sales')
            ->order('order desc, id desc')
            ->page($page,$limit)
            ->select();
        if ($list->isEmpty()) return [];
        return $list->toArray();
    }

    public static function getShopProduct($id)
    {
        $product = self::where('id',$id)->field('id,store_name,image,unit_name,integral')->find();
        return $product;
    }

    public static function decStock($id,$num)
    {
        return self::where('id',$id)->setDec('stock',$num);
    }

    public static function addSales($id,$num)
    {
        return self::where('id',$id)->setInc('stock',$num);
    }
}