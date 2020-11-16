<?php

namespace app\admin\controller\record;


use app\admin\controller\AuthController;
use app\admin\model\order\StoreOrder;
use app\admin\model\store\StoreProduct;
use service\JsonService;
use service\UtilService as Util;

class ProductStatistics extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 获取销量
     */
    public function get_echarts_maxlist($data=''){
        return JsonService::successful(StoreProduct::getMaxList(compact('data')));
    }

    public function get_product_statistics()
    {
        $data = StoreProduct::getProductStatistics();
        return JsonService::success($data);
    }

    /**
     * 获取产品曲线图数据
     */
    public function get_echarts_product($type='',$data=''){
        dump(StoreProduct::getChatrdata($type,$data));die;
        return JsonService::successful(StoreProduct::getChatrdata($type,$data));
    }

    public function get_echarts_order()
    {
        return JsonService::successful(StoreOrder::getChatrOder());
    }
}
