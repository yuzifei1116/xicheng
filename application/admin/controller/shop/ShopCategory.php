<?php

namespace app\admin\controller\shop;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService;
use service\UtilService as Util;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\shop\ShopCategory as ShopCategoryModel;


/**
 * 文章分类管理  控制器
 * */
class ShopCategory extends AuthController

{

    /**
     * 分类管理
     * */
     public function index(){
         return $this->fetch();
     }

    /**
     * 异步查找产品
     *
     * @return json
     */
    public function cate_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(ShopCategoryModel::cateList($where));
    }

    /**

     * 添加分类管理

     * */

    public function create(){
        $f = array();
        $f[] = Form::input('cate_name','分类名称');
        $f[] = Form::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')))->width('100%')->height('500px')->icon('image');
        $f[] = Form::number('sort','排序',0);
        $form = Form::make_post_form('添加分类',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    /**

     * 保存分类管理

     * */

    public function save(Request $request)
    {
        $data = Util::postMore([
            'cate_name',
            ['image',[]],
            ['sort', 0],
        ], $request);
        if (!$data['cate_name']) return JsonService::fail('请输入分类名称');
//        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['sort'] <0 ) $data['sort'] = 0;
        $data['image'] = $data['image'][0];
        $data['add_time'] = time();
        $res = ShopCategoryModel::set($data);
        if ($res) {
            return JsonService::successful('添加类型成功!');
        } else {
            return JsonService::fail('添加类型失败!');
        }
    }

    /**

     * 修改分类

     * */

    public function edit($id){
        if(!$id) return $this->failed('参数错误');
        $cate = ShopCategoryModel::get($id)->getData();
        if(!$cate) return JsonService::fail('数据不存在!');
        $f = array();
        $f[] = Form::input('cate_name','分类名称',$cate['cate_name']);
        $f[] = Form::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$cate['image'])->width('100%')->height('500px')->icon('image');
        $f[] = Form::number('sort','排序',$cate['sort']);
        $form = Form::make_post_form('编辑分类',$f,Url::build('update',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'cate_name',
            ['image',[]],
            ['sort',0],
        ]);
        if(!$data['cate_name']) return JsonService::fail('请输入分类名称');
//        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['sort'] <0 ) $data['sort'] = 0;
        $data['image'] = $data['image'][0];
        $res = ShopCategoryModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改类型成功!');
        }else{
            return JsonService::fail('修改类型失败!');
        }
    }

    /**
     * 删除分类
     * */
    public function delete($id)
    {
        if (!$id) return JsonService::fail('参数错误');
        if (!ShopCategoryModel::get($id)) JsonService::fail('数据错误');
        $res = ShopCategoryModel::edit(['is_del'=>1],$id);
        if(!$res)
            return JsonService::fail('删除失败,请稍候再试!');
        else
            return JsonService::successful('删除成功!');
    }


}

