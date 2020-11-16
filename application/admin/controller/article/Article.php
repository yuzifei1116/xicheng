<?php

namespace app\admin\controller\article;

use app\admin\controller\AuthController;
use app\admin\model\article\ArticleContent;
use service\FormBuilder as Form;
use service\JsonService;
use think\Db;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\article\ArticleCategory as CategoryModel;
use app\admin\model\article\Article as ArticleModel;
use think\Url;

use app\admin\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class Article extends AuthController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        //获取分类
        $this->assign('cate',CategoryModel::getCateList());
        return $this->fetch();
    }
    /**
     * 异步查找产品
     *
     * @return json
     */
    public function article_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['title',''],
            ['cid',0],
        ]);
        return JsonService::successlayui(ArticleModel::ArticleList($where));
    }
    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function hide($hide='',$id=''){
        ($hide=='' || $id=='') && JsonService::fail('缺少参数');
        $res=ArticleModel::edit(['hide'=>$hide],$id);
        if($res){
            return JsonService::successful($hide==1 ? '隐藏成功':'显示成功');
        }else{
            return JsonService::fail($hide==1 ? '隐藏失败':'显示失败');
        }
    }
    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_product($field='',$id='',$value=''){
        $field=='' || $id=='' || $value=='' && JsonService::fail('缺少参数');
        if(ArticleModel::where(['id'=>$id])->update([$field=>$value]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }
    /**
     * 设置批量产品上架
     *
     * @return json
     */
    public function product_show(){
        $post=Util::postMore([
            ['ids',[]]
        ]);
        if(empty($post['ids'])){
            return JsonService::fail('请选择需要上架的产品');
        }else{
            $res=ArticleModel::where('id','in',$post['ids'])->update(['is_show'=>1]);
            if($res)
                return JsonService::successful('上架成功');
            else
                return JsonService::fail('上架失败');
        }
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
            Form::select('cid','产品分类')->setOptions(function(){
                $list = CategoryModel::getCateList();
                $menus=[];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['title']];//,'disabled'=>$menu['pid']== 0];
                }
                return $menus;
            })->filterable(1)->multiple(0),
            Form::input('title','文章标题')->col(Form::col(24)),
            Form::input('author','作者')->col(Form::col(24)),
            Form::frameImageOne('image_input','图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image_input')))->icon('image')->width('100%')->height('500px'),
            Form::input('synopsis','简介')->col(24),
            Form::number('sort','排序')->col(24),
            Form::radio('hide','状态',0)->options([['label'=>'隐藏','value'=>1],['label'=>'显示','value'=>0]])->col(8),
        ];
        $form = Form::make_post_form('添加文章',$field,Url::build('save'),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','article'.date('Ymd'));
        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        SystemAttachment::attachmentAdd($res->fileInfo->getSaveName(),$fileInfo['size'],$fileInfo['type'],$res->dir,$thumbPath,1);
        if($res->status == 200)
            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
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
            ['cid',0],
            'title',
            'author',
            ['image_input',[]],
            ['synopsis',''],
            ['sort',0],
            ['hide',0],
        ],$request);
//        dump($data);die;
        if(!$data['cid']) return Json::fail('请选择文章分类');
        if(!$data['title']) return Json::fail('请输入文章标题');
        if(!$data['synopsis']) return Json::fail('请输入文章简介');
        if(count($data['image_input'])<1) return Json::fail('请上传文章图片');
        $data['image_input'] = $data['image_input'][0];
        $data['add_time'] = time();
        $res=ArticleModel::set($data);
        return Json::successful('添加文章成功!');
    }


    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $article = ArticleModel::get($id);
        if(!$article) return Json::fail('数据不存在!');

//        $res = Db::name('ArticleContent')->where('nid',$id)->value('content');
//        dump($res);
//        die;
        $this->assign([
            'content'=>Db::name('ArticleContent')->where('nid',$id)->value('content'),
            'field'=>'content',
            'action'=>Url::build('change_field',['nid'=>$id,'field'=>'content'])
        ]);
        return $this->fetch('public/edit_content');
    }

    public function change_field($nid = 0,$content = '')
    {
        if (!$nid) return Json::fail('数据错误');
        $article = ArticleModel::get($nid);
        if(!$article) return Json::fail('数据不存在!');
        $con = ArticleContent::get($nid);
        if(empty($con)){
            $res = ArticleContent::set(['nid'=>$nid,'content'=>$content]);
        }else{
            $res = ArticleContent::edit(['nid'=>$nid,'content'=>$content],$nid);
        }
//        echo ArticleContent::getLastSql();die;
        if ($res){
            return Json::successful('修改成功');
        }else{
            return Json::failjson('修改失败');
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
        $product = ArticleModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $field = [
            Form::select('cid','产品分类',explode(',',$product->getData('cid')))->setOptions(function(){
                $list = CategoryModel::getCateList();
                $menus=[];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['title']];//,'disabled'=>$menu['pid']== 0];
                }
                return $menus;
            })->filterable(1)->multiple(0),
            Form::input('title','文章标题',$product->getData('title')),
            Form::input('author','作者',$product->getData('author')),
            Form::frameImageOne('image_input','图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image_input')),$product->getData('image_input'))->icon('image')->width('100%'),
            Form::input('synopsis','简介',$product->getData('synopsis'))->col(24),
            Form::number('sort','排序',$product->getData('sort'))->col(24),
            Form::radio('hide','状态',$product->getData('hide'))->options([['label'=>'隐藏','value'=>1],['label'=>'显示','value'=>0]])->col(8),
        ];
        $form = Form::make_post_form('编辑',$field,Url::build('update',array('id'=>$id)),2);
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
            ['cid',0],
            'title',
            'author',
            ['image_input',[]],
            ['synopsis',''],
            ['sort',0],
            ['hide',0],
        ],$request);
        if(!$data['cid']) return Json::fail('请选择文章分类');
        if(!$data['title']) return Json::fail('请输入文章标题');
        if(!$data['synopsis']) return Json::fail('请输入文章简介');
        if(count($data['image_input'])<1) return Json::fail('请上传文章图片');
        $data['image_input'] = $data['image_input'][0];
        $res = ArticleModel::edit($data,$id);
        if ($res){
            return Json::successful('修改成功!');
        }else{
            return Json::failjson('修改失败');
        }
    }

    public function attr($id)
    {
        if(!$id) return $this->failed('数据不存在!');
        $result = StoreProductAttrResult::getResult($id);
        $image = ArticleModel::where('id',$id)->value('image');
        $this->assign(compact('id','result','image'));
        return $this->fetch();
    }
    /**
     * 生成属性
     * @param int $id
     */
    public function is_format_attr($id = 0){
        if(!$id) return Json::fail('产品不存在');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $product = ArticleModel::get($id);
        if(!$product) return Json::fail('产品不存在');
        $attrFormat = attrFormat($attr)[1];
        if(count($detail)){
            foreach ($attrFormat as $k=>$v){
                foreach ($detail as $kk=>$vv){
                    if($v['detail'] == $vv['detail']){
                        $attrFormat[$k]['price'] = $vv['price'];
                        $attrFormat[$k]['cost'] = isset($vv['cost']) ? $vv['cost'] : $product['cost'];
                        $attrFormat[$k]['sales'] = $vv['sales'];
                        $attrFormat[$k]['pic'] = $vv['pic'];
                        $attrFormat[$k]['check'] = false;
                        break;
                    }else{
                        $attrFormat[$k]['cost'] = $product['cost'];
                        $attrFormat[$k]['price'] = '';
                        $attrFormat[$k]['sales'] = '';
                        $attrFormat[$k]['pic'] = $product['image'];
                        $attrFormat[$k]['check'] = true;
                    }
                }
            }
        }else{
            foreach ($attrFormat as $k=>$v){
                $attrFormat[$k]['cost'] = $product['cost'];
                $attrFormat[$k]['price'] = $product['price'];
                $attrFormat[$k]['sales'] = $product['stock'];
                $attrFormat[$k]['pic'] = $product['image'];
                $attrFormat[$k]['check'] = false;
            }
        }
        return Json::successful($attrFormat);
    }

    public function set_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $res = StoreProductAttr::createProductAttr($attr,$detail,$id);
        if($res)
            return $this->successful('编辑属性成功!');
        else
            return $this->failed(StoreProductAttr::getErrorInfo());
    }

    public function clear_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        if(false !== StoreProductAttr::clearProductAttr($id) && false !== StoreProductAttrResult::clearResult($id))
            return $this->successful('清空产品属性成功!');
        else
            return $this->failed(StoreProductAttr::getErrorInfo('清空产品属性失败!'));
    }

    /**
     * 删除图文
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $res = ArticleModel::del($id);
        if(!$res)
            return Json::fail('删除失败,请稍候再试!');
        else
            return Json::successful('删除成功!');
    }




    /**
     * 点赞
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function collect($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ArticleModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign(StoreProductRelation::getCollect($id));
        return $this->fetch();
    }

    /**
     * 收藏
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function like($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ArticleModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign(StoreProductRelation::getLike($id));
        return $this->fetch();
    }
    /**
     * 修改产品价格
     * @param Request $request
     */
    public function edit_product_price(Request $request){
        $data = Util::postMore([
            ['id',0],
            ['price',0],
        ],$request);
        if(!$data['id']) return Json::fail('参数错误');
        $res = ArticleModel::edit(['price'=>$data['price']],$data['id']);
        if($res) return Json::successful('修改成功');
        else return Json::fail('修改失败');
    }

    /**
     * 修改产品库存
     * @param Request $request
     */
    public function edit_product_stock(Request $request){
        $data = Util::postMore([
            ['id',0],
            ['stock',0],
        ],$request);
        if(!$data['id']) return Json::fail('参数错误');
        $res = ArticleModel::edit(['stock'=>$data['stock']],$data['id']);
        if($res) return Json::successful('修改成功');
        else return Json::fail('修改失败');
    }



}
