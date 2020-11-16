<?php


namespace app\gzh\model\system;


use basic\ModelBasic;
use traits\ModelTrait;

class SystemPullNew extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取拉新礼
     * pyp
     */
    public static function getPullNew()
    {
        $data = self::find();
        return $data = empty($data) ? [] : $data->toArray();
    }
}