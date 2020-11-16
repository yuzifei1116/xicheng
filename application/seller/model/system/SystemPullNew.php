<?php


namespace app\seller\model\system;


use basic\ModelBasic;
use traits\ModelTrait;

class SystemPullNew extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取新人礼
     * pyp
     */
    public static function getPullNew()
    {
        $data = self::find();
        return $data = empty($data) ? [] : $data->toArray();
    }
}