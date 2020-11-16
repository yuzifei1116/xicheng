<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/12
 * Time: 10:47
 */

namespace app\ebapi\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreVisit extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取用户浏览记录
     * @param $uid
     * @auth:pyp
     * @date:2020/5/12 10:49
     */
    public static function getUserStoreVisit($uid)
    {
        $data = self::alias('v')
            ->join('StoreProduct p','v.product_id=p.id','right')
            ->where('v.uid',$uid)
            ->field('p.id,p.image')
            ->order('v.add_time desc')
            ->limit(10)
            ->select();
        if ($data->isEmpty()) return [];
        return $data->toArray();
    }
}