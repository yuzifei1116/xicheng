<?php

namespace app\admin\controller\menu;


use app\admin\controller\AuthController;
use app\core\model\menu\Menu as MenuModel;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use app\core\model\menu\MenuCate;
use think\Request;
use think\Url;

class Menu extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /*
     *  异步获取分类列表
     *  @return json
     */
    public function menu_list(){
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(MenuModel::MenuList($where));
    }

    /**
     * 添加
     */
    public function create()
    {
        $field = [
            FormBuilder::select('cate_id','菜品类型')->setOptions(function(){
                $list = MenuCate::getMenuCateList(0); //获取房间类型列表
                $menus = [];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['cate_title']];
                }
                return $menus;
            })->filterable(1)->required(),
            FormBuilder::input('menu_title','菜品名称')->col(24)->required(),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')))->width('100%')->height('500px')->icon('image'),
            FormBuilder::number('price','价格')->col(24)->required(),
            FormBuilder::number('original_price','原价')->col(24)->required(),
            FormBuilder::number('give_integral','赠送积分',0)->col(24),
            FormBuilder::number('sort','排序')->col(8),
            FormBuilder::radio('status','状态',1)->options([['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]])
        ];
        $form = FormBuilder::make_post_form('添加菜品',$field,Url::build('save'),2);
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
            ['cate_id',0],
            ['menu_title',''],
            ['image',[]],
            ['price',0],
            ['original_price',0],
            ['give_integral',0],
            ['sort',0],
            ['status',1],
        ],$request);
        if($data['cate_id'] == 0) return JsonService::fail('请选择菜品类型');
        if($data['menu_title'] == '') return JsonService::fail('请输入菜品名称');
        if(!$data['price']) return JsonService::fail('请输入菜品价格');
        if($data['price'] > $data['original_price']) return JsonService::fail('菜品价格不能大于原价');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['sort'] < 0) $data['sort'] = 0;
        if ($data['status'] == 1){
            $cate = MenuCate::get($data['cate_id']);
            if ($cate['status'] == 0) return JsonService::fail('此菜品不能选择上架');
        }
        $data['image'] = $data['image'][0];
        $data['add_time'] = time();
        $res = MenuModel::set($data);
        if ($res){
            return JsonService::successful('添加成功');
        }else{
            return JsonService::fail('添加失败');
        }
    }

    public function edit($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $menu = MenuModel::get($id);
        if (empty($menu)) return JsonService::fail('数据错误');
        $field = [
            FormBuilder::select('cate_id','菜品类型',(string)$menu->getData('cate_id'))->setOptions(function(){
                $list = MenuCate::getMenuCateList(0); //获取房间类型列表
                $menus = [];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['cate_title']];
                }
                return $menus;
            })->filterable(1)->required(),
            FormBuilder::input('menu_title','菜品名称',$menu->getData('menu_title'))->col(24)->required(),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$menu->getData('image'))->width('100%')->height('500px')->icon('image'),
            FormBuilder::number('price','价格',$menu->getData('price'))->col(24)->required(),
            FormBuilder::number('original_price','原价',$menu->getData('original_price'))->col(24)->required(),
            FormBuilder::number('give_integral','赠送积分',$menu->getData('give_integral'))->col(24),
            FormBuilder::number('sort','排序',$menu->getData('sort'))->col(8),
            FormBuilder::radio('status','状态',$menu->getData('status'))->options([['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]])
        ];
        $form = FormBuilder::make_post_form('修改',$field,Url::build('update',['id'=>$id]),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request,$id)
    {
        $data = UtilService::postMore([
            ['cate_id',0],
            ['menu_title',''],
            ['image',[]],
            ['price',0],
            ['original_price',0],
            ['give_integral',0],
            ['sort',0],
            ['status',1],
        ],$request);
        if($data['cate_id'] == 0) return JsonService::fail('请选择菜品类型');
        if($data['menu_title'] == '') return JsonService::fail('请输入菜品名称');
        if(!$data['price']) return JsonService::fail('请输入菜品价格');
        if($data['price'] > $data['original_price']) return JsonService::fail('菜品价格不能大于原价');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['sort'] < 0) $data['sort'] = 0;
        if ($data['status'] == 1){
            $cate = MenuCate::get($data['cate_id']);
            if ($cate['status'] == 0) return JsonService::fail('此菜品不能选择上架');
        }
        $data['image'] = $data['image'][0];
        $res = MenuModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改成功');
        }else{
            return JsonService::fail('修改失败');
        }
    }

    public function delete($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $menu = MenuModel::get($id);
        if (empty($menu)) return JsonService::fail('数据错误');
        $res = MenuModel::edit(['is_del'=>1],$id);
        if ($res){
            return JsonService::successful('删除成功');
        }else{
            return JsonService::fail('删除失败');
        }
    }

    public function set_status($id,$status)
    {
        if (!$id) return JsonService::fail('数据错误');
        $menu = MenuModel::get($id);
        if(empty($menu)) return JsonService::fail('数据不存在!');
        $cate = MenuCate::get($menu['cate_id']);
        if(empty($cate)) return JsonService::fail('数据不存在!');
        if ($status == 1 && $cate['status'] == 0) return JsonService::successful('此菜品不能上架');
        $res = MenuModel::edit(['status'=>$status],$id);
        if ($res){
            return JsonService::successful('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}