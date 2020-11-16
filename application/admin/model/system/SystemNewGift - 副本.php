<?php


namespace app\admin\model\system;


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
        return $data = empty($data) ? [] : $data->toArray();
    }
}