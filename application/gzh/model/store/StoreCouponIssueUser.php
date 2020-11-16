<?php

namespace app\gzh\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreCouponIssueUser extends ModelBasic
{
    use ModelTrait;
    public static function addUserIssue($uid,$issue_coupon_id)
    {
        $add_time = time();
        return self::set(compact('uid','issue_coupon_id','add_time'));
    }
}