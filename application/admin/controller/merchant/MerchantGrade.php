<?php

namespace app\admin\controller\merchant;

use app\admin\controller\AuthController;
use app\admin\model\store\StoreProduct;
use service\FormBuilder as Form;
use service\JsonService;
use service\PHPCSVServer;
use think\Db;
use service\UtilService as Util;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\merchant\MerchantGrade as MerchantGradeModel;
use think\Url;

use app\admin\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class MerchantGrade extends AuthController
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
    public function merchant_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(MerchantGradeModel::MerchantGradeList($where));
    }
    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_recommend($recommend='',$id=''){
        ($recommend=='' || $id=='') && JsonService::fail('缺少参数');
        $res=MerchantGradeModel::where(['id'=>$id])->update(['recommend'=>(int)$recommend]);
        if($res){
            return JsonService::successful('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_product($field='',$id='',$value=''){
        $field=='' || $id=='' || $value=='' && JsonService::fail('缺少参数');
        $res = MerchantGradeModel::edit([$field=>$value],$id);
        if($res)
            return JsonService::successfuljson('保存成功');
        else
            return JsonService::failjson('保存失败');
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $field = [
            Form::input('title','等级名称')->col(24),
//            Form::input('grade','商铺等级')->placeholder('数字越大等级越高')->col(8),
            Form::number('number','可发布商品数量')->min(0)->col(24),
            Form::number('price','收费标准')->min(0)->col(24),
            Form::number('percentage','抽成')->min(0)->col(24),
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
            ['title',''],
//            ['grade',0],
            ['number',0],
            ['price',0],
            ['percentage',100],
        ],$request);
        if(!$data['title']) return JsonService::failjson('请输入等级名称');
//        if($data['grade'] == '' || $data['grade'] < 0) return JsonService::failjson('请输入商铺等级');
        if($data['number'] == '' || $data['number'] < 0) return JsonService::failjson('请输入可发布商品数量');
        if($data['price'] == '' || $data['price'] < 0) return JsonService::failjson('请输入收费标准');
        if($data['percentage'] == '' || $data['percentage'] < 0) return JsonService::failjson('请输入抽成比例');
        if (MerchantGradeModel::getMerchantGrade($data['grade'])) return JsonService::failjson('商铺等级重复');
        $res=MerchantGradeModel::set($data);
        if ($res){
            return JsonService::successfuljson('添加等级成功!');
        }else{
            return JsonService::failjson('添加失败');
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $grade = MerchantGradeModel::get($id);
        if(!$grade) return JsonService::failjson('数据不存在!');
        $field = [
            Form::input('title','等级名称',$grade->getData('title'))->col(24),
//            Form::input('grade','商铺等级',$grade->getData('grade'))->placeholder('数字越大等级越高')->col(8),
            Form::number('number','可发布商品数量',$grade->getData('number'))->min(0)->col(24),
            Form::number('price','收费标准',$grade->getData('price'))->min(0)->col(24),
            Form::number('percentage','抽成',$grade->getData('percentage'))->min(0)->col(24),
        ];
        $form = Form::make_post_form('编辑等级',$field,Url::build('update',array('id'=>$id)),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            ['title',''],
//            ['grade',0],
            ['number',0],
            ['price',0],
            ['percentage',100],
        ],$request);
        if(!$data['title']) return JsonService::failjson('请输入等级名称');
//        if($data['grade'] == '' || $data['grade'] < 0) return JsonService::failjson('请输入商铺等级');
        if($data['number'] == '' || $data['number'] < 0) return JsonService::failjson('请输入可发布商品数量');
        if($data['price'] == '' || $data['price'] < 0) return JsonService::failjson('请输入收费标准');
        if($data['percentage'] == '' || $data['percentage'] < 0) return JsonService::failjson('请输入抽成比例');
//        if (MerchantGradeModel::getMerchantGrade($data['grade'])) return JsonService::failjson('商铺等级重复');
        $res=MerchantGradeModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改成功!');
        }else{
            return JsonService::failjson('修改失败');
        }
    }

    public function detail($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $merchant = MerchantGradeModel::get($id);
        if(!$merchant) return JsonService::fail('数据不存在!');
        $merchant['banner'] = json_decode($merchant['banner'],true);
        $this->assign('merchant',$merchant);
        return $this->fetch();
    }
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        if(!$id) return $this->failed('数据不存在');
        if(!MerchantGradeModel::be(['id'=>$id,'is_del'=>0])) return $this->failed('产品数据不存在');
        $res = MerchantGradeModel::edit(['is_del'=>1],$id);
        if ($res){
            return JsonService::successfuljson('删除成功');
        }else{
            return JsonService::failjson('删除失败');
        }

    }

    //导出Excel表格
    public function export(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['store_name',''],
            ['is_show',''],
        ]);
        $list = MerchantGradeModel::SaveExport($where);
        $headArr = [
            'store_name'=>'产品名称',
            'store_info'=>'产品简介',
            'integral'=>'积分',
            'stock'=>'库存',
            'sales'=>'销量',
//            'add_time'=>'添加时间',
        ];
        PHPCSVServer::exportCommon('积分商城',$headArr, $list);
    }

    public function agree($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $merchant = MerchantGradeModel::get($id);
        if (empty($merchant)) return JsonService::fail('数据错误');
        $res = MerchantGradeModel::edit(['status'=>1],$id);
        if ($res){
            return JsonService::successfuljson('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }

    public function noagree($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $merchant = MerchantGradeModel::get($id);
        if (empty($merchant)) return JsonService::fail('数据错误');
        $res = MerchantGradeModel::edit(['status'=>3],$id);
        if ($res){
            return JsonService::successfuljson('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}
