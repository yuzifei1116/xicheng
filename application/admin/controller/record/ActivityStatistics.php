<?php

namespace app\admin\controller\record;


use app\admin\controller\AuthController;
use app\admin\model\order\StoreOrder;
use app\admin\model\store\StoreProduct;
use app\core\model\activity\ActivityOrder;
use app\core\model\stay\StayInfo;
use service\JsonService;
use service\UtilService;
use service\UtilService as Util;

class ActivityStatistics extends AuthController
{
    public function index()
    {
        $this->assign([
            'year'=>getMonth('y'),
            'two_count'=>ActivityOrder::getTwoCount() //获取二次购买率
        ]);
        return $this->fetch();
    }

    public function stay_statistics()
    {
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20],
        ]);
        $data = ActivityOrder::getProductStatistics($where);
        return JsonService::successlayui($data);
    }

    /**
     * 获取头部订单金额等信息
     * return json
     *
     */
    public function getBadge(){
        $where = Util::postMore([
            ['status',''],
            ['real_name',''],
            ['is_del',0],
            ['data',''],
            ['type',''],
            ['order','']
        ]);
        return JsonService::successful(ActivityOrder::getBadge($where));
    }
}
