<?php

namespace app\seller\controller\widget;
use service\JsonService;
use think\Request;
use think\Url;
use app\seller\model\system\SystemAttachment as SystemAttachmentModel;
use app\seller\model\system\SystemAttachmentCategory as Category;
use app\seller\controller\AuthController;
use service\UploadService as Upload;
use service\UtilService as Util;
use service\FormBuilder as Form;

/**
 * 文件校验控制器
 * Class SystemFile
 * @package app\seller\controller\system
 *
 */
class Images extends AuthController
{
    /**
     * 附件列表
     * @return \think\response\Json
     */
   public function index()
   {
       $pid = 0;
       if(is_numeric(input('pid'))){
           $pid = input('pid');
           session('pid',$pid);
       }else{
           $pid = session('pid')?session('pid'):0;
       }
       $this->assign('pid',$pid);
       //分类标题
       $typearray = Category::getAll();
       $this->assign(compact('typearray'));
//       $typearray = self::dir;
//       $this->assign(compact('typearray'));
       $this->assign(SystemAttachmentModel::getAll($pid,$this->sellerId));
       return $this->fetch('widget/images');
   }
    /**
     * 图片管理上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $pid = input('pid')!= NULL ?input('pid'):session('pid');
        $res = Upload::image('file','attach'.DS.date('Y').DS.date('m').DS.date('d'));
        $thumbPath = '';
//        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        //入口是public需要替换图片路径
        if(strpos(PUBILC_PATH,'public') == false){
            $res->dir = str_replace('public/','',$res->dir);
        }
        SystemAttachmentModel::attachmentAdd($res->fileInfo->getSaveName(),$fileInfo['size'],$fileInfo['type'],$res->dir,$thumbPath,$pid,$this->sellerId);
        $info = array(
//            "originalName" => $fileInfo['name'],
//            "name" => $res->fileInfo->getSaveName(),
//            "url" => '.'.$res->dir,
//            "size" => $fileInfo['size'],
//            "type" => $fileInfo['type'],
//            "state" => "SUCCESS"
            'code' =>200,
            'msg'  =>'上传成功',
            'src'  =>$res->dir
        );
        echo json_encode($info);
    }

    /**
     * ajax 提交删除
     */
    public function delete(){
        $request = Request::instance();
        $post = $request->post();
        if(empty($post['imageid'])) JsonService::fail('还没选择要删除的图片呢？');
        foreach ($post['imageid'] as $v){
            self::deleteimganddata($v);
        }
        JsonService::successful('删除成功');
    }

    /**删除图片和数据记录
     * @param $att_id
     */
    public function deleteimganddata($att_id){
        $attinfo = SystemAttachmentModel::get($att_id)->toArray();
        if($attinfo){
            if ($attinfo['mer_id'] != $this->sellerId) return JsonService::failjson('数据错误');
            @unlink(ROOT_PATH.ltrim($attinfo['att_dir'],'.'));
            @unlink(ROOT_PATH.ltrim($attinfo['satt_dir'],'.'));
            SystemAttachmentModel::where(['att_id'=>$att_id])->delete();
        }
    }
    /**
     * 移动图片分类显示
     */
    public function moveimg($imgaes){

        $formbuider = [];
        $formbuider[] = Form::hidden('imgaes',$imgaes);
        $formbuider[] = Form::select('pid','选择分类')->setOptions(function (){
            $list = Category::getCateList();
            $options =  [['value'=>0,'label'=>'所有分类']];
            foreach ($list as $id=>$cateName){
                $options[] = ['label'=>$cateName['html'].$cateName['name'],'value'=>$cateName['id']];
            }
            return $options;
        })->filterable(1);
        $form = Form::make_post_form('分类',$formbuider,Url::build('moveImgCecate'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**移动图片分类操作
     * @param Request $request
     * @param $id
     */
    public function moveImgCecate(Request $request)
    {
        $data = Util::postMore([
            'pid',
            'imgaes'
        ],$request);
        $images = explode(',',$data['imgaes']);
        foreach ($images as $key=>$value){
            $image = SystemAttachmentModel::get(100);
            if (empty($image)) return JsonService::failjson('数据错误');
            if ($image['mer_id'] != $this->sellerId) return JsonService::failjson('数据错误');
        }
        if($data['imgaes'] == '') return JsonService::fail('请选择图片');
        if(!$data['pid']) return JsonService::fail('请选择分类');
        $res = SystemAttachmentModel::where('att_id','in',$data['imgaes'])->update(['pid'=>$data['pid']]);
        if($res)
            JsonService::successful('移动成功');
        else
            JsonService::fail('移动失败！');
    }
}
