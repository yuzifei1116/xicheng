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

class Menu extends ModelBasic
{
    use ModelTrait;

    public static function MenuList($where)
    {
        $data = self::alias('m')->join('menu_cate c','m.cate_id=c.id','left')
            ->where('m.is_del',0)
            ->field('m.*,c.cate_title')
            ->order('m.sort desc,id desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $count = self::alias('m')->join('menu_cate c','m.cate_id=c.id','left')->count();
        return compact('count','data');
    }

    /**
     *门票列表
     */
    public static function getMenuList($page,$limit)
    {
        $list = self::where('status',1)
            ->where('is_del',0)
            ->field('id,menu_title,image,price,original_price,sort')
            ->order('sort desc')
            ->page($page,$limit)
            ->cache('menu_list_'.$page.$limit,60)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as &$v){
            $v['image'] = request()->domain().$v['image'];
        }
        return $list;
    }
}