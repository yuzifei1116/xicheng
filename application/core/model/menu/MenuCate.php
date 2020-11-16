<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\menu;


use basic\ModelBasic;
use traits\ModelTrait;

class MenuCate extends ModelBasic
{
    use ModelTrait;

    public static function CateList($where)
    {
        $data = self::order('sort desc,id desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $count = self::count();
        return compact('count','data');
    }

    /**
     * @param int type 0上架 1下架
     */
    public static function getMenuCateList($type = 0)
    {
        $model = new self();
        if ($type) $model = $model->where('status',1);
        $list = $model->order('sort desc')->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        return $list;
    }

    public static function getCateList()
    {
        return self::where('status',1)->order('sort desc')->field('id,cate_title')->select()->toArray();
    }
}