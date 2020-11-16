<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\ad;


use basic\ModelBasic;
use traits\ModelTrait;

class Ad extends ModelBasic
{
    use ModelTrait;

    public static function getAd($id = 0)
    {
        $ad = self::where('is_show',1)->where('id',$id)->find();
        $ad['img'] = request()->domain().$ad['img'];
        return $ad;
    }
}