<?php

namespace app\admin\controller\coupon;

use app\admin\controller\AuthController;
use app\core\model\user\User;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use app\core\model\coupon\Coupon as CouponModel;
use think\Request;
use think\Url;


/**
 * 优惠券控制器
 * Class StoreCategory
 * @package app\admin\controller\system
 */
class Coupon extends AuthController
{

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /*
     *  异步获取分类列表
     *  @return json
     */
    public function coupon_list(){
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(CouponModel::couponList($where));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $f = array();
        $f[] = FormBuilder::radio('type','类型',0)->options([['label'=>'通用','value'=>0],['label'=>'商城','value'=>1],['label'=>'住宿','value'=>2],['label'=>'活动','value'=>3],['label'=>'点餐','value'=>4]]);
        $f[] = FormBuilder::input('title','优惠券名称')->required();
        $f[] = FormBuilder::number('money','优惠券面值',0)->min(0)->required();
        $f[] = FormBuilder::number('sort','排序',0);
        $f[] = FormBuilder::radio('status','状态',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]]);
        $f[] = FormBuilder::dateRange('time','有效期')->required();

        $form = FormBuilder::make_post_form('添加优惠券',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save(Request $request)
    {
        $data = UtilService::postMore([
            ['type',0],
            ['title',''],
            ['money',0],
            'sort',
            ['status',1],
            ['time',[]]
        ],$request);
        if(!$data['title']) return JsonService::fail('请输入优惠券名称');
        if(!$data['money']) return JsonService::fail('请输入优惠券面值');
        if (!$data['time'][0] || !$data['time'][1]) return JsonService::fail('请选择有效期');
        $data['start_time'] = strtotime($data['time'][0]);
        $data['end_time'] = strtotime($data['time'][1]) + 3600*24-1;
        unset($data['time']);
        $res = CouponModel::set($data);
        if ($res){
            return JsonService::successful('添加成功!');
        }else{
            return JsonService::fail('添加失败');
        }
    }

    public function edit($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $coupon = CouponModel::get($id);
        if (empty($coupon)) return JsonService::fail('数据错误');
        $f = array();
        $f[] = FormBuilder::radio('type','类型',$coupon->getData('type'))->options([['label'=>'通用','value'=>0],['label'=>'商城','value'=>1],['label'=>'住宿','value'=>2],['label'=>'活动','value'=>3],['label'=>'点餐','value'=>4]]);
        $f[] = FormBuilder::input('title','优惠券名称',$coupon->getData('title'))->required();
        $f[] = FormBuilder::number('money','优惠券面值',$coupon->getData('money'))->min(0)->required();
        $f[] = FormBuilder::number('sort','排序',$coupon->getData('sort'));
        $f[] = FormBuilder::radio('status','状态',$coupon->getData('status'))->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]]);
        $f[] = FormBuilder::dateRange('time','有效期',$coupon->getData('start_time'),$coupon->getData('end_time'))->required();
        $form = FormBuilder::make_post_form('编辑',$f,Url::build('update',['id'=>$id]),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request,$id)
    {
        $data = UtilService::postMore([
            ['type',0],
            ['title',''],
            ['money',0],
            'sort',
            ['status',1],
            ['time',[]]
        ],$request);
        if(!$data['title']) return JsonService::fail('请输入优惠券名称');
        if(!$data['money']) return JsonService::fail('请输入优惠券面值');
        if (!$data['time'][0] || !$data['time'][1]) return JsonService::fail('请选择有效期');
        $data['start_time'] = strtotime($data['time'][0]);
        $data['end_time'] = strtotime($data['time'][1]) + 3600*24-1;
        unset($data['time']);
        $res = CouponModel::edit($data,$id);
        if ($res){
            return JsonService::successful('编辑成功!');
        }else{
            return JsonService::fail('编辑失败!');
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
        if(!$id) return JsonService::fail('数据不存在!');
        $data['is_del'] = 1;
        if(!CouponModel::edit($data,$id))
            return JsonService::fail(CouponModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return JsonService::successful('删除成功!');
    }

    /**
     * @param $id
     */
    public function send_coupon($id){
        $where = UtilService::getMore([
            ['status',''],
            ['title',''],
            ['is_del',0],
        ],$this->request);
        $nickname = User::where('uid','IN',$id)->column('uid,nickname');
        $this->assign('where',$where);
        $this->assign('uid',$id);
        $this->assign('nickname',implode(',',$nickname));
        $this->assign(CouponModel::systemPageCoupon($where));
        return $this->fetch();
    }

    public function set_status($id,$status)
    {
        if (!$id) return JsonService::fail('数据错误');
        $coupon = CouponModel::get($id);
        if(!$coupon) return JsonService::fail('数据不存在!');
        $res = CouponModel::edit(['status'=>$status],$id);
        if ($res){
            return JsonService::successful('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}
