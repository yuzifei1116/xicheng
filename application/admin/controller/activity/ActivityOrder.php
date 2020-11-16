<?php

namespace app\admin\controller\activity;


use app\admin\controller\AuthController;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use app\core\model\activity\Activity as ActivityModel;
use app\core\model\activity\ActivityOrder as ActivityOrderModel;
use think\Request;
use think\Url;

class ActivityOrder extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function order_list()
    {
        $where = UtilService::getMore([
            ['order_id',''],
            ['title',''],
            ['paid',''],
            ['status',''],
            ['add_time',''],
            ['page',1],
            ['limit',20]
        ]);
        return JsonService::successlayui(ActivityOrderModel::getorderList($where));
    }

    /**
     * 使用
     */
    public function use_order($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $order = ActivityOrderModel::get($id);
        if (empty($order)) return JsonService::fail('数据错误');
        if ($order['status'] != 0) return JsonService::fail('该门票已使用'); //检查是否已使用
        if ($order['paid'] != 1) return JsonService::fail('该门票未支付,不能使用'); //检查是否已支付

        $activity = ActivityModel::get($order['activity_id']);
        if (empty($activity)) return JsonService::fail('数据错误');
        if ($activity['status'] != 1) return JsonService::fail('该活动已下线或未上线,不能使用');
        if (time() < $activity['start_time'] || time() >= $activity['end_time']) return JsonService::fail('该活动未开始或已结束,不能使用');

        $res = ActivityOrderModel::edit(['status'=>1],$id);
        if ($res){
            return JsonService::successful('操作成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }
}