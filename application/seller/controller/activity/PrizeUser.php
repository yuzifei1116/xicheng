<?php

namespace app\seller\controller\activity;

use app\seller\controller\AuthController;
use service\JsonService;
use app\seller\model\activity\PrizeUser as PrizeUserModel;
use service\UtilService;
use service\PHPCSVServer;

class PrizeUser extends AuthController
{
    public function index($aid)
    {
        $this->assign('aid',$aid);
        return $this->fetch();
    }

    public function get_prize_user($aid)
    {
        $where=UtilService::getMore([
            ['page',0],
            ['limit',10],
            ['username',''],
            ['status',''],
        ]);
        return JsonService::successlayui(PrizeUserModel::getPrizeUser($where,$aid));
    }

    public function get_prize($id)
    {
        $res=PrizeUserModel::edit(['status'=>2],$id);
        if ($res){
            return JsonService::successfuljson('领取成功');
        }else{
            return JsonService::failjson('领取失败');
        }
    }

    public function set_status($id)
    {
        $res=PrizeUserModel::edit(['status'=>3],$id);
        if ($res){
            return JsonService::successfuljson('操作成功');
        }else{
            return JsonService::failjson('操作失败');
        }
    }

    //导出Excel表格
    public function export(){
        $where = UtilService::getMore([
            ['username',''],
            ['status',''],
            ['aid',0],
        ]);
        $list = PrizeUserModel::SaveExport($where);
        $headArr = [
            'title'=>'活动标题',
            'username'=>'用户名',
            'prize_name'=>'奖品名称',
            'tel'=>'电话',
            'addr'=>'地址',
            '_status'=>'状态',
            'add_time'=>'添加时间',
        ];
        PHPCSVServer::exportCommon('test',$headArr, $list);
    }
}