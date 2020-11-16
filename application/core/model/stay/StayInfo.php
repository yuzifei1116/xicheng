<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\stay;


use app\core\model\user\User;
use app\core\model\user\UserBill;
use basic\ModelBasic;
use traits\ModelTrait;

class StayInfo extends ModelBasic
{
    use ModelTrait;

    public static function getInfoList($where)
    {
        $data = self::setWhere($where,self::alias('i')->join('stay_room r','i.room_id=r.id','left')->join('stay_type t','i.type_id=t.id','left'),'i.')
            ->field('i.*,r.price,t.title,t.image,t.status as type_status')
            ->order('i.add_time desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){
            $v['start_time'] = date('Y-m-d',$v['start_time'] );
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time'] );
            $v['check_in_time'] = date('Y-m-d H:i:s',$v['check_in_time'] );
            $v['check_out_time'] = date('Y-m-d H:i:s',$v['check_out_time'] );
            if ($v['paid'] == 0) {
                $v['_paid'] = '未支付';
            }else{
                $v['_paid'] = '已支付';
            }
        }
        $count = self::setWhere($where,self::alias('i')->join('stay_room r','i.room_id=r.id','left')->join('stay_type t','i.type_id=t.id','left'),'i.')->count();
        return compact('count','data');
    }

    public static function setWhere($where,$model=null,$i)
    {
        if ($model === null) $model = new self();
        if (isset($where['type_id']) && $where['type_id'] != 0) $model = $model->where($i.'type_id',$where['type_id']);
        if (isset($where['status']) && $where['status'] != '') $model = $model->where($i.'status',$where['status']);
        if (isset($where['paid']) && $where['paid'] != '') $model = $model->where($i.'paid',$where['paid']);
        if (isset($where['number']) && $where['number'] != '') $model = $model->where($i.'number',$where['number']);
        if (isset($where['name']) && $where['name'] != '') $model = $model->where($i.'name','like',"%$where[name]%");
        if (isset($where['add_time']) && $where['add_time'] != ''){
            list($startTime, $endTime) = explode(' - ', $where['add_time']);
            $model = $model->where($i.'add_time', '>', strtotime($startTime));
            $model = $model->where($i.'add_time', '<', strtotime($endTime)+24*3600);
        }
        return $model;
    }

    /**
     * 查询此房间是否被预定
     */
    public static function getReserveRoom($room_id,$start_time,$end_time,$num)
    {
        $list = self::where('room_id',$room_id)
            ->where('start_time','<=',time())
            ->where('status','in','0,1,2')
            ->field('id,room_id,start_time,end_time')
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();

        //把预定过的日期放进数组
        $days = []; //预定的日期数组
        foreach ($list as $k=>$v){
            $day_num = (int)(($v['end_time'] - $v['start_time']) / (3600*24)); //预定的几天
            for($i=0;$i<=$day_num;$i++){
                $date = date('Ymd',$v['start_time']+3600*24*$i);
                if (in_array($date,$days)) return false;
                $days[] = $date;
            }
        }

        //判断预定的日期在不在数组里 在返回false 不在就是可以预定 返回true
        for($i=0;$i<=$num;$i++){
            $date = date('Ymd',$start_time+3600*24*$i);
            if (in_array($date,$days)) return false;
        }
        return true;

    }

    public static function infoList($uid,$type,$page,$limit)
    {
        $model = new self();
        $model = $model->alias('i')->join('stay_room r','i.room_id=r.id','left');

        //0全部 1待付款 2待评论 3已完成
        if ($type == 1) $model = $model->where('i.paid',0);
        if ($type == 2) $model = $model->where('i.paid',1)->where('i.status','in','0,1,2,3');
        if ($type == 3) $model = $model->where('i.paid',1)->where('i.status',4);
        $list = $model->where('i.uid',$uid)
            ->field('i.*,r.title,r.image,r.price')
            ->order('i.add_time desc')
            ->page($page,$limit)
            ->select();
        $list = $list->isEmpty() ? [] : $list->toArray();
        foreach ($list as $k=>&$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $v['image'] = request()->domain() . $v['image'];
        }
        return $list;
    }

    /**
     * 微信支付 为 0元时 预售尾款不会为0
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayPrice($order_id,$uid){
        $orderInfo = self::where('uid',$uid)->where('order_id',$order_id)->find();
        if(!$orderInfo) return self::setErrorInfo('订单不存在!');
        if($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        self::beginTrans();
        $res1 = UserBill::expend($uid,'住宿','stay','wechat',$orderInfo['pay_price'],$orderInfo['id']);
        $res2 = self::paySuccess($orderInfo,$uid);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 回调
     */
    public static function paySuccess($orderInfo,$uid)
    {
        $res1 = self::edit(['paid'=>1,'pay_time'=>time()],$orderInfo['id']);
        $res2 = User::where('uid',$orderInfo['uid'])->setInc('stay_count');
        return $res1 && $res2;
    }

    /**
     *
     */
    public static function getProductStatistics($where)
    {
        $data = self::alias('i')->join('stay_room r','i.room_id=r.id','left')
            ->where('paid',1)
            ->field('r.id,r.number,r.title,r.image,r.price,SUM(i.money) as total_price')
            ->order('r.number asc')
            ->group('room_id')
            ->page($where['page'],$where['limit'])
            ->select();
        $count = self::alias('i')->join('stay_room r','i.room_id=r.id','left')
            ->where('paid',1)
            ->group('room_id')->count();
        return compact('count','data');
    }

    public static function getBadge($where){
        $model = self::where('paid',1);
        $total_price = self::getModelTime($where,$model)->sum('money');
        return [
            [
                'name'=>'收入总额',
                'field'=>'元',
                'count'=>$total_price,
                'background_color'=>'layui-bg-blue',
                'col'=>4
            ]
        ];
    }

    public static function getTwoCount()
    {
        $count = self::group('uid')->count(); //用户购买的次数
        $two_count = User::where('stay_count','>',1)->count();
        return bcdiv($two_count,$count,2)*100;
    }
}