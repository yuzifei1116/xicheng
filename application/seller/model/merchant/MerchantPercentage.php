<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/6/13
 * Time: 11:14
 */

namespace app\seller\model\merchant;


use basic\ModelBasic;
use traits\ModelTrait;

class MerchantPercentage extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public static function MerchantPercentageList($where,$mer_id)
    {
        $data = self::setWhere($where,'p')->alias('p')
            ->join('MerchantList m','p.mer_id=m.id','LEFT')
            ->join('StoreOrder o','p.order_id=o.order_id','LEFT')
            ->where('p.mer_id',$mer_id)
            ->field('p.*,m.mer_name,o.order_id')
            ->page((int)$where['page'],(int)$where['limit'])
            ->order('add_time desc')
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();

        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

    /*
     * 获取查询条件
     * */
    public static function setWhere($where,$alert='',$model=null)
    {
        $model=$model===null ? new self() : $model;
        if($alert) $model=$model->alias($alert);
        $alert=$alert ? $alert.'.': '';
        if(isset($where['order_id']) && $where['order_id']!='') $model=$model->where("{$alert}order_id",$where['order_id']);
        return $model;
    }
}