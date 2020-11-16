<?php

namespace app\seller\controller\ump;

use app\seller\controller\AuthController;
use app\seller\model\store\StoreProduct;
use app\seller\model\ump\StoreCouponIssue;
use app\seller\model\wechat\WechatUser as UserModel;
use service\FormBuilder as Form;
use service\JsonService;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UtilService;
use think\Request;
use app\seller\model\ump\StoreCoupon as CouponModel;
use think\Url;

/**
 * 优惠券控制器
 * Class StoreCategory
 * @package app\seller\controller\system
 */
class StoreCoupon extends AuthController
{

    /**
     * @return mixed
     */
    public function index()
    {
        $where = Util::getMore([
            ['status',''],
            ['title',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(CouponModel::systemPage($where,$this->sellerId));
        return $this->fetch();
    }

//    /**
//     * @return mixed
//     */
//    public function create()
//    {
//        $f = array();
//        $f[] = Form::input('title','优惠券名称');
//        $f[] = Form::number('coupon_price','优惠券面值',0)->min(0);
//        $f[] = Form::number('use_min_price','优惠券最低消费')->min(0);
//        $f[] = Form::number('coupon_time','优惠券有效期限')->min(0);
//        $f[] = Form::number('sort','排序');
//        $f[] = Form::radio('status','状态',0)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]]);
//
//        $form = Form::make_post_form('添加优惠券',$f,Url::build('save'));
//        $this->assign(compact('form'));
//        return $this->fetch('public/form-builder');
//    }

    public function create()
    {
        return $this->fetch();
    }

    public function save()
    {
        $data = input('post.');
        if (empty($data)) return JsonService::failjson('数据提交错误');
//        dump($data);die;
        if (empty($data['title'])) JsonService::failjson('请输入优惠券名称');

        //验证优惠券类型
        if ($data['coupon_type'] == 1){ //coupon_type 1代表代金券 2代表折扣券
            if (empty($data['coupon_price'])) return JsonService::failjson('请输入优惠券面值');
            if (!is_numeric($data['coupon_price'])) return JsonService::failjson('请输入正确的优惠券面值');
            if ($data['coupon_price'] <= 0) return JsonService::failjson('请输入正确的优惠券面值');
            $data['coupon_discount'] = 1; //折扣率重新赋值为1
        }elseif ($data['coupon_type'] == 2){ //coupon_type 1代表代金券 2代表折扣券
            if (empty($data['coupon_discount'])) return JsonService::failjson('请输入折扣率');
            if (!is_numeric($data['coupon_discount'])) return JsonService::failjson('请输入正确的折扣');
            if ($data['coupon_discount'] < 0 || $data['coupon_discount'] > 1) return JsonService::failjson('请输入正确的折扣,例:0.99');
            $data['coupon_price'] = 0; //代金券重新赋值为0
        }

        //验证是否限量
        if ($data['is_limit'] == 1){ //coupon_num 发布数量 1限量 0不限量
            if (empty($data['coupon_num'])) return JsonService::failjson('请输入发布数量');
            if (!is_numeric($data['coupon_num'])) return JsonService::failjson('请输入正确的发布数量');
            if ($data['coupon_num'] <= 0) return JsonService::failjson('请输入正确的发布数量');
        }elseif ($data['is_limit'] == 0){
            $data['coupon_num'] = 0;
        }

        //验证用户前台可否领取
        if ($data['self_can_get'] == 1){ //1可以 0不可以
            if (empty($data['self_max_num'])) return JsonService::failjson('请输入领取数量');
            if (!is_numeric($data['self_max_num'])) return JsonService::failjson('请输入正确的领取数量');
            if ($data['self_max_num'] <= 0) return JsonService::failjson('请输入正确的领取数量');
        }elseif ($data['self_can_get'] == 0){
            $data['self_max_num'] = 0;
        }

        if (!is_numeric($data['use_min_price'])) return JsonService::failjson('请输入正确的最低消费');

        //验证优惠券持续时间
        if ($data['time_type'] == 1){ //1有效天数 2期限
            if (empty($data['coupon_long_time'])) return JsonService::failjson('请输入天数');
            if (!is_numeric($data['coupon_long_time'])) return JsonService::failjson('请输入正确的天数');
            if ($data['coupon_long_time'] <= 0) return JsonService::failjson('请输入正确的天数');
            unset($data['time']);
            $data['coupon_start_time'] = 0;
            $data['coupon_end_time'] = 0;
        }elseif ($data['time_type'] == 2){
            $data['coupon_long_time'] = 0;
            $time = explode(' - ',$data['time']);
            $data['coupon_start_time'] = strtotime($time[0]);
            $data['coupon_end_time'] = strtotime($time[1])+3600*24-1;
            if ($data['coupon_start_time'] > $data['coupon_end_time']){
                return JsonService::failjson('日期选择顺序错误');
            }
            unset($data['time']);
        }

        //验证可使用商品
        if (!empty($data['coupon_products'])){
            $data['coupon_products'] = implode(',',$data['coupon_products']);
        }
        $data['add_time'] = time();
        $data['mer_id'] = $this->sellerId;
//        dump($data);die;
        $res = CouponModel::set($data);
        if ($res){
            return JsonService::successfuljson('添加优惠券成功!');
        }else{
            return JsonService::failjson('添加优惠券失败');
        }
    }

    public function edit($id)
    {
        if (!$id) return JsonService::failjson('数据错误');
        $data = CouponModel::get($id);
        if (!$data) return JsonService::failjson('数据错误');
        if($data['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $data = $data->toArray();
        if (!empty($data['coupon_start_time'])){
            $time[0] = date('Y-m-d',$data['coupon_start_time']);
            $time[1] = date('Y-m-d',$data['coupon_end_time']);
            $data['time'] = implode(' - ',$time);
        }else{
            $data['time'] = '';
        }
        if (!empty($data['coupon_products'])){
            $coupon_products = explode(',',$data['coupon_products']);
            $data['coupon_products'] = StoreProduct::getProduct($coupon_products);
        }
//        dump($data);die;
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {
        $data = input('post.');
//        dump($data);die;
        $id = $data['id'];
        unset($data['id']);
        $coupon = CouponModel::get($id);
        if (!$coupon) return JsonService::failjson('数据错误');
        if($coupon['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');

        if (empty($data)) return JsonService::failjson('数据提交错误');
        if (empty($data['title'])) JsonService::failjson('请输入优惠券名称');

        //验证优惠券类型
        if ($data['coupon_type'] == 1){ //coupon_type 1代表代金券 2代表折扣券
            if (empty($data['coupon_price'])) return JsonService::failjson('请输入优惠券面值');
            if (!is_numeric($data['coupon_price'])) return JsonService::failjson('请输入正确的优惠券面值');
            if ($data['coupon_price'] <= 0) return JsonService::failjson('请输入正确的优惠券面值');
            $data['coupon_discount'] = 1; //折扣率重新赋值为1
        }elseif ($data['coupon_type'] == 2){ //coupon_type 1代表代金券 2代表折扣券
            if (empty($data['coupon_discount'])) return JsonService::failjson('请输入折扣率');
            if (!is_numeric($data['coupon_discount'])) return JsonService::failjson('请输入正确的折扣');
            if ($data['coupon_discount'] < 0 || $data['coupon_discount'] > 1) return JsonService::failjson('请输入正确的折扣,例:0.99');
            $data['coupon_price'] = 0; //代金券重新赋值为0
        }

        //验证是否限量
        if ($data['is_limit'] == 1){ //coupon_num 发布数量 1限量 0不限量
            if (empty($data['coupon_num'])) return JsonService::failjson('请输入发布数量');
            if (!is_numeric($data['coupon_num'])) return JsonService::failjson('请输入正确的发布数量');
            if ($data['coupon_num'] <= 0) return JsonService::failjson('请输入正确的发布数量');
        }elseif ($data['is_limit'] == 0){
            $data['coupon_num'] = 0;
        }

        //验证用户前台可否领取
        if ($data['self_can_get'] == 1){ //1可以 0不可以
            if (empty($data['self_max_num'])) return JsonService::failjson('请输入领取数量');
            if (!is_numeric($data['self_max_num'])) return JsonService::failjson('请输入正确的领取数量');
            if ($data['self_max_num'] <= 0) return JsonService::failjson('请输入正确的领取数量');
        }elseif ($data['self_can_get'] == 0){
            $data['self_max_num'] = 0;
        }

        if (!is_numeric($data['use_min_price'])) return JsonService::failjson('请输入正确的最低消费');

        //验证优惠券持续时间
        if ($data['time_type'] == 1){ //1有效天数 2期限
            if (empty($data['coupon_long_time'])) return JsonService::failjson('请输入天数');
            if (!is_numeric($data['coupon_long_time'])) return JsonService::failjson('请输入正确的天数');
            if ($data['coupon_long_time'] <= 0) return JsonService::failjson('请输入正确的天数');
            unset($data['time']);
            $data['coupon_start_time'] = 0;
            $data['coupon_end_time'] = 0;
        }elseif ($data['time_type'] == 2){
            $data['coupon_long_time'] = 0;
            $time = explode(' - ',$data['time']);
            $data['coupon_start_time'] = strtotime($time[0]);
            $data['coupon_end_time'] = strtotime($time[1])+3600*24-1;
            if ($data['coupon_start_time'] > $data['coupon_end_time']){
                return JsonService::failjson('日期选择顺序错误');
            }
            unset($data['time']);
        }
        //验证可使用商品
        if (!empty($data['coupon_products'])){
            $data['coupon_products'] = implode(',',$data['coupon_products']);
        }
        $data['add_time'] = time();
        $res = CouponModel::edit($data,$id);
        if ($res){
            return JsonService::successfuljson('编辑优惠券成功!');
        }else{
            return JsonService::failjson('编辑优惠券失败');
        }
    }
//    /**
//     * @param Request $request
//     * @return \think\response\Json
//     */
//    public function save(Request $request)
//    {
//        $data = Util::postMore([
//            'title',
//            'coupon_price',
//            'use_min_price',
//            'coupon_time',
//            'sort',
//            ['status',0]
//        ],$request);
//        if(!$data['title']) return JsonService::fail('请输入优惠券名称');
//        if(!$data['coupon_price']) return JsonService::fail('请输入优惠券面值');
//        if(!$data['coupon_time']) return JsonService::fail('请输入优惠券有效期限');
//        $data['add_time'] = time();
//        CouponModel::set($data);
//        return JsonService::successful('添加优惠券成功!');
//    }

//    /**
//     * 显示编辑资源表单页.
//     *
//     * @param  int  $id
//     * @return \think\Response
//     */
//    public function edit($id)
//    {
//        $coupon = CouponModel::get($id);
//        if(!$coupon) return JsonService::fail('数据不存在!');
//        $f = array();
//        $f[] = Form::input('title','优惠券名称',$coupon->getData('title'));
//        $f[] = Form::number('coupon_price','优惠券面值',$coupon->getData('coupon_price'))->min(0);
//        $f[] = Form::number('use_min_price','优惠券最低消费',$coupon->getData('use_min_price'))->min(0);
//        $f[] = Form::number('coupon_time','优惠券有效期限',$coupon->getData('coupon_time'))->min(0);
//        $f[] = Form::number('sort','排序',$coupon->getData('sort'));
//        $f[] = Form::radio('status','状态',$coupon->getData('status'))->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]]);
//
//        $form = Form::make_post_form('添加优惠券',$f,Url::build('update',array('id'=>$id)));
//        $this->assign(compact('form'));
//        return $this->fetch('public/form-builder');
//    }


//    /**
//     * 保存更新的资源
//     *
//     * @param  \think\Request  $request
//     * @param  int  $id
//     * @return \think\Response
//     */
//    public function update(Request $request, $id)
//    {
//        $data = Util::postMore([
//            'title',
//            'coupon_price',
//            'use_min_price',
//            'coupon_time',
//            'sort',
//            ['status',0]
//        ],$request);
//        if(!$data['title']) return JsonService::fail('请输入优惠券名称');
//        if(!$data['coupon_price']) return JsonService::fail('请输入优惠券面值');
//        if(!$data['coupon_time']) return JsonService::fail('请输入优惠券有效期限');
//        CouponModel::edit($data,$id);
//        return JsonService::successful('修改成功!');
//    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!$id) return JsonService::fail('数据不存在!');
        $coupon = CouponModel::get($id);
        if (!$coupon) return JsonService::failjson('数据错误');
        if($coupon['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $data['is_del'] = 1;
        if(!CouponModel::edit($data,$id))
            return JsonService::fail(CouponModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return JsonService::successful('删除成功!');
    }

    /**
     * 修改优惠券状态
     * @param $id
     * @return \think\response\Json
     */
    public function status($id)
    {
        if(!$id) return JsonService::fail('数据不存在!');
        $coupon = CouponModel::get($id);
        if (!$coupon) return JsonService::failjson('数据错误');
        if($coupon['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        if(!CouponModel::editIsDel($id))
            return JsonService::fail(CouponModel::getErrorInfo('修改失败,请稍候再试!'));
        else
            return JsonService::successful('修改成功!');
    }

    /**
     * @return mixed
     */
    public function grant_subscribe(){
        $where = Util::getMore([
            ['status',''],
            ['title',''],
            ['is_del',0],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(CouponModel::systemPageCoupon($where));
        return $this->fetch();
    }

    /**
     * @return mixed
     */
    public function grant_all(){
        $where = Util::getMore([
            ['status',''],
            ['title',''],
            ['is_del',0],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(CouponModel::systemPageCoupon($where));
        return $this->fetch();
    }

    /**
     * @param $id
     */
    public function grant($id){
        $where = Util::getMore([
            ['status',''],
            ['title',''],
            ['is_del',0],
        ],$this->request);
        $nickname = UserModel::where('uid','IN',$id)->column('uid,nickname');
        $this->assign('where',$where);
        $this->assign('uid',$id);
        $this->assign('nickname',implode(',',$nickname));
        $this->assign(CouponModel::systemPageCoupon($where));
        return $this->fetch();
    }

    public function issue($id)
    {
        if(!CouponModel::be(['id'=>$id,'status'=>1,'is_del'=>0]))
            return $this->failed('发布的优惠劵已失效或不存在!');
        $coupon = CouponModel::get($id);
        if (!$coupon) return JsonService::failjson('数据错误');
        if($coupon['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');
        $f = array();
        $f[] = Form::input('id','优惠劵ID',$id)->disabled(1);
        $f[] = Form::dateTimeRange('range_date','领取时间')->placeholder('不填为永久有效');
        $f[] = Form::number('count','发布数量',0)->min(0)->placeholder('不填或填0,为不限量');
        $f[] = Form::radio('is_permanent','是否不限量',0)->options([['label'=>'限量','value'=>0],['label'=>'不限量','value'=>1]]);
        $f[] = Form::radio('status','状态',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]]);

        $form = Form::make_post_form('添加优惠券',$f,Url::build('update_issue',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

//        FormBuilder::text('id','优惠劵ID',$id)->disabled();
//        FormBuilder::dateTimeRange('range_date','领取时间')->placeholder('不填为永久有效');
//        FormBuilder::text('count','发布数量')->placeholder('不填或填0,为不限量');
//        FormBuilder::radio('status','是否开启',[
//            ['value'=>1,'label'=>'开启'],
//            ['value'=>0,'label'=>'关闭']
//        ],1);
//        $this->assign(['title'=>'发布优惠券','rules'=>FormBuilder::builder()->getContent(),'action'=>Url::build('update_issue',array('id'=>$id))]);
//        return $this->fetch('public/common_form');
    }

    public function update_issue(Request $request,$id)
    {
        list($_id,$rangeTime,$count,$status,$is_permanent) = UtilService::postMore([
            'id',['range_date',['','']],['count',0],['status',0],['is_permanent',0]
        ],$request,true);
        if($_id != $id) return JsonService::fail('操作失败,信息不对称');

        $coupon = CouponModel::get($id);
        if (!$coupon) return JsonService::failjson('数据错误');
        if($coupon['mer_id'] != $this->sellerId) return JsonService::fail('数据错误!');

        if(!$count) $count = 0;
        if(!CouponModel::be(['id'=>$id,'status'=>1,'is_del'=>0])) return JsonService::fail('发布的优惠劵已失效或不存在!');
        if(count($rangeTime)!=2) return JsonService::fail('请选择正确的时间区间');

        list($startTime,$endTime) = $rangeTime;
//        echo $startTime;echo $endTime;var_dump($rangeTime);die;
        if(!$startTime) $startTime = 0;
        if(!$endTime) $endTime = 0;
        if(!$startTime && $endTime) return JsonService::fail('请选择正确的开始时间');
        if($startTime && !$endTime) return JsonService::fail('请选择正确的结束时间');
        if(StoreCouponIssue::setIssue($id,$count,strtotime($startTime),strtotime($endTime),$count,$status,$is_permanent))
            return JsonService::successful('发布优惠劵成功!');
        else
            return JsonService::fail('发布优惠劵失败!');
    }


    /**
     * 给分组用户发放优惠券
     */
    public function grant_group(){
        $where = Util::getMore([
            ['status',''],
            ['title',''],
            ['is_del',0],
        ],$this->request);
        $group = UserModel::getUserGroup();
        $this->assign('where',$where);
        $this->assign('group',json_encode($group));
        $this->assign(CouponModel::systemPageCoupon($where));
        return $this->fetch();
    }
    /**
     * 给标签用户发放优惠券
     */
    public function grant_tag(){
        $where = Util::getMore([
            ['status',''],
            ['title',''],
            ['is_del',0],
        ],$this->request);
        $tag = UserModel::getUserTag();;//获取所有标签
        $this->assign('where',$where);
        $this->assign('tag',json_encode($tag));
        $this->assign(CouponModel::systemPageCoupon($where));
        return $this->fetch();
    }
}
