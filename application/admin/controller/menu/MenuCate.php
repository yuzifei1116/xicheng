<?php

namespace app\admin\controller\menu;


use app\admin\controller\AuthController;
use app\core\model\menu\Menu;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use app\core\model\menu\MenuCate as MenuCateModel;
use think\Request;
use think\Url;

class MenuCate extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /*
     *  异步获取分类列表
     *  @return json
     */
    public function cate_list(){
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(MenuCateModel::CateList($where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $field = [
            FormBuilder::input('cate_title','分类名称')->required(),
            FormBuilder::number('sort','排序'),
            FormBuilder::radio('status','状态',1)->options([['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]])
        ];
        $form = FormBuilder::make_post_form('添加分类',$field,Url::build('save'),2);
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
        $data = UtilService::postMore([
            'cate_title',
            'sort',
            ['status',1]
        ],$request);
        if(!$data['cate_title']) return JsonService::fail('请输入分类名称');
        $res = MenuCateModel::set($data);
        if ($res){
            return JsonService::successful('添加分类成功!');
        }else{
            return JsonService::fail('添加分类失败!');
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
        $c = MenuCateModel::get($id);
        if(!$c) return JsonService::fail('数据不存在!');
        $field = [
            FormBuilder::input('cate_title','分类名称',$c->getData('cate_title'))->required(),
            FormBuilder::number('sort','排序',$c->getData('sort')),
            FormBuilder::radio('status','状态',$c->getData('status'))->options([['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]])
        ];
        $form = FormBuilder::make_post_form('编辑分类',$field,Url::build('update',array('id'=>$id)),2);

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
        $data = UtilService::postMore([
            'cate_title',
            'sort',
            ['status',1]
        ],$request);
        if(!$data['cate_title']) return JsonService::fail('请输入分类名称');
        $res = MenuCateModel::edit($data,$id);
        if ($res){
            return JsonService::successful('编辑成功');
        }else{
            return JsonService::fail('编辑失败');
        }
    }

    public function delete($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $c = MenuCateModel::get($id);
        if(!$c) return JsonService::fail('数据不存在!');
        if (Menu::be(['cate_id'=>$c['id']])) return JsonService::fail('此分类不能删除');
        $res = MenuCateModel::del($id);
        if ($res){
            return JsonService::successful('删除成功');
        }else{
            return JsonService::fail('删除失败');
        }
    }

    public function set_status($id,$status)
    {
        if (!$id) return JsonService::fail('数据错误');
        $c = MenuCateModel::get($id);
        if(!$c) return JsonService::fail('数据不存在!');
        MenuCateModel::beginTrans();
        $res1 = MenuCateModel::edit(['status'=>$status],$id);
        if ($status == 0){
            $res2 = Menu::edit(['status'=>$status],$id,'cate_id');
        }else{
            $res2 = true;
        }

        $res = $res1 && $res2;
        MenuCateModel::checkTrans($res);
        if ($res){
            return JsonService::successful('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}