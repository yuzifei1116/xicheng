<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 17:57
 */

namespace app\ebapi\model\user;

use traits\ModelTrait;
use basic\ModelBasic;


class UserNoticeSee extends ModelBasic
{
    use ModelTrait;

    public static function getReadCount($uid,$nid)
    {
        return self::where('uid',$uid)->where('nid',$nid)->count();
    }
}