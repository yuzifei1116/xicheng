<?php

namespace app\admin\controller\order;

use Api\Express;
use app\admin\controller\AuthController;
use app\admin\model\merchant\MerchantGrade;
use app\admin\model\merchant\MerchantList;
use app\admin\model\merchant\MerchantPercentage;
use service\FormBuilder as Form;
use app\admin\model\order\StoreOrderStatus;
use app\admin\model\ump\StorePink;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use basic\ModelBasic;
use behavior\admin\OrderBehavior;
use behavior\wechat\PaymentBehavior;
use EasyWeChat\Core\Exception;
use service\CacheService;
use service\HookService;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UtilService as Util;
use service\JsonService as Json;
use think\Db;
use think\Request;
use think\Url;
use app\admin\model\order\StoreOrder as StoreOrderModel;
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
        $this->assign([
            'merchants'=>MerchantList::getMerchantList()
        ]);
        return $this->fetch();
    }

    /**
     * 获取订单列表
     * return json
     */
    public function merchant_percentage_list(){
        $where = Util::getMore([
            ['mer_id',0],
            ['order_id',''],
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(MerchantPercentage::MerchantPercentageList($where));
    }

}
