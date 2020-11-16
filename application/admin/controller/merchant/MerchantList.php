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
use app\admin\model\merchant\MerchantList as MerchantModel;
use think\Url;
use app\admin\model\merchant\MerchantGrade;

use app\admin\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class MerchantList extends AuthController
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
            ['real_name',''],
            ['recommend',''],
            ['status',''],
        ]);
        return JsonService::successlayui(MerchantModel::MerchantList($where));
    }
    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_recommend($recommend='',$id=''){
        ($recommend=='' || $id=='') && JsonService::fail('缺少参数');
        $res=MerchantModel::where(['id'=>$id])->update(['recommend'=>(int)$recommend]);
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
        $res = MerchantModel::edit([$field=>$value],$id);
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
        return $this->fetch();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','merchant/logo/'.date('Ymd'));
        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        SystemAttachment::attachmentAdd($res->fileInfo->getSaveName(),$fileInfo['size'],$fileInfo['type'],$res->dir,$thumbPath,1);
        if($res->status == 200)
            return JsonService::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return JsonService::fail($res->error);
    }
    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload_banner()
    {
        $res = Upload::image('file','merchant/banner/'.date('Ymd'));
        if($res->status == true)
            return JsonService::successlayui(0,$res,'图片上传成功!');
        else
            return JsonService::fail($res->error);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = input('post.');
        if(!$data['mer_name']) return JsonService::fail('请输入店铺名称');
        if(!$data['account']) return JsonService::fail('请输入店铺账号');
        if(!$data['pwd']) return JsonService::fail('请输入店铺密码');
        $data['pwd'] = md5($data['pwd']);
        if(MerchantModel::be($data['account'],'account')) return JsonService::fail('店铺账号已存在');

//        if(!$data['info']) return JsonService::fail('请输入店铺名称');
//        if(count($data['image'])<1) return JsonService::fail('请上传店铺logo');
//        $data['image'] = $data['image'][0];
        $data['add_time'] = time();
        $res=MerchantModel::set($data);
        if ($res){
            return JsonService::successful('添加店铺成功!');
        }else{
            return JsonService::failjson('添加店铺失败');
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
        $merchant = MerchantModel::get($id);
        if(!$merchant) return JsonService::fail('数据不存在!');
        $merchant['banner'] = json_decode($merchant['banner'],true);
        $merchant_grade = MerchantGrade::getMerchantGradeName();
        $this->assign('merchant',$merchant);
        $this->assign('merchant_grade',$merchant_grade);
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        $data = input('post.');
        $id = $data['id'];
        unset($data['id']);
        $merchant = MerchantModel::get($id);
        if (!$merchant) return JsonService::failjson('数据错误');
        if(!$data['mer_name']) return JsonService::fail('请输入店铺名称');
        if(!$data['account']) return JsonService::fail('请输入店铺账号');
        if(!$data['pwd']) {
            unset($data['pwd']);
        }else{
            $data['pwd'] = md5($data['pwd']);
        }

        if(!$data['image']) return JsonService::fail('请上传店铺图片');
        if(!$data['real_name']) return JsonService::fail('请输入店主姓名');
        if(!$data['info']) return JsonService::fail('请输入店铺简介');
        if(!$data['phone']) return JsonService::fail('请输入店主电话');
//        if(!$data['province']) unset($data['province']);
//        if(!$data['city']) unset($data['city']);
//        if(!$data['county']) unset($data['county']);
        if(!$data['address']) return JsonService::fail('请选择店铺具体地址');
        if(!$data['specific_address']) return JsonService::fail('请输入店铺具体地址');
        if(!$data['longitude']) return JsonService::fail('请输入店铺经度');
        if(!$data['latitude']) return JsonService::fail('请输入店铺纬度');
        if(!$data['customer_service']) return JsonService::fail('请输入店铺客服电话');
        if (!isset($data['banner'])) return JsonService::fail('请上传轮播图');
        if(count($data['banner']) > 3) return JsonService::fail('最多上传3张轮播图');
        $data['banner'] = json_encode($data['banner']);
        MerchantModel::beginTrans();
        $res1 = true;
        if ($data['status'] == 0){
            $res1 = StoreProduct::edit(['mer_status'=>0],$id,'mer_id');
        }elseif($data['status'] == 1){
            $res1 = StoreProduct::edit(['mer_status'=>1],$id,'mer_id');
        }
        $res2 = MerchantModel::edit($data,$id);
        $res = $res1 && $res2;
        MerchantModel::checkTrans($res);
        if ($res1){
            return JsonService::successful('修改成功!');
        }else{
            return JsonService::failjson('修改失败');
        }
    }

    public function detail($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $merchant = MerchantModel::get($id);
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
        if(!MerchantModel::be(['id'=>$id,'is_del'=>0])) return $this->failed('产品数据不存在');
        $res = MerchantModel::edit(['is_del'=>1],$id);
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
        $list = MerchantModel::SaveExport($where);
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
        $merchant = MerchantModel::get($id);
        if (empty($merchant)) return JsonService::fail('数据错误');
        $res = MerchantModel::edit(['status'=>1],$id);
        if ($res){
            return JsonService::successfuljson('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }

    public function noagree($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $merchant = MerchantModel::get($id);
        if (empty($merchant)) return JsonService::fail('数据错误');
        $res = MerchantModel::edit(['status'=>3],$id);
        if ($res){
            return JsonService::successfuljson('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}
