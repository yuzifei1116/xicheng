<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/15
 * Time: 15:32
 */

namespace app\seller\controller\activity;


use app\admin\controller\AuthController;
use service\JsonService;
use app\admin\model\activity\PrizeList as PrizeListModel;
use service\UtilService;

class PrizeList extends AuthController
{
    public function index()
    {
        $aid = input('get.id/d',0);
        $this->assign('aid',$aid);
        return $this->fetch();
    }

    public function get_prize_list($aid)
    {
        $where=UtilService::getMore([
            ['page',0],
            ['limit',10],
            ['prize_name',''],
        ]);
        return JsonService::successlayui(PrizeListModel::getPrizeList($where,$aid));
    }

    /**
     * @return mixed
     */
    public function create($aid)
    {
        $this->assign('aid',$aid);
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
        if (empty($data['prize_name'])) return JsonService::failjson('标题不能为空');
        if (empty($data['image'])) return JsonService::failjson('请上传图片');
        if (empty($data['probability'])) return JsonService::failjson('请填写概率');
        if ($data['is_limit'] == 0) $data['number'] = 0;
        if ($data['is_limit'] == 1 && $data['number'] < 1)  return JsonService::failjson('请填写奖品数量');
        $data['add_time'] = time();
        $data['probability'] = (int)$data['probability'];
        $res = PrizeListModel::set($data);
        if ($res){
            return JsonService::successfuljson('添加成功!');
        }else{
            return JsonService::failjson('添加失败');
        }
    }

    public function set_show($status='',$id=''){
        ($status=='' || $id=='') && JsonService::fail('缺少参数');
        $res=PrizeListModel::where(['id'=>$id])->update(['status'=>(int)$status]);
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
        $data = PrizeListModel::get($id);
        $this->assign('id',$id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {
        $data = input('post.');
        if (empty($data['prize_name'])) return JsonService::failjson('标题不能为空');
        if (empty($data['image'])) return JsonService::failjson('请上传图片');
        if (empty($data['probability'])) return JsonService::failjson('请填写概率');
        if ($data['is_limit'] == 0) $data['number'] = 0;
        if ($data['is_limit'] == 1 && $data['number'] < 1)  return JsonService::failjson('请填写奖品数量');
        $id = $data['id'];
        unset($data['id']);
        $data['probability'] = (int)$data['probability'];
        $res = PrizeListModel::edit($data,$id);
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
        if(!PrizeListModel::del($id))
            return JsonService::failjson(PrizeListModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return JsonService::successfuljson('删除成功!');
    }

    /**
     * 快速编辑
     *
     * @return
     */
    public function set_probability($field='',$id='',$value=''){
        $field=='' || $id=='' || $value=='' && JsonService::fail('缺少参数');
        $value = (int)$value;
        if(PrizeListModel::where(['id'=>$id])->update([$field=>$value]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }
}