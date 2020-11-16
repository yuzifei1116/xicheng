<?php

namespace app\admin\controller\coupon;

use app\admin\controller\AuthController;
use app\core\model\user\User;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use app\core\model\coupon\Coupon as CouponModel;
use app\core\model\coupon\CouponUse as CouponUseModel;
use think\Request;
use think\Url;


/**
 * 优惠券控制器
 * Class StoreCategory
 * @package app\admin\controller\system
 */
class CouponUse extends AuthController
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
    public function use_list(){
        $where = UtilService::getMore([
            ['type',''],
            ['is_use',''],
            ['add_time',''],
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(CouponUseModel::couponUseList($where));
    }

    /**
     * 发放优惠券到指定个人
     * @param $id
     * @param $uid
     * @return \think\response\Json
     */
    public function send_coupon($id,$uid){
        if(!$id) return JsonService::fail('数据不存在!');
        $coupon = CouponModel::get($id);
        if(!$coupon) return JsonService::fail('数据不存在!');
        $user = explode(',',$uid);
        foreach ($user as $k=>$v){
            if (CouponUseModel::be(['coupon_id'=>$id,'uid'=>$v])) {
                $nickname = User::get($uid)['nickname'];
                return JsonService::fail($nickname.'已有该优惠券,请勿多次发送');
            }
        }
        $res = CouponUseModel::setCoupon($coupon,$user);
        if(!$res)
            return JsonService::fail('发放失败,请稍候再试!');
        else
            return JsonService::successful('发放成功!');

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

    /**
     * 修改优惠券状态
     * @param $id
     * @return \think\response\Json
     */
    public function status($id)
    {
        if(!$id) return Json::fail('数据不存在!');
        if(!CouponModel::editIsDel($id))
            return Json::fail(CouponModel::getErrorInfo('修改失败,请稍候再试!'));
        else
            return Json::successful('修改成功!');
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

    public function issue($id)
    {
        if(!CouponModel::be(['id'=>$id,'status'=>1,'is_del'=>0]))
            return $this->failed('发布的优惠劵已失效或不存在!');
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
