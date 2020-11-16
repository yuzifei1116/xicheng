<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\admin\controller\stay;


use app\admin\controller\AuthController;
use service\FormBuilder;
use service\JsonService;
use app\core\model\stay\StayType as StayTypeModel;
use app\core\model\stay\StayRoom;
use service\UtilService;
use think\Request;
use think\Url;

class StayType extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 房间类型
     */
    public function type_list()
    {
        $where = UtilService::getMore([
            ['page',1],
            ['limit',20]
        ]);
        return JsonService::successlayui(StayTypeModel::getTypeList($where));
    }

    /**
     * 添加
     */
    public function create()
    {
        $field = [
            FormBuilder::input('title','类型名称')->col(24),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')))->width('100%')->height('500px')->icon('image'),
            FormBuilder::number('sort','排序')->col(8),
            FormBuilder::radio('status','状态',1)->options([['label'=>'正常','value'=>1],['label'=>'禁止','value'=>0]])
        ];
        $form = FormBuilder::make_post_form('添加房间类型',$field,Url::build('save'),2);
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
            'title',
            ['image',[]],
            'sort',
            ['status',1]
        ],$request);
        if($data['title'] == '') return JsonService::fail('类型名称不能为空');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['sort'] <0 ) $data['sort'] = 0;
        $data['image'] = $data['image'][0];
        $res = StayTypeModel::set($data);
        if ($res){
            return JsonService::successful('添加房间类型成功!');
        }else{
            return JsonService::fail('添加房间类型失败!');
        }
    }

    public function edit($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $type = StayTypeModel::get($id);
        if (empty($type)) return JsonService::fail('数据错误');
        $field = [
            FormBuilder::input('title','类型名称',$type->getData('title'))->col(24),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$type->getData('image'))->width('100%')->height('500px')->icon('image'),
            FormBuilder::number('sort','排序',$type->getData('sort'))->col(8),
            FormBuilder::radio('status','状态',$type->getData('status'))->options([['label'=>'正常','value'=>1],['label'=>'禁止','value'=>0]])
        ];
        $form = FormBuilder::make_post_form('修改',$field,Url::build('update',['id'=>$id]),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request,$id)
    {
        $data = UtilService::postMore([
            ['title',''],
            ['image',[]],
            ['sort',0],
            ['status',1],
        ],$request);
        if($data['title'] == '') return JsonService::fail('类型名称不能为空');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['sort'] < 0) $data['sort'] = 0;
        $data['image'] = $data['image'][0];
        $res = StayTypeModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改房间类型成功!');
        }else{
            return JsonService::fail('修改房间类型失败!');
        }
    }

    /**
     * 设置房间状态
     */
    public function set_status($id, $status)
    {
        if (!$id || ($status != 0 && $status != 1)) return JsonService::fail('数据错误');
        StayTypeModel::beginTrans();
        $res1 = StayTypeModel::edit(['status'=>$status],$id);
        if ($status == 0){
            $res2 = StayRoom::edit(['status'=>2],$id,'type_id');
        }else{
            $res2 = true;
        }
        $res = $res1 && $res2;
        StayTypeModel::checkTrans($res);
//        $res = StayTypeModel::edit(['status'=>$status],$id);
        if ($res) {
            return JsonService::success('操作成功');
        }else{
            return JsonService::fail('操作失败');
        }
    }
}