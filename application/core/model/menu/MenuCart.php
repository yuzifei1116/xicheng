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

class MenuCart extends ModelBasic
{
    use ModelTrait;

    public static function getCartList($uid)
    {
        $list = self::alias('c')->join('menu m','c.menu_id=m.id','left')
            ->where('uid',$uid)->where('num','>',0)->where('is_pay',0)
            ->where('m.status',1)->where('m.is_del',0)
            ->field('c.*,m.menu_title,m.image,m.price')
            ->order('id desc')
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as $k=>&$v){
            $v['image'] = request()->domain() . $v['image'];
        }
        return $list;
    }

    /**
     * 菜单信息
     */
    public static function getMenuInfo($uid)
    {
        $info['menu'] = self::alias('c')->join('menu m','c.menu_id=m.id','left')
            ->where('c.uid',$uid)->where('c.num','>',0)->where('c.is_pay',0)
            ->where('m.status',1)->where('m.is_del',0)
            ->field('c.*,m.menu_title,m.image,m.price')
            ->order('id desc')
            ->select();
        $info['total_price'] = 0;
        foreach ($info['menu'] as $v){
            $info['total_price'] += bcmul($v['num'],$v['price'],2);
            $v['image'] = request()->domain() . $v['image'];
        }
        return $info;
    }
}