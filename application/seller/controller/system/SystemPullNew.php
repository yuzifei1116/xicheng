<?php


namespace app\seller\controller\system;


use app\seller\controller\AuthController;
use app\seller\model\system\SystemPullNew as SystemPullNewModel;
use service\JsonService;
use app\seller\model\ump\StoreCoupon;

/**
 * Class SystemNewGift
 * @package app\seller\controller\system
 * pyp
 */
class SystemPullNew extends AuthController
{
    /**
     * pyp
     */
    public function index()
    {
        $data = SystemPullNewModel::getPullNew();
        $coupon_list = [];
        $cids = [];
        if (empty($data)){
            $data['status'] = 0;
            $data['give_now_money'] = 0;
            $data['give_integral'] = 0;
            $data['give_coupon'] = [];
        }else{
            if ($data['give_coupon'] != ''){
                $coupon = json_decode($data['give_coupon'],true);
                foreach ($coupon as $k=>$v){
                    $coupon_list[$k]['cid'] = $k;
                    $coupon_list[$k]['title'] = StoreCoupon::getTitle($k);
                    $coupon_list[$k]['num'] = $v;
                    $cids[] = (string)$k;
                }
            }
        }
//        dump($data);
        $coupon = StoreCoupon::getCouponList();
        $this->assign([
            'data'=>$data,
            'coupon'=>$coupon,
            'coupon_list'=>$coupon_list,
            'cids'=>$cids
        ]);
        return $this->fetch();
    }

    public function save()
    {
        $data = input('post.');
        if (isset($data['cid'])){
            $count = count($data['cid']);
            $coupon = [];
            for ($i=0;$i<$count;$i++){
                if ($data['num'][$i] <= 0) {
                    continue;
                }else{
                    $coupon[$data['cid'][$i]] = $data['num'][$i];
                }
            }
            $data['give_coupon'] = json_encode($coupon);
            unset($data['num']);
            unset($data['cid']);
        }else{
            $data['give_coupon'] = '';
        }

        $list = SystemPullNewModel::getPullNew();

        if (empty($list)){
            $res = SystemPullNewModel::set($data);
        }else{
            $res = SystemPullNewModel::where('id','>',0)->update($data);
        }
        if (!$res){
            return JsonService::fail('提交失败');
        }else{
            return JsonService::successful('设置成功');
        }
    }
}