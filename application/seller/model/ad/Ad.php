<?php

namespace app\seller\model\ad;

use traits\ModelTrait;
use basic\ModelBasic;


use app\seller\model\system\SystemConfig;

/**
 * 产品管理 model
 * Class BannerBanner
 * @package app\seller\model\banner
 */
class Ad extends ModelBasic
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
    public static function AdList(){
        $data = self::select();
        $count=self::count();
        return compact('count','data');
    }
}