<?php


namespace app\gzh\model\system;


use basic\ModelBasic;
use traits\ModelTrait;

class SystemNewGift extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取新人礼
     * pyp
     */
    public static function getNewGift()
    {
        $data = self::find();
        if (empty($data)) return [];
        return $data->toArray();
    }
}