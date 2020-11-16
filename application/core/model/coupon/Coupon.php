<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------

namespace app\core\model\coupon;

use basic\ModelBasic;
use traits\ModelTrait;

class Coupon extends ModelBasic
{
    use ModelTrait;

    public static function couponList($where)
    {
        $data = self::where('is_del',0)
            ->order('sort desc,id asc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){ //0通用 1商城 2住宿 3活动 4点餐
            if ($v['type'] == 0){
                $v['_type'] = '通用';
            }elseif ($v['type'] == 1){
                $v['_type'] = '商城';
            }elseif ($v['type'] == 2){
                $v['_type'] = '住宿';
            }elseif ($v['type'] == 3){
                $v['_type'] = '活动';
            }elseif ($v['type'] == 4){
                $v['_type'] = '点餐';
            }
            $v['time'] = date('Y.m.d H:i:s',$v['start_time']) . ' - ' . date('Y.m.d H:i:s',$v['end_time']);
        }
        $count = self::count();
        return compact('count','data');
    }

    /**
     * @param $where
     * @return array
     */
    public static function systemPageCoupon($where){
        $model = new self;
        if($where['status'] != '')  $model = $model->where('status',$where['status']);
        if($where['title'] != '')  $model = $model->where('title','LIKE',"%$where[title]%");
        $model = $model->where('is_del',0);
        $model = $model->where('status',1);
        $model = $model->order('sort desc,id desc');
        $data = self::page($model,$where);
        foreach ($data['list'] as $k=>&$v){
            $v['time'] = date('Y.m.d H:i:s',$v['start_time']) . ' - ' . date('Y.m.d H:i:s',$v['end_time']);
            if ($v['type'] == 0){
                $v['_type'] = '通用';
            }elseif ($v['type'] == 1){
                $v['_type'] = '商城';
            }elseif ($v['type'] == 2){
                $v['_type'] = '住宿';
            }elseif ($v['type'] == 3){
                $v['_type'] = '活动';
            }elseif ($v['type'] == 4){
                $v['_type'] = '点餐';
            }
        }
        return $data;
    }
}