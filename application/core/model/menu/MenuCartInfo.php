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

class MenuCartInfo extends ModelBasic
{
    use ModelTrait;

    /**
     *
     */
    public static function getProductStatistics($where)
    {
        $data = self::alias('c')->join('menu m','c.menu_id=m.id','left')
            ->where('c.is_pay',1)
            ->field('m.menu_title,m.image,c.price,SUM(c.total_price) as total_price')
            ->order('m.id asc')
            ->group('c.menu_id')
            ->page($where['page'],$where['limit'])
            ->select();
        $count = self::alias('c')->join('menu m','c.menu_id=m.id','left')
            ->where('c.is_pay',1)
            ->group('c.menu_id')->count();
        return compact('count','data');
    }
}