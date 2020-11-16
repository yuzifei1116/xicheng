<?php

namespace app\admin\model\banner;

use traits\ModelTrait;
use basic\ModelBasic;


use app\admin\model\system\SystemConfig;

/**
 * 产品管理 model
 * Class BannerBanner
 * @package app\admin\model\banner
 */
class Banner extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    /*
     * 获取产品列表
     * @param $where array
     * @return array
     *
     */
    public static function BannerList(){
        $data = self::select();
        $count=self::count();
        return compact('count','data');
    }
}