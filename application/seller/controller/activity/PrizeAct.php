<?php

namespace app\seller\controller\activity;

use app\admin\controller\AuthController;
use service\JsonService;
use app\admin\model\activity\PrizeAct as PrizeActModel;
use service\UtilService;

class PrizeAct extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function get_activity_list()
    {
        $where=UtilService::getMore([
            ['page',0],
            ['limit',10],
            ['title',''],
            ['status',''],
        ]);
        return JsonService::successlayui(PrizeActModel::getActList($where));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->fetch();
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        $data = input('post.');
        if (empty($data['title'])) return JsonService::failjson('标题不能为空');
        if (empty($data['image'])) return JsonService::failjson('请上传图片');
        if (empty($data['time'])) return JsonService::failjson('请选择时间');
        $time = explode(' - ',$data['time']);
        $data['start_time'] = strtotime($time[0]);
        $data['end_time'] = strtotime($time[1]);
        if ($data['start_time'] > $data['end_time']) return JsonService::fail('时间选择顺序错误');
        unset($data['time']);
        $data['add_time'] = time();
        $res = PrizeActModel::set($data);
        if ($res){
            return JsonService::successfuljson('添加成功!');
        }else{
            return JsonService::failjson('添加失败');
        }
    }

    public function set_show($status='',$id=''){
        ($status=='' || $id=='') && JsonService::fail('缺少参数');
        $res=PrizeActModel::where(['id'=>$id])->update(['status'=>(int)$status]);
        if($res){
            return JsonService::successful($status==1 ? '开启成功':'关闭成功');
        }else{
            return JsonService::fail($status==1 ? '开启失败':'关闭失败');
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
        $data = PrizeActModel::get($id);
        $time[0] = date('Y-d-m H:i:s',$data['start_time']);
        $time[1] = date('Y-d-m H:i:s',$data['end_time']);
        $data['time'] = implode(' - ',$time);
        $this->assign('id',$id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {
        $data = input('post.');
        if (empty($data['title'])) return JsonService::failjson('标题不能为空');
        if (empty($data['image'])) return JsonService::failjson('请上传图片');
        if (empty($data['time'])) return JsonService::failjson('请选择时间');
        $time = explode(' - ',$data['time']);
        $data['start_time'] = strtotime($time[0]);
        $data['end_time'] = strtotime($time[1]);
        if ($data['start_time'] > $data['end_time']) return JsonService::fail('时间选择顺序错误');
        unset($data['time']);
        $data['add_time'] = time();
        $id = $data['id'];
        unset($data['id']);
        $res = PrizeActModel::edit($data,$id);
        if ($res){
            return JsonService::successfuljson('修改成功!');
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
        if(!$id) return JsonService::successful('数据错误!');
        if(!PrizeActModel::edit(['is_del'=>1],$id))
            return JsonService::failjson(PrizeActModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return JsonService::successfuljson('删除成功!');
    }
}