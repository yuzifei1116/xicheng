<?php

namespace app\admin\controller\banner;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService;
use think\Db;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\banner\RoutineBanner as RoutineBannerModel;
use think\Url;

use app\admin\model\system\SystemAttachment;


/**
 * 轮播图
 * Class bannerBanner
 * @package app\admin\controller\banner
 */
class RoutineBanner extends AuthController
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
    public function banner_list(){
        return JsonService::successlayui(RoutineBannerModel::BannerList());
    }
    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show='',$id=''){
        ($is_show=='' || $id=='') && JsonService::fail('缺少参数');
        $res=RoutineBannerModel::where(['id'=>$id])->update(['is_show'=>(int)$is_show]);
        if($res){
            return JsonService::successful($is_show==1 ? '显示成功':'隐藏成功');
        }else{
            return JsonService::fail($is_show==1 ? '显示失败':'隐藏失败');
        }
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
        $res = Upload::image('file','images/'.date('Ymd'));
//        dump($res);die;
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
        if (empty($data['title'])) return JsonService::fail('标题不能为空');
        if (empty($data['img'])) return JsonService::fail('请上传图片');
        unset($data['file']);
//        dump($data);die;
        $data['add_time'] = time();
        $res = RoutineBannerModel::set($data);
        if ($res){
            return Json::successful('添加成功!');
        }else{
            return Json::fail('添加失败');
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
        $data = RoutineBannerModel::get($id);
        $this->assign('id',$id);
        $this->assign('data',$data);
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
        if (empty($data['title'])) return JsonService::fail('标题不能为空');
        if (empty($data['img'])) return JsonService::fail('请上传图片');
        $id = $data['id'];
        unset($data['file']);
        unset($data['id']);
//        dump($data);die;
        $res = RoutineBannerModel::edit($data,$id);
        if ($res){
            return Json::successful('修改成功!');
        }else{
            return Json::fail('修改失败');
        }
    }


    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!$id) return Json::successful('数据错误!');
        if(!RoutineBannerModel::del($id))
            return Json::fail(RoutineBannerModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }





}
