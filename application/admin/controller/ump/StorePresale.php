<?php

namespace app\admin\controller\ump;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreProduct as ProductModel;
use think\Url;
use app\admin\model\ump\StorePresaleAttr;
use app\admin\model\ump\StorePresaleAttrResult;
use app\admin\model\ump\StorePresale as StorePresaleModel;
use app\admin\model\system\SystemAttachment;

/**
 * 限时预售  控制器
 * Class StorePresale
 * @package app\admin\controller\store
 */
class StorePresale extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = StorePresaleModel::class;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        $this->assign('countPresale',StorePresaleModel::getPresaleCount());
//        $this->assign('presaleId',StorePresaleModel::getPresaleIdAll());
        return $this->fetch();
    }
    public function save_excel(){
        $where=Util::getMore([
            ['status',''],
            ['store_name','']
        ]);
        StorePresaleModel::SaveExcel($where);
    }
    /**
     * 异步获取砍价数据
     */
    public function get_presale_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['status',''],
            ['title','']
        ]);
        $presaleList = StorePresaleModel::systemPage($where);
        if(is_object($presaleList['list'])) $presaleList['list'] = $presaleList['list']->toArray();
        $data = $presaleList['list']['data'];
//        foreach ($data as $k=>$v){
//            $data[$k]['_stop_time'] =$v['stop_time'] ?  date('Y/m/d H:i:s',$v['stop_time']) : '';
//        }
        return Json::successlayui(['count'=>$presaleList['list']['total'],'data'=>$data]);
    }

    public function get_presale_id(){
        return Json::successlayui(StorePresaleModel::getPresaleIdAll());
    }
    /**
     * 添加预售产品
     * @return form-builder
     */
    public function create($id=0)
    {
        if($id) $preinfo = StorePresaleModel::get($id);
        $f = array();
        $f[] = Form::input('title','产品标题',isset($preinfo)?$preinfo->title:'');
        $f[] = Form::input('info','预售活动简介',isset($preinfo)?$preinfo->info:'')->type('textarea');
        $f[] = Form::input('unit_name','单位',isset($preinfo)?$preinfo->unit_name:'')->placeholder('个、位');
        $f[] = Form::dateTimeRange('presale_time','预售时间',isset($preinfo)?$preinfo->start_time:'',isset($preinfo)?$preinfo->stop_time:'');
        $f[] = Form::dateTimeRange('weikuan_time','尾款支付时间',isset($preinfo)?$preinfo->wk_start_time:'',isset($preinfo)?$preinfo->wk_stop_time:'');
        $f[] = Form::frameImageOne('image','产品主图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),isset($preinfo)?$preinfo->image:'')->icon('image')->width('100%')->height('500px');
        $f[] = Form::frameImages('images','产品轮播图',Url::build('admin/widget.images/index',array('fodder'=>'images')),isset($preinfo)?json_decode($preinfo->images,1):[])->maxLength(5)->icon('images')->width('100%')->height('500px');
        $f[] = Form::number('price','定金',isset($preinfo)?$preinfo->price:'')->min(0)->col(12);
        $f[] = Form::number('pre_price','预售价格',isset($preinfo)?$preinfo->pre_price:'')->min(0)->col(12);
        $f[] = Form::number('ot_price','原价')->min(0)->col(12);
//        $f[] = Form::number('cost','成本价')->min(0)->col(12);
        $f[] = Form::number('stock','库存',isset($preinfo)?$preinfo->stock:99999)->min(0)->precision(0)->col(12);
        $f[] = Form::number('sales','销量',isset($preinfo)?$preinfo->sales:0)->min(0)->precision(0)->col(12);
        $f[] = Form::number('sort','排序',isset($preinfo)?$preinfo->sort:0)->col(12);
        $f[] = Form::number('limit_num','限购个数',isset($preinfo)?$preinfo->limit_num:0)->precision(0)->col(12);
//        $f[] = Form::number('give_integral','赠送积分')->min(0)->precision(0)->col(12);
        $f[] = Form::number('postage','邮费',isset($preinfo)?$preinfo->postage:0)->min(0)->col(12);
        $f[] = Form::radio('is_postage','是否包邮',isset($preinfo)?$preinfo->is_postage:1)->options([['label'=>'是','value'=>1],['label'=>'否','value'=>0]])->col(12);
//        $f[] = Form::radio('is_hot','热门推荐',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $f[] = Form::radio('status','活动状态',isset($preinfo)?$preinfo->status:1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $form = Form::make_post_form('预售',$f,Url::build('save',['id'=>$id]));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**保存预售产品
     * @param Request $request
     * @param int $id
     */
    public function save(Request $request,$id = 0)
    {
        $data = Util::postMore([
            'title',
            ['product_id',0],
            ['info',''],
            'unit_name',
            ['image',''],
            ['images',[]],
            'price',
            'pre_price',
            'ot_price',
            'cost',
            'sales',
            'stock',
            'sort',
//            'give_integral',
            'postage',
            ['presale_time',[]],
            ['weikuan_time',[]],
            ['is_postage',0],
            ['cost',0],
//            ['is_hot',0],
            ['status',0],
            ['limit_num',0]
        ],$request);
//        dump($data);die;
        if(!$data['title']) return Json::fail('请输入产品标题');
        if(!$data['unit_name']) return Json::fail('请输入产品单位');
//        var_dump($this->request->post());
        $data['start_time'] = strtotime($data['presale_time'][0]);
        $data['stop_time'] = strtotime($data['presale_time'][1]);
        if(empty($data['start_time'])) return Json::fail('请选预售时间');
        unset($data['presale_time']);
//        if(count($data['weikuan_time'])<1) return Json::fail('请选尾款支付时间');
        $data['wk_start_time'] = strtotime($data['weikuan_time'][0]);
        $data['wk_stop_time'] = strtotime($data['weikuan_time'][1]);
        if(empty($data['wk_start_time'])) return Json::fail('请选尾款支付时间');
        unset($data['weikuan_time']);
        if(!$data['image']) return Json::fail('请选择推荐图');
        if(count($data['images'])<1) return Json::fail('请选择轮播图');
        $data['images'] = json_encode($data['images']);
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品定金');
        if($data['pre_price'] == '' || $data['pre_price'] < 0) return Json::fail('请输入产品预售售价');
//        if($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入产品原售价');
//        if($data['cost'] == '' || $data['cost'] < 0) return Json::fail('请输入产品成本价');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
        $data['add_time'] = time();
        $data['mer_id'] = 0;
        if($data['limit_num']<1) return Json::fail('请输入限购个数');
        if($id){
            $product = StorePresaleModel::get($id);
            if(!$product) return Json::fail('数据不存在!');
            StorePresaleModel::edit($data,$id);
            return Json::successful('编辑成功!');
        }else{
            StorePresaleModel::set($data);
            return Json::successful('添加成功!');
        }

    }
    /** 开启预售 在普通商品操作那
     * @param $id
     * @return mixed|void
     */
    public function presale($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::input('title','产品标题',$product->getData('store_name'));
        $f[] = Form::hidden('product_id',$id);
        $f[] = Form::input('info','预售活动简介',$product->getData('store_info'))->type('textarea');
        $f[] = Form::input('unit_name','单位',$product->getData('unit_name'))->placeholder('个、位');
        $f[] = Form::dateTimeRange('presale_time','预售时间');
        $f[] = Form::dateTimeRange('weikuan_time','尾款支付时间');
        $f[] = Form::frameImageOne('image','产品主图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$product->getData('image'))->icon('image');
        $f[] = Form::frameImages('images','产品轮播图',Url::build('admin/widget.images/index',array('fodder'=>'images')),json_decode($product->getData('slider_image')))->maxLength(5)->icon('images');
        $f[] = Form::number('price','预售价')->min(0)->col(12);
        $f[] = Form::number('pre_price','定金')->min(0)->col(12);
//        $f[] = Form::number('ot_price','原价',$product->getData('price'))->min(0)->col(12);
//        $f[] = Form::number('cost','成本价',$product->getData('cost'))->min(0)->col(12);
        $f[] = Form::number('stock','库存',$product->getData('stock'))->min(0)->precision(0)->col(12);
        $f[] = Form::number('sales','销量',$product->getData('sales'))->min(0)->precision(0)->col(12);
        $f[] = Form::number('sort','排序',$product->getData('sort'))->col(12);
        $f[] = Form::number('limit_num','单次购买产品个数',1)->precision(0)->col(12);
//        $f[] = Form::number('give_integral','赠送积分',$product->getData('give_integral'))->min(0)->precision(0)->col(12);
        $f[] = Form::number('postage','邮费',$product->getData('postage'))->min(0)->col(12);
        $f[] = Form::radio('is_postage','是否包邮',$product->getData('is_postage'))->options([['label'=>'是','value'=>1],['label'=>'否','value'=>0]])->col(12);
//        $f[] = Form::radio('is_hot','热门推荐',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $f[] = Form::radio('status','活动状态',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
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
        $data['is_del'] = 1;
        if(!StorePresaleModel::edit($data,$id))
            return Json::fail(StorePresaleModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $presale = StorePresaleModel::get($id);
        if(!$presale) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>StorePresaleModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    public function change_field(Request $request,$id,$field){
        if(!$id) return $this->failed('数据不存在');
        $presale = StorePresaleModel::get($id);
        if(!$presale) return Json::fail('数据不存在!');
        $data['description'] = $request->post('description');
        $res = StorePresaleModel::edit($data,$id);
        if($res)
            return Json::successful('添加成功');
        else
            return Json::fail('添加失败');
    }


    /**
     * 修改预售产品状态
     * @param $status
     * @param int $id
     */
    public function set_presale_status($status,$id = 0){
        if(!$id) return Json::fail('参数错误');
        $res = StorePresaleModel::edit(['status'=>$status],$id);
        if($res) return Json::successful('修改成功');
        else return Json::fail('修改失败');
    }
}
