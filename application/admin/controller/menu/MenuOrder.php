<?php

namespace app\admin\controller\menu;


use app\admin\controller\AuthController;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use think\Request;
use think\Url;
use app\core\model\menu\MenuOrder as MenuOrderModel;

class MenuOrder extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function order_list()
    {
        $where = UtilService::getMore([
            ['order_id',''],
            ['status',''],
            ['paid',''],
            ['add_time',''],
            ['page',1],
            ['limit',20]
        ]);
        return JsonService::successlayui(MenuOrderModel::getOrderList($where));
    }

    /**
     * 房间入住
     * @param $id
     * @throws \think\exception\DbException
     */
    public function check_in($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $info = StayInfoModel::get($id);
        if (empty($info)) return JsonService::fail('数据错误');
        if ($info['status'] != 0) return JsonService::fail('数据错误'); //0未入住  1已入住  2已退房  3已退款
        if (date('Y-m-d') != date('Y-m-d',$info['scheduled_time'])) return JsonService::fail('日期错误');
        if ($info['paid'] == 0) return JsonService::fail('订单未支付');

        //检查房间类型
        $type = StayTypeModel::get($info['type_id']);
        if (empty($type)) return JsonService::fail('数据错误');
        if ($type['status'] != 1) return JsonService::fail('此类型房间已被禁用'); //状态 0禁止 1正常

        //检查房间
        $room = StayRoomModel::get($info['room_id']);
        if (empty($room)) return JsonService::fail('数据错误');
        if ($room['status'] == 2) return JsonService::fail('此房间已被禁用'); //0未入住 1已入住 2禁用
        if ($room['status'] == 1) return JsonService::fail('数据错误'); //0未入住 1已入住 2禁用

        StayInfoModel::beginTrans();
        $res1 = StayInfoModel::edit(['status'=>1,'check_in_time'=>time()],$id);
        $res2 = StayRoomModel::edit(['status'=>1],$info['room_id']);
        $res = $res1 && $res2;
        StayInfoModel::checkTrans($res);
        if ($res){
            return JsonService::success('已成功入住');
        }else{
            return JsonService::fail('操作失败');
        }
    }

    /**
     * 退房
     * @param $id
     * @throws \think\exception\DbException
     */
    public function check_out($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $info = StayInfoModel::get($id);
        if (empty($info)) return JsonService::fail('数据错误');
        if ($info['status'] != 1) return JsonService::fail('数据错误'); //0未入住  1已入住  2已退房  3已退款
        if (date('Y-m-d') != date('Y-m-d',$info['scheduled_time'])) return JsonService::fail('日期错误');
        if ($info['paid'] == 0) return JsonService::fail('订单未支付');

        //检查房间
        $room = StayRoomModel::get($info['room_id']);
        if (empty($room)) return JsonService::fail('数据错误');
        if ($room['status'] != 1) return JsonService::fail('数据错误'); //0未入住 1已入住 2禁用

        StayInfoModel::beginTrans();
        $res1 = StayInfoModel::edit(['status'=>2,'check_out_time'=>time()],$id);
        $res2 = StayRoomModel::edit(['status'=>0],$info['room_id']);
        $res = $res1 && $res2;
        StayInfoModel::checkTrans($res);
        if ($res){
            return JsonService::success('退房成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}