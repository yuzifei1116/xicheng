<?php


namespace app\ebapi\model\gzh;

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class ArticleCategory
 * @package app\gzh\model
 */
class Ad extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取全部可展示广告
     * @return array
     * @author: gyz
     * @Time: 2020/4/28 17:40
     */
    public static function getAllShow()
    {
        $res = self::where('is_show',1)
            ->order('order desc')
            ->cache(120)
            ->select();
        if ($res->isEmpty()) return [];
        return $res->toArray();
    }

}