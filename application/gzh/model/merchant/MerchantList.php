<?php

namespace app\gzh\model\merchant;

use basic\ModelBasic;
use traits\ModelTrait;

class MerchantList extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }

    public static function getMerchantList($page,$limit)
    {
        $list = self::where('status',1)
            ->where('is_del',0)
            ->field('id,mer_name,province,city,county,address,image,info,customer_service')
            ->order('recommend desc,sort desc,id asc')
            ->page($page,$limit)
            ->select();
        if ($list->isEmpty()) return [];
        return $list->toArray();
    }

    /**
     * 获取正常商户的id
     * @return array
     * @author: gyz
     * @Time: 2020/6/8 17:59
     */
    public static function getCheckMerId()
    {
        $res = self::where('status',1)->column('id');
        return $res;
    }

    /**
     * 申请状态
     * @auth:pyp
     * @date:2020/6/9 10:17
     */
    public static function getUserMerchant($uid)
    {
        return self::where('uid',$uid)->find();
    }
}