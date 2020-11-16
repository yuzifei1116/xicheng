<?php

namespace app\seller\model\merchant;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class MerchantGrade extends ModelBasic
{
    use ModelTrait;

    /**
     * 可发布商品数量
     * @param $grade
     * @return mixed
     * @auth:pyp
     * @date:DATE
     */
    public static function getNumber($id)
    {
        return self::where('id',$id)->value('number');
    }

    /**
     * 获取该商铺等级的抽成
     * @param $grade
     * @auth:pyp
     * @date:2020/6/13 10:06
     */
    public static function getMerchantGradePercentage($grade)
    {
        return self::where('grade',$grade)->value('percentage');
    }
}