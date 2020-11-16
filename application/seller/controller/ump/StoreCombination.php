<?php

namespace app\seller\controller\ump;

use app\seller\controller\AuthController;
use service\FormBuilder as Form;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService;
use service\UploadService as Upload;
use think\Request;
use app\seller\model\store\StoreProduct as ProductModel;
use think\Url;
use app\seller\model\ump\StoreCombinationAttr;
use app\seller\model\ump\StoreCombinationAttrResult;
use app\seller\model\ump\StoreCombination as StoreCombinationModel;
use app\seller\model\system\SystemAttachment;

/**
 * 限时拼团  控制器
 * Class StoreCombination
 * @package app\seller\controller\store
 */
class StoreCombination extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = StoreCombinationModel::class;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        $this->assign('countCombination',StoreCombinationModel::getCombinationCount());
//        $this->assign('combinationId',StoreCombinationModel::getCombinationIdAll());
        return $this->fetch();
    }
    public function save_excel(){
        $where=Util::getMore([
            ['status',''],
            ['store_name','']
        ]);
        StoreCombinationModel::SaveExcel($where,$this->sellerId);
    }
    /**
     * 异步获取砍价数据
     */
    public function get_combination_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['status',''],
            ['com_type',1],
            ['title','']
        ]);
        $combinationList = StoreCombinationModel::systemPage($where,$this->sellerId);
        if(is_object($combinationList['list'])) $combinationList['list'] = $combinationList['list']->toArray();
        $data = $combinationList['list']['data'];
//        foreach ($data as $k=>$v){
//            $data[$k]['_stop_time'] =$v['stop_time'] ?  date('Y/m/d H:i:s',$v['stop_time']) : '';
//        }
        return JsonService::successlayui(['count'=>$combinationList['list']['total'],'data'=>$data]);
    }

    public function get_combination_id(){
        return JsonService::successlayui(StoreCombinationModel::getCombinationIdAll());
    }
    /**
     * 添加拼团产品
     * @return form-builder
     */
    public function create($id=0)
    {
        if($id) $preinfo = StoreCombinationModel::get($id);
        $f = array();
        $f[] = Form::input('title','产品标题',isset($preinfo)?$preinfo->title:'');
//        $f[] = Form::input('info','活动简介',isset($preinfo)?$preinfo->info:'')->type('textarea');
//        $f[] = Form::input('unit_name','单位',isset($preinfo)?$preinfo->unit_name:'')->placeholder('个、位');
        $f[] = Form::dateTimeRange('combination_time','拼团时间',isset($preinfo)?$preinfo->start_time:'',isset($preinfo)?$preinfo->stop_time:'');
//        $f[] = Form::dateTimeRange('weikuan_time','尾款支付时间',isset($preinfo)?$preinfo->wk_start_time:'',isset($preinfo)?$preinfo->wk_stop_time:'');
        $f[] = Form::frameImageOne('image','产品主图片',Url::build('seller/widget.images/index',array('fodder'=>'image')),isset($preinfo)?$preinfo->image:'')->icon('image');
        $f[] = Form::frameImages('images','产品轮播图',Url::build('seller/widget.images/index',array('fodder'=>'images')),isset($preinfo)?json_decode($preinfo->images,1):[])->maxLength(5)->icon('images');
        $f[] = Form::number('price','拼团价',isset($preinfo)?$preinfo->price:'')->min(0)->col(12);
//        $f[] = Form::number('pre_price','拼团价格',isset($preinfo)?$preinfo->pre_price:'')->min(0)->col(12);
        $f[] = Form::number('ot_price','原价',isset($preinfo)?$preinfo->ot_price:0)->min(0)->col(12);
        $f[] = Form::input('min_people','最少拼团人数',isset($preinfo)?$preinfo->min_people:'')->placeholder('最少多少人购买才能成团，如果到期人数不够，则退款');
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
        $form = Form::make_post_form('拼团',$f,Url::build('save',['id'=>$id]));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**保存拼团产品
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
//            'pre_price',
            'ot_price',
            'cost',
            'sales',
            'stock',
            'sort',
//            'give_integral',
            'postage',
            ['combination_time',[]],
            ['weikuan_time',[]],
            ['is_postage',0],
            ['cost',0],
//            ['is_hot',0],
            ['status',0],
            ['limit_num',0]
        ],$request);
//        dump($data);die;
        if(!$data['title']) return JsonService::fail('请输入产品标题');
//        if(!$data['unit_name']) return JsonService::fail('请输入产品单位');
//        var_dump($this->request->post());
        $data['start_time'] = strtotime($data['combination_time'][0]);
        $data['stop_time'] = strtotime($data['combination_time'][1]);
        if(empty($data['start_time'])) return JsonService::fail('请选拼团时间');
        unset($data['combination_time']);
//        if(count($data['weikuan_time'])<1) return JsonService::fail('请选尾款支付时间');
//        $data['wk_start_time'] = strtotime($data['weikuan_time'][0]);
//        $data['wk_stop_time'] = strtotime($data['weikuan_time'][1]);
//        if(empty($data['wk_start_time'])) return JsonService::fail('请选尾款支付时间');
        unset($data['weikuan_time']);
        if(!$data['image']) return JsonService::fail('请选择推荐图');
        if(count($data['images'])<1) return JsonService::fail('请选择轮播图');
        $data['images'] = json_encode($data['images']);
        if($data['price'] == '' || $data['price'] < 0) return JsonService::fail('请输入产品价格');
//        if($data['pre_price'] == '' || $data['pre_price'] < 0) return JsonService::fail('请输入产品拼团售价');
//        if($data['ot_price'] == '' || $data['ot_price'] < 0) return JsonService::fail('请输入产品原售价');
//        if($data['cost'] == '' || $data['cost'] < 0) return JsonService::fail('请输入产品成本价');
        if($data['stock'] == '' || $data['stock'] < 0) return JsonService::fail('请输入库存');
        $data['add_time'] = time();
        $data['mer_id'] = $this->sellerId;
        if($data['limit_num']<1) return JsonService::fail('请输入限购个数');
        if($id){
            $product = StoreCombinationModel::get($id);
            if(!$product) return JsonService::fail('数据不存在!');
            if($product['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
            StoreCombinationModel::edit($data,$id);
            return JsonService::successful('编辑成功!');
        }else{
            StoreCombinationModel::set($data);
            return JsonService::successful('添加成功!');
        }

    }
    /** 开启拼团 在普通商品操作那
     * @param $id
     * @return mixed|void
     */
    public function combination($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return JsonService::fail('数据不存在!');
        if($product['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $f = array();
        $f[] = Form::input('title','产品标题',$product->getData('store_name'));
        $f[] = Form::hidden('product_id',$id);
        $f[] = Form::input('info','拼团活动简介',$product->getData('store_info'))->type('textarea');
        $f[] = Form::input('unit_name','单位',$product->getData('unit_name'))->placeholder('个、位');
        $f[] = Form::dateTimeRange('combination_time','拼团时间');
//        $f[] = Form::dateTimeRange('weikuan_time','尾款支付时间');
        $f[] = Form::frameImageOne('image','产品主图片',Url::build('seller/widget.images/index',array('fodder'=>'image')),$product->getData('image'))->icon('image');
        $f[] = Form::frameImages('images','产品轮播图',Url::build('seller/widget.images/index',array('fodder'=>'images')),json_decode($product->getData('slider_image')))->maxLength(5)->icon('images');
        $f[] = Form::number('price','拼团价')->min(0)->col(12);
//        $f[] = Form::number('pre_price','定金')->min(0)->col(12);
        $f[] = Form::number('ot_price','原价',$product->getData('price'))->min(0)->col(12);
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
        $form = Form::make_post_form('拼团',$f,Url::build('save'));
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
        $product = StoreCombinationModel::get($id);
        if(!$product) return JsonService::fail('数据不存在!');
        if($product['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $data['is_del'] = 1;
        if(!StoreCombinationModel::edit($data,$id))
            return JsonService::fail(StoreCombinationModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return JsonService::successful('删除成功!');
    }

    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $combination = StoreCombinationModel::get($id);
        if(!$combination) return JsonService::fail('数据不存在!');
        if($combination['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $this->assign([
            'content'=>StoreCombinationModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    public function change_field(Request $request,$id,$field){
        if(!$id) return $this->failed('数据不存在');
        $combination = StoreCombinationModel::get($id);
        if(!$combination) return JsonService::fail('数据不存在!');
        if($combination['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $data['description'] = $request->post('description');
        $res = StoreCombinationModel::edit($data,$id);
        if($res)
            return JsonService::successful('添加成功');
        else
            return JsonService::fail('添加失败');
    }


    /**
     * 修改拼团产品状态
     * @param $status
     * @param int $id
     */
    public function set_combination_status($status,$id = 0){
        if(!$id) return JsonService::fail('参数错误');
        $combination = StoreCombinationModel::get($id);
        if(!$combination) return JsonService::fail('数据不存在!');
        if($combination['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $res = StoreCombinationModel::edit(['status'=>$status],$id);
        if($res) return JsonService::successful('修改成功');
        else return JsonService::fail('修改失败');
    }
}
