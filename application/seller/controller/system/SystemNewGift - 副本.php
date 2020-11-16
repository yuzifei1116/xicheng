<?php


namespace app\seller\controller\system;


use app\seller\controller\AuthController;
use app\seller\model\system\SystemNewGift as SystemNewGiftModel;
use service\JsonService;

/**
 * 新人礼控制器
 * Class SystemNewGift
 * @package app\seller\controller\system
 * pyp
 */
class SystemNewGift extends AuthController
{
    /**
     * pyp
     */
    public function index()
    {
        $data = SystemNewGiftModel::getNewGift();
        if (empty($data)){
            $data['status'] = 0;
            $data['give_now_money'] = 0;
            $data['give_integral'] = 0;
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function save()
    {
        $data = input('post.');
        $list = SystemNewGiftModel::getNewGift();
        if (empty($list)){
            $res = SystemNewGiftModel::set($data);
        }else{
            $res = SystemNewGiftModel::where('id','>',0)->update($data);
        }
        if (!$res){
            return JsonService::fail('提交失败');
        }else{
            return JsonService::successful('设置成功');
        }
    }
}