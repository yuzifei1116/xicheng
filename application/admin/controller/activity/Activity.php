<?php

namespace app\admin\controller\activity;


use app\admin\controller\AuthController;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use app\core\model\activity\Activity as ActivityModel;
use think\Request;
use think\Url;

class Activity extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function activity_list()
    {
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20]
        ]);
        return JsonService::successlayui(ActivityModel::getActivityList($where));
    }

    /**
     * 添加
     */
    public function create()
    {
        $field = [
            FormBuilder::input('title','活动名称')->col(24)->required(),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')))->width('100%')->height('500px')->icon('image'),
            FormBuilder::number('price','价格')->col(24)->required(),
            FormBuilder::number('original_price','原价')->col(24)->required(),
            FormBuilder::number('give_integral','赠送积分',0)->col(24),
            FormBuilder::number('ficti','虚拟销量',0)->col(24),
            FormBuilder::radio('status','状态',0)->options([['label'=>'未上线','value'=>0],['label'=>'已上线','value'=>1],['label'=>'已下线','value'=>2]])->required(),
            FormBuilder::dateRange('time','活动时间')->required()
        ];
        $form = FormBuilder::make_post_form('添加活动',$field,Url::build('save'),2);
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
            ['title',''],
            ['price',0],
            ['original_price',0],
            ['give_integral',0],
            ['ficti',0],
            ['status',0],
            ['image',[]],
            ['time',[]]
        ],$request);

        if(!$data['title']) return JsonService::fail('请输入活动名称');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if(!$data['price']) return JsonService::fail('请输入活动门票价格');
        if($data['price'] > $data['original_price']) return JsonService::fail('门票价格不能大于原价');
        if (strtotime($data['time'][0]) > strtotime($data['time'][1])) return JsonService::fail('选择时间错误');
        if (!$data['time'][0] || !$data['time'][1]) return JsonService::fail('请选择活动时间');

        $data['start_time'] = strtotime($data['time'][0]);
        $data['end_time'] = strtotime($data['time'][1]);
        unset($data['time']);
        $data['image'] = $data['image'][0];

        $res = ActivityModel::set($data);
        if ($res){
            return JsonService::successful('添加成功!');
        }else{
            return JsonService::fail('添加失败!');
        }
    }

    public function edit($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $activity = ActivityModel::get($id);
        if (empty($activity)) return JsonService::fail('数据错误');
        $field = [
            FormBuilder::input('title','活动名称',$activity->getData('title'))->col(24)->required(),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$activity->getData('image'))->width('100%')->height('500px')->icon('image'),
            FormBuilder::number('price','价格',$activity->getData('price'))->col(24)->required(),
            FormBuilder::number('original_price','原价',$activity->getData('original_price'))->col(24)->required(),
            FormBuilder::number('give_integral','赠送积分',$activity->getData('give_integral'))->col(24),
            FormBuilder::number('ficti','虚拟销量',$activity->getData('ficti'))->col(24),
            FormBuilder::radio('status','状态',$activity->getData('status'))->options([['label'=>'未上线','value'=>0],['label'=>'已上线','value'=>1],['label'=>'已下线','value'=>2]])->required(),
            FormBuilder::dateRange('time','活动时间',$activity->getData('start_time'),$activity->getData('end_time'))->required()
        ];
        $form = FormBuilder::make_post_form('编辑',$field,Url::build('update',['id'=>$id]),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request,$id)
    {
        $data = UtilService::postMore([
            ['title',''],
            ['price',0],
            ['original_price',0],
            ['give_integral',0],
            ['ficti',0],
            ['status',0],
            ['image',[]],
            ['time',[]]
        ],$request);

        if(!$data['title']) return JsonService::fail('请输入活动名称');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if(!$data['price']) return JsonService::fail('请输入活动门票价格');
        if($data['price'] > $data['original_price']) return JsonService::fail('门票价格不能大于原价');
        if (strtotime($data['time'][0]) > strtotime($data['time'][1])) return JsonService::fail('选择时间错误');
        if (!$data['time'][0] || !$data['time'][1]) return JsonService::fail('请选择活动时间');
        $data['start_time'] = strtotime($data['time'][0]);
        $data['end_time'] = strtotime($data['time'][1]);
        unset($data['time']);
        $data['image'] = $data['image'][0];
        $res = ActivityModel::edit($data,$id);
        if ($res){
            return JsonService::successful('编辑成功!');
        }else{
            return JsonService::fail('编辑失败!');
        }
    }

    /**
     * 上线
     */
    public function online($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $activity = ActivityModel::get($id);
        if (empty($activity)) return JsonService::fail('数据错误');
        if ($activity['status'] == 1) return JsonService::fail('活动已上线,请勿再次操作');
        $res = ActivityModel::edit(['status'=>1],$id);
        if ($res){
            return JsonService::successful('上线成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }

    /**
     * 上线
     */
    public function offline($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $activity = ActivityModel::get($id);
        if (empty($activity)) return JsonService::fail('数据错误');
        if ($activity['status'] != 1) return JsonService::fail('活动已上线,请勿再次操作');
        $res = ActivityModel::edit(['status'=>2],$id);
        if ($res){
            return JsonService::successful('下线成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }
}