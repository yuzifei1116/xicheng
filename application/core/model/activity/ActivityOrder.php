<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\core\model\activity;


use app\core\model\user\User;
use app\core\model\user\UserBill;
use basic\ModelBasic;
use traits\ModelTrait;
use app\ebapi\model\user\WechatUser;
use app\ebapi\model\activity\Activity;
use app\core\util\MiniProgramService;

class ActivityOrder extends ModelBasic
{
    use ModelTrait;

    public static function getorderList($where)
    {
        $data = self::setwhere($where,self::alias('o')->join('activity a','o.activity_id=a.id','left'),'o.','a.')
            ->field('o.*,a.title,a.start_time,a.end_time')
            ->order('o.add_time desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        foreach ($data as $k=>&$v){
            $v['time'] = date('Y.m.d',$v['start_time']) . ' - ' . date('Y.m.d',$v['end_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            if ($v['paid'] == 0) {
                $v['_paid'] = '未支付';
            }else{
                $v['_paid'] = '已支付';
            }
            if ($v['status'] == 0) {
                $v['_status'] = '未使用';
            }else{
                $v['_status'] = '已使用';
            }
        }
        $count = self::setwhere($where,self::alias('o')->join('activity a','o.activity_id=a.id','left'),'o.','a.')->count();
        return compact('count','data');
    }

    public static function setWhere($where,$model=null,$o,$a)
    {
        if ($model === null) $model = new self();
        if (isset($where['status']) && $where['status'] != '') $model = $model->where($o.'status',$where['status']);
        if (isset($where['paid']) && $where['paid'] != '') $model = $model->where($o.'paid',$where['paid']);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where($a.'title','like',"%$where[title]%");
        if (isset($where['order_id']) && $where['order_id'] != '') $model = $model->where($o.'order_id',$where['order_id']);
        if (isset($where['add_time']) && $where['add_time'] != ''){
            list($startTime, $endTime) = explode(' - ', $where['add_time']);
            $model = $model->where($o.'add_time', '>', strtotime($startTime));
            $model = $model->where($o.'add_time', '<', strtotime($endTime)+24*3600);
        }
        return $model;
    }

    public static function OrderList($uid,$type = 0,$page,$limit)
    {
        $model = new self();
        $model = $model->alias('o')->join('activity a','o.activity_id=a.id','left');
        if ($type == 1) $model = $model->where('o.paid',0);
        if ($type == 2) $model = $model->where('o.paid',1)->where('o.status',0);
        if ($type == 3) $model = $model->where('o.paid',1)->where('o.status',1);
        if ($type == 4) $model = $model->where('o.paid',1)->where('o.status',2);
        $list = $model->where('o.uid',$uid)
            ->field('o.*,a.title,a.image,a.price')
            ->order('o.add_time desc')
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
        $res1 = UserBill::expend($uid,'购买门票','activity','wechat',$orderInfo['pay_price'],$orderInfo['id']);
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
        $res2 = User::where('uid',$orderInfo['uid'])->setInc('activity_count');
        return $res1 && $res2;
    }

    public static function getTwoCount()
    {
        $count = self::group('uid')->count(); //用户购买的次数
        $two_count = User::where('activity_count','>',1)->count();
        return bcdiv($two_count,$count,2)*100;
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


    /**
     *
     */
    public static function getProductStatistics($where)
    {
        $data = self::alias('o')->join('activity a','o.activity_id=a.id','left')
            ->where('o.paid',1)
            ->field('a.id,a.title,a.image,a.price,SUM(o.money) as total_price')
            ->order('a.id asc')
            ->group('o.activity_id')
            ->page($where['page'],$where['limit'])
            ->select();
        $count = self::alias('o')->join('activity a','o.activity_id=a.id','left')
            ->where('o.paid',1)
            ->group('o.activity_id')->count();
        return compact('count','data');
    }

     /**
     * 支付
     */
    public static function jsPay($orderId,$field = 'order_id')
    {
        if(is_string($orderId))
            $orderInfo = self::where($field,$orderId)->find();
        else
            $orderInfo = $orderId;
        if(!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if($orderInfo['paid']) exception('支付已支付!');
        if($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::getOpenId($orderInfo['uid']);

        //为了获取商品名称
        $store_name = Activity::where('id',$orderInfo->activity_id)->title;
        // $store_name = $orderInfo['store_names'];

        return MiniProgramService::jsPay($openid,$orderInfo['order_id'],$orderInfo['pay_price'],'productr',$store_name);
    }
}