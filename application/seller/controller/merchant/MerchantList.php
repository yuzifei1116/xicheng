<?php

namespace app\seller\controller\merchant;

use app\seller\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService;
use service\PHPCSVServer;
use think\Db;
use service\UtilService as Util;
use service\UploadService as Upload;
use think\Request;
use app\seller\model\merchant\MerchantList as MerchantModel;
use think\Url;

use app\seller\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\seller\controller\store
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
        $merchant = MerchantModel::get($this->sellerId);
        $merchant['banner'] = json_decode($merchant['banner'],true);
        $this->assign('merchant',$merchant);
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
        if ($id != $this->sellerId) return JsonService::failjson('数据错误');
        unset($data['id']);
        $merchant = MerchantModel::get($id);
        if(!$data['mer_name']) return JsonService::fail('请输入店铺名称');
        if(!$data['account']) return JsonService::fail('请输入店铺账号');
        if(!$data['old_pwd']){
            unset($data['old_pwd']);
            unset($data['pwd']);
            unset($data['re_pwd']);
        }else{
            if ($merchant['pwd'] != md5($data['old_pwd'])) return JsonService::fail('密码错误');
            if ($data['pwd'] != $data['re_pwd']) return JsonService::fail('密码不一致');
            unset($data['old_pwd']);
            unset($data['re_pwd']);
            $data['pwd'] = md5($data['pwd']);
        }

        if(!$data['image']) return JsonService::fail('请输入店铺账号');
        if(!$data['real_name']) return JsonService::fail('请输入店主姓名');
        if(!$data['info']) return JsonService::fail('请输入店铺简介');
        if(!$data['phone']) return JsonService::fail('请输入店主电话');
        if(!$data['address']) return JsonService::fail('请输入店铺具体地址');
        if(!$data['longitude']) return JsonService::fail('请输入店铺经度');
        if(!$data['latitude']) return JsonService::fail('请输入店铺纬度');
        if(!$data['customer_service']) return JsonService::fail('请输入店铺客服电话');
//        if(!$data['province']) unset($data['province']);
//        if(!$data['city']) unset($data['city']);
//        if(!$data['county']) unset($data['county']);
        if(!$data['address']) return JsonService::fail('请选择店铺具体地址');
        if(!$data['specific_address']) return JsonService::fail('请输入店铺具体地址');
        if (!isset($data['banner'])) return JsonService::fail('请上传轮播图');
        if(count($data['banner']) > 3) return JsonService::fail('最多上传3张轮播图');
        $data['banner'] = json_encode($data['banner']);
        $res = MerchantModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改成功!');
        }else{
            return JsonService::failjson('修改失败');
        }
    }
}
