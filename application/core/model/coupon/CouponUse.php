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

class CouponUse extends ModelBasic
{
    use ModelTrait;

    public static function couponUseList($where)
    {
        $data = self::setWhere($where,self::alias('l')->join('coupon c','l.coupon_id=c.id','left')->join('user u','l.uid=u.uid'),'l.','c.')
            ->field('l.*,c.title,c.type,c.start_time,c.end_time,u.nickname')
            ->order('l.add_time desc')
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

            if ($v['is_use'] == 0){
                $v['_is_use'] = '未使用';
            }else{
                $v['_is_use'] = '已使用';
            }
            $v['time'] = date('Y.m.d H:i:s',$v['start_time']) . ' - ' . date('Y.m.d H:i:s',$v['end_time']);
            $v['add_time'] = date('Y.m.d H:i:s',$v['add_time']);
        }
        $count = self::setWhere($where,self::alias('l')->join('coupon c','l.coupon_id=c.id','left')->join('user u','l.uid=u.uid'),'l.','c.')->count();
        return compact('count','data');
    }

    public static function setWhere($where,$model=null,$l,$c)
    {
        if ($model === null) $model = new self();
        if (isset($where['type']) && $where['type'] != '') $model = $model->where($c.'type',$where['type']);
        if (isset($where['is_use']) && $where['is_use'] != '') $model = $model->where($l.'is_use',$where['is_use']);
        if (isset($where['add_time']) && $where['add_time'] != ''){
            list($startTime, $endTime) = explode(' - ', $where['add_time']);
            $model = $model->where($l.'add_time', '>', strtotime($startTime));
            $model = $model->where($l.'add_time', '<', strtotime($endTime)+24*3600);
        }
        return $model;
    }

    public static function setCoupon($coupon,$user)
    {
        $data = array();
        $time = time();
        foreach ($user as $k=>$v){
            $data[$k]['coupon_id'] = $coupon['id'];
            $data[$k]['uid'] = $v;
            $data[$k]['is_use'] = 0;
            $data[$k]['add_time'] = $time;
        }
        $res = self::insertAll($data);
        return $res;
    }
}