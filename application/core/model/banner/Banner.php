<?php


namespace app\core\model\banner;

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class ArticleCategory
 * @package app\gzh\model
 */
class Banner extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取全部可展示轮播图
     * @return array
     * @author: gyz
     * @Time: 2020/4/28 17:40
     */
    public static function getAllShow()
    {
        $res = self::where('is_show',1)
            ->order('order desc')
            ->cache(60)
            ->select();
        if ($res->isEmpty()) return [];
        foreach ($res as &$v){
            $v['img'] = request()->domain() . $v['img'];
        }
        return $res->toArray();
    }

}