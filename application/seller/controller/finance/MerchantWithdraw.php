<?php

namespace app\seller\controller\finance;

use app\seller\controller\AuthController;
use app\seller\model\merchant\MerchantGrade;
use app\seller\model\merchant\MerchantList;
use service\FormBuilder as Form;
use app\seller\model\store\StoreProductAttr;
use app\seller\model\store\StoreProductAttrResult;
use app\seller\model\store\StoreProductRelation;
use app\seller\model\system\SystemConfig;
use service\JsonService;
use think\Db;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\seller\model\store\StoreCategory as CategoryModel;
use app\seller\model\store\StoreProduct as ProductModel;
use think\Url;
use app\seller\model\merchant\MerchantWithdraw as MerchantWithdrawModel;

use app\seller\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\seller\controller\store
 */
class MerchantWithdraw extends AuthController
{

    use CurdControllerTrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->assign('money',MerchantList::getMerchantPrice($this->sellerId));
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
        $mer_id = $this->sellerId;
        return JsonService::successlayui(MerchantWithdrawModel::MerchantWithdrawList($where,$mer_id));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
//        $this->assign(['title'=>'添加产品','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
//        return $this->fetch('public/common_form');
        $field = [
            Form::number('money','提现金额')->min(100)->col(8),
            Form::input('bank_name','银行名称')->placeholder('如中国银行,农业银行等')->col(Form::col(24)),
            Form::input('account_bank','银行账号')->col(Form::col(24)),
            Form::input('account_name','银行账户名')->placeholder('开户人姓名')->col(Form::col(24)),
            Form::input('remark','提现备注'),
        ];
        $form = Form::make_post_form('添加产品',$field,Url::build('save'),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = Util::postMore([
            ['money',0],
            ['bank_name',''],
            ['account_bank',''],
            ['account_name',''],
            ['remark',''],
        ],$request);
        if ($data['money'] < 100) return Json::fail('提现金额最少为100元');
        $merchant_price = MerchantList::getMerchantPrice($this->sellerId);
        if ($merchant_price < $data['money']) return Json::fail('店铺资金不足'.$data['money'].'元');
        $data['add_time'] = time();
        $data['mer_id'] = $this->sellerId;
        MerchantWithdrawModel::beginTrans();
        $res1 = MerchantWithdrawModel::set($data);
        $res2 = MerchantList::edit(['price'=>bcsub($merchant_price,$data['money'],2)],$this->sellerId);
        $res = $res1 && $res2;
        MerchantWithdrawModel::checkTrans($res);
        if ($res){
            return Json::successful('申请成功!');
        }else{
           return Json::fail('申请失败');
        }

    }
}
