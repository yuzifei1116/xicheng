<?php

namespace app\seller\controller\shop;

use app\seller\controller\AuthController;
use app\seller\model\user\User;
use service\FormBuilder as Form;
use service\JsonService;
use service\PHPCSVServer;
use think\Db;
use service\UtilService as Util;
use service\UploadService as Upload;
use think\Request;
use app\seller\model\shop\ShopOrder as OrderModel;
use think\Url;

use app\seller\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\seller\controller\store
 */
class ShopOrder extends AuthController
{
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
    public function order_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['order_id',''],
            ['status',''],
        ]);
        return JsonService::successlayui(OrderModel::OrderList($where));
    }

    //导出Excel表格
    public function export(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['order_id',''],
            ['status',''],
        ]);
        $list = OrderModel::SaveExport($where);
        $headArr = [
            'user_name'=>'用户名',
            'store_name'=>'产品名称',
            'integral'=>'积分',
            'total_num'=>'数量',
            'total_integral'=>'总价格',
            'user_phone'=>'联系方式',
            'user_address'=>'详细地址',
            'mark'=>'备注',
            'delivery_name'=>'快递名称',
            'delivery_id'=>'快递单号',
            'add_time'=>'添加时间',
        ];
        PHPCSVServer::exportCommon('积分商城',$headArr, $list);
    }

    /**
     * 修改状态为已收货
     * @param $id
     * @return \think\response\Json|void
     */
    public function take_delivery($id){
        if(!$id) return $this->failed('数据不存在');
        $order = OrderModel::get($id);
        if(!$order) return Json::fail('数据不存在!');
        if($order['status'] == 2) return Json::fail('不能重复收货!');
        $data['status'] = 2;
        if(!OrderModel::edit($data,$id))
            return JsonService::failjson(OrderModel::getErrorInfo('收货失败,请稍候再试!'));
        else{
            return JsonService::successful('收货成功!');
        }
    }

    /**
     * 发货
     * @param $id
     *  express
     */
    public function deliver_goods($id){
        if(!$id) return $this->failed('数据不存在');
        $product = OrderModel::get($id);
        if(!$product) return JsonService::fail('数据不存在!');
        if($product['status'] == 0){
            $f = array();
            $f[] = Form::select('delivery_name','快递公司')->setOptions(function(){
                $list =  Db::name('express')->where('is_show',1)->order('sort DESC')->column('id,name');
                $menus = [];
                foreach ($list as $k=>$v){
                    $menus[] = ['value'=>$v,'label'=>$v];
                }
                return $menus;
            })->filterable(1);
            $f[] = Form::input('delivery_id','快递单号');
            $form = Form::make_post_form('修改订单',$f,Url::build('updateDeliveryGoods',array('id'=>$id)),2);
            $this->assign(compact('form'));
            return $this->fetch('public/form-builder');
        }
        else return $this->failedNotice('订单状态错误');
    }

    /**发货保存
     * @param Request $request
     * @param $id
     */
    public function updateDeliveryGoods(Request $request, $id){
        $data = Util::postMore([
            'delivery_name',
            'delivery_id',
        ],$request);
        $data['delivery_type'] = 'express';
        if(!$data['delivery_name']) return JsonService::failjson('请选择快递公司');
        if(!$data['delivery_id']) return JsonService::failjson('请输入快递单号');
        $data['delivery_id'] = '快递号:'.$data['delivery_id'];
        $data['status'] = 1;
        $res = OrderModel::edit($data,$id);
        if ($res){
            return JsonService::successfuljson('修改成功!');
        }else{
            return JsonService::failjson('修改失败');
        }
    }

    public function order_info($oid = '')
    {
        if(!$oid || !($orderInfo = OrderModel::get($oid)))
            return $this->failed('订单不存在!');
        $this->assign('orderInfo',$orderInfo);
        return $this->fetch();
    }
}
