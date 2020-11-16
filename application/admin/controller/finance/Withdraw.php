<?php

namespace app\admin\controller\finance;

use app\admin\controller\AuthController;
use app\admin\model\merchant\MerchantList;
use service\FormBuilder as Form;
use service\JsonService;
use think\Db;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use think\Request;
use think\Url;
use app\admin\model\merchant\MerchantWithdraw;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\seller\controller\store
 */
class Withdraw extends AuthController
{

    use CurdControllerTrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 异步查找产品
     *
     * @return json
     */
    public function merchant_withdraw_ist(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['status',''],
        ]);
        return JsonService::successlayui(MerchantWithdraw::MerchantWithdrawList($where));
    }

    /**
     * 审核通过
     * @param $id
     * @auth:pyp
     * @date:2020/6/13 18:06
     */
    public function yes($id)
    {
        if (!$id) return Json::fail('数据错误');
        $withdraw = MerchantWithdraw::get($id);
        if (empty($withdraw)) return Json::fail('数据错误');
        if ($withdraw['status'] != 0) return Json::fail('数据错误');
        $res = MerchantWithdraw::edit(['status'=>1],$id);
        if ($res){
            return Json::successful('操作成功');
        }else{
            return Json::fail('操作失败');
        }
    }

    /**
     * 拒绝
     * @param $id
     * @auth:pyp
     * @date:2020/6/13 18:07
     */
    public function no($id)
    {
        if (!$id) return Json::fail('数据错误');
        $withdraw = MerchantWithdraw::get($id);
        if (empty($withdraw)) return Json::fail('数据错误');
        if ($withdraw['status'] != 0) return Json::fail('数据错误');

        $timeout = 20; //保险时间
        $key = 'lock_withdraw_'.$id; //锁的名字
        $value = uniqid(); //锁的唯一值
        $is_lock = $this->getRedis()->set($key, $value, ['nx','ex'=>$timeout]);
        if (!$is_lock) return JsonService::failjson('人数过多，请稍后再试');
        //主逻辑
        $res = $this->no_do($id,$withdraw); //结算逻辑
        //删除锁
        $this->getRedis()->del($key);
        //返回结果
        if (!$res[0]){
            return JsonService::failjson($res[1]);
        }else{
            return JsonService::successfuljson($res[1]);
        }
    }

    public function no_do($id,$withdraw)
    {
        $merchant = MerchantList::get($withdraw['mer_id']);
        $res1 = MerchantList::edit(['price'=>bcadd($merchant['price'],$withdraw['money'],2)],$merchant['id']);
        $res2 = MerchantWithdraw::edit(['status'=>2],$id);
        $res = $res1 && $res2;
        if ($res){
            return [1,'操作成功'];
        }else{
            return [0,'操作失败'];
        }
    }
}
