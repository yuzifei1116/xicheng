<?php

namespace app\admin\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreProductAttrValue extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['unique'];

    protected function setSukAttr($value)
    {
        return is_array($value) ? implode(',',$value) : $value;
    }

    protected function setUniqueAttr($value,$data)
    {
        if(is_array($data['suk'])) $data['suk'] = $this->setSukAttr($data['suk']);
        return self::uniqueId($data['product_id'].$data['suk'].uniqid(true));
    }

    /*
     * 减少销量增加库存
     * */
    public static function incProductAttrStock($productId,$unique,$num)
    {
        return false !== self::where('product_id',$productId)->where('unique',$unique)->inc('stock',$num)->dec('sales',$num)->update();
    }

    public static function decProductAttrStock($productId,$unique,$num)
    {
        return false !== self::where('product_id',$productId)->where('unique',$unique)
            ->dec('stock',$num)->inc('sales',$num)->update();
    }


    public static function uniqueId($key)
    {
        return substr(md5($key),12,8);
    }

    public static function clearProductAttrValue($productId)
    {
        return self::where('product_id',$productId)->delete();
    }


}