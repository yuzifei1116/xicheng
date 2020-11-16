<?php

namespace app\seller\controller\order;

use app\seller\controller\AuthController;
use app\seller\model\merchant\MerchantList;
use app\seller\model\merchant\MerchantPercentage;

use service\JsonService;
use service\UtilService as Util;

/**
 * 订单管理控制器 同一个订单表放在一个控制器
 * Class StoreOrder
 * @package app\admin\controller\store
 */
class Percentage extends AuthController
{
    /**
     * @return mixed
     */
    public function index()
    {
        //所有店铺
        return $this->fetch();
    }

    /**
     * 获取订单列表
     * return json
     */
    public function merchant_percentage_list(){
        $where = Util::getMore([
            ['order_id',''],
            ['page',1],
            ['limit',20],
        ]);
        $mer_id = $this->sellerId;
        return JsonService::successlayui(MerchantPercentage::MerchantPercentageList($where,$mer_id));
    }

}
