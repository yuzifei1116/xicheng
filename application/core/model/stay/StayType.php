<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------

namespace app\core\model\stay;

use basic\ModelBasic;
use traits\ModelTrait;

class StayType extends ModelBasic
{
    use ModelTrait;

    public static function getTypeList($where)
    {
        $data = self::order('sort desc,id asc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $count = self::count();
        return compact('count','data');
    }

    /**
     * 获取房间类型列表
     */
    public static function getRoomTypeList($type=1)
    {
        $model = new self();
        if ($type) $model = $model->where('status',1);
        $list = $model->order('sort desc')->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        return $list;
    }
}