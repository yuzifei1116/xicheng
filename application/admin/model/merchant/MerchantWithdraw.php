<?php

namespace app\admin\model\merchant;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class MerchantWithdraw extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public static function MerchantWithdrawList($where)
    {
        $data = self::setWhere($where,'w')->alias('w')
            ->page((int)$where['page'],(int)$where['limit'])
            ->order('add_time desc')
            ->select();
        $data = $data->isEmpty() ? [] : $data->toArray();
        foreach ($data as &$v){
            if ($v['status'] == 0){
                $v['_status'] = '申请中';
            }elseif ($v['status'] == 1){
                $v['_status'] = '申请成功';
            }elseif ($v['status'] == 2){
                $v['_status'] = '申请失败';
            }
        }
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
        if(isset($where['status']) && $where['status']!='') $model=$model->where("{$alert}status",$where['status']);
        if(isset($where['order_id']) && $where['order_id']!='') $model=$model->where("{$alert}order_id",$where['order_id']);
        return $model;
    }
}