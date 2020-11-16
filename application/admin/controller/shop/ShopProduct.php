<?php

namespace app\admin\controller\shop;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService;
use service\PHPCSVServer;
use think\Db;
use service\UtilService as Util;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\shop\ShopProduct as ProductModel;
use think\Url;
use app\admin\model\shop\ShopCategory;

use app\admin\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class ShopProduct extends AuthController
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
    public function product_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['store_name',''],
            ['is_show',''],
        ]);
        return JsonService::successlayui(ProductModel::ProductList($where));
    }
    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show='',$id=''){
        ($is_show=='' || $id=='') && JsonService::fail('缺少参数');
        $res=ProductModel::where(['id'=>$id])->update(['is_show'=>(int)$is_show]);
        if($res){
            return JsonService::successful($is_show==1 ? '上架成功':'下架成功');
        }else{
            return JsonService::fail($is_show==1 ? '上架失败':'下架失败');
        }
    }
    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_product($field='',$id='',$value=''){
        $field=='' || $id=='' || $value=='' && JsonService::fail('缺少参数');
        $res = ProductModel::edit([$field=>$value],$id);
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
//        $this->assign(['title'=>'添加产品','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
//        return $this->fetch('public/common_form');
        $field = [
            Form::select('cate_id','商品类型')->setOptions(function(){
                $list = ShopCategory::getCagteList(); //获取房间类型列表
                $menus = [];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['cate_name']];
                }
                return $menus;
            })->filterable(1),
            Form::input('store_name','产品名称')->col(Form::col(24)),
            Form::input('store_info','产品简介')->type('textarea'),
            Form::input('unit_name','产品单位','件'),
            Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')))->icon('image')->width('100%')->height('500px'),
            Form::frameImages('slider_image','产品轮播图(640*640px)',Url::build('admin/widget.images/index',array('fodder'=>'slider_image')))->maxLength(5)->icon('images')->width('100%')->height('500px')->spin(0),
            Form::number('integral','产品售价/积分')->min(0)->col(8),
            Form::number('sales','销量',0)->min(0)->precision(0)->col(8)->readonly(1),
            Form::number('ficti','虚拟销量')->min(0)->precision(0)->col(8),
            Form::number('stock','库存')->min(0)->precision(0)->col(8),
            Form::number('order','排序')->col(8),
            Form::radio('is_show','产品状态',1)->options([['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]])->col(8),
        ];
        $form = Form::make_post_form('添加产品',$field,Url::build('save'),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','store/product/'.date('Ymd'));
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
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = Util::postMore([
            ['cate_id',0],
            ['store_name',''],
            ['store_info',''],
            ['unit_name','件'],
            ['image',[]],
            ['slider_image',[]],
            ['integral',0],
            ['order',0],
            ['stock',100],
            'sales',
            ['ficti',0],
            ['is_show',0],
        ],$request);
        if(!$data['cate_id']) return JsonService::fail('请选择视频类型');
        if(!$data['store_name']) return JsonService::fail('请输入产品名称');
        if(count($data['image'])<1) return JsonService::fail('请上传产品图片');
        if(count($data['slider_image'])<1) return JsonService::fail('请上传产品轮播图');
        if($data['integral'] == '' || $data['integral'] < 0) return JsonService::fail('请输入产品售价');
        if($data['stock'] == '' || $data['stock'] < 0) return JsonService::fail('请输入库存');
        $data['image'] = $data['image'][0];
        $data['slider_image'] = json_encode($data['slider_image']);
        $data['add_time'] = time();
        $data['description'] = '';
        $res=ProductModel::set($data);
        if ($res){
            return JsonService::successful('添加产品成功!');
        }else{
            return JsonService::failjson('添加产品失败');
        }
    }


    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return JsonService::fail('数据不存在!');
        $this->assign([
            'content'=>ProductModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    public function change_field(Request $request, $id)
    {
        $data = Util::postMore([
            ['description','']
        ],$request);

        $res = ProductModel::edit($data,$id);
        if ($res){
            return JsonService::successful('保存成功!');
        }else{
            return JsonService::failjson('保存失败');
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
        $product = ProductModel::get($id);
        if(!$product) return JsonService::fail('数据不存在!');
        $field = [
            Form::select('cate_id','商品类型',(string)$product->getData('cate_id'))->setOptions(function() use($id){
                $list = ShopCategory::getCagteList(); //获取房间类型列表
                $menus = [];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['cate_name']];
                }
                return $menus;
            })->filterable(1),
            Form::input('store_name','产品名称',$product->getData('store_name')),
            Form::input('store_info','产品简介',$product->getData('store_info'))->type('textarea'),
            Form::input('unit_name','产品单位',$product->getData('unit_name')),
            Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')),$product->getData('image'))->icon('image')->width('100%'),
            Form::frameImages('slider_image','产品轮播图(640*640px)',Url::build('admin/widget.images/index',array('fodder'=>'slider_image')),json_decode($product->getData('slider_image'),1) ? : [])->maxLength(5)->icon('images')->width('100%')->height('500px'),
            Form::number('integral','产品售价/积分',$product->getData('integral'))->min(0)->precision(2)->col(8),
            Form::number('sales','销量',$product->getData('sales'))->min(0)->precision(0)->col(8)->readonly(1),
            Form::number('ficti','虚拟销量',$product->getData('ficti'))->min(0)->precision(0)->col(8),
            Form::number('stock','库存',$product->getData('stock'))->min(0)->precision(0)->col(8),
            Form::number('order','排序',$product->getData('order'))->col(8),
            Form::radio('is_show','产品状态',$product->getData('is_show'))->options([['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]])->col(8),
        ];
        $form = Form::make_post_form('编辑产品',$field,Url::build('update',array('id'=>$id)),2);
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
            ['cate_id',0],
            ['store_name',''],
            ['store_info',''],
            ['unit_name','件'],
            ['image',[]],
            ['slider_image',[]],
            ['integral',0],
            ['order',0],
            ['stock',100],
            'sales',
            ['ficti',0],
            ['is_show',0],
        ],$request);
        if(!$data['cate_id']) return JsonService::fail('请选择视频类型');
        if(!$data['store_name']) return JsonService::fail('请输入产品名称');
        if(count($data['image'])<1) return JsonService::fail('请上传产品图片');
        if(count($data['slider_image'])<1) return JsonService::fail('请上传产品轮播图');
        if(count($data['slider_image'])>5) return JsonService::fail('轮播图最多5张图');
        if($data['integral'] == '' || $data['integral'] < 0) return JsonService::fail('请输入产品售价');
        if($data['stock'] == '' || $data['stock'] < 0) return JsonService::fail('请输入库存');
        $data['image'] = $data['image'][0];
        $data['slider_image'] = json_encode($data['slider_image']);
        $res = ProductModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改成功!');
        }else{
            return JsonService::failjson('修改失败');
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

        if(!$id) return $this->failed('数据不存在');
        if(!ProductModel::be(['id'=>$id,'is_del'=>0])) return $this->failed('产品数据不存在');
        $res = ProductModel::edit(['is_del'=>1],$id);
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
        $list = ProductModel::SaveExport($where);
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
}
