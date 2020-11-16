<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\admin\controller\record;


use app\admin\controller\AuthController;
use service\JsonService;
use service\UtilService;
use app\core\model\user\UserBill as UserBillModel;

class UserBill extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function user_bill()
    {
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(UserBillModel::getUserBill($where));
    }
}