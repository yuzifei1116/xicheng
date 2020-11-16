<?php
namespace app\ebapi\controller;

use app\ebapi\model\store\StoreProductRelation;
use app\ebapi\model\store\StoreProductReply;
use app\ebapi\model\store\StoreSeckill;
use app\core\util\GroupDataService;
use service\JsonService;
use service\UtilService;


/**
 * 小程序秒杀api接口
 * Class SeckillApi
 * @package app\ebapi\controller
 *
 */
class SeckillApi extends AuthController
{
    /**
     * 秒杀列表页
     * @return \think\response\Json
     */
    public function seckill_index(){
        $lovely = GroupDataService::getData('routine_lovely')?:[];//banner图
        $seckillTime = GroupDataService::getData('routine_seckill_time')?:[];//秒杀时间段
        $seckillTimeIndex = 0;
        if(count($seckillTime)){
            foreach($seckillTime as $key=>&$value){
                $currentHour = date('H');
                $activityEndHour = bcadd((int)$value['time'],(int)$value['continued'],0);
                if($activityEndHour > 24){
                    $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                    $value['state'] = '即将开始';
                    $value['status'] = 2;
                    $value['stop'] = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                }else{
                    if($currentHour >= (int)$value['time'] && $currentHour < $activityEndHour){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '抢购中';
                        $value['stop'] = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                        $value['status'] = 1;
                        if(!$seckillTimeIndex) $seckillTimeIndex = $key;
                    }else if($currentHour < (int)$value['time']){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '即将开始';
                        $value['status'] = 2;
                        $value['stop'] = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                    }else if($currentHour >= $activityEndHour){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '已结束';
                        $value['status'] = 0;
                        $value['stop'] = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                    }
                }
            }
        }
        $data['lovely'] = isset($lovely[0]) ? $lovely[0] : '';
        $data['seckillTime'] = $seckillTime;
        $data['seckillTimeIndex'] = $seckillTimeIndex;
        return JsonService::successful($data);
    }

    public function seckill_list(){
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $mer_id = input('get.mer_id/d',0);
        $seckillInfo = StoreSeckill::seckillList($page,$limit,$mer_id);
        return JsonService::successfuljson($seckillInfo);
    }
    /**
     * 秒杀详情页
     * @param Request $request
     * @return \think\response\Json
     */
    public function seckill_detail()
    {
//        $data = UtilService::postMore(['id']);
//        $id = $data['id'];
        $id = input('id/d',0);
        if(!$id || !($storeInfo = StoreSeckill::getSkillProductInfo($id))) return JsonService::failjson('商品不存在或已下架!');
//        $storeInfo['userLike'] = StoreProductRelation::isProductRelation($storeInfo['product_id'],$this->userInfo['uid'],'like','product_seckill');
//        $storeInfo['like_num'] = StoreProductRelation::productRelationNum($storeInfo['product_id'],'like','product_seckill');
//        $storeInfo['userCollect'] = StoreProductRelation::isProductRelation($storeInfo['product_id'],$this->userInfo['uid'],'collect','product_seckill');
        $storeInfo['uid'] = $this->userInfo['uid'];
        $data['storeInfo'] = $storeInfo;
//        setView($this->userInfo['uid'],$id,$storeInfo['product_id'],'viwe');
//        $data['reply'] = StoreProductReply::getRecProductReply($storeInfo['product_id']);
//        $data['replyCount'] = StoreProductReply::productValidWhere()->where('product_id',$storeInfo['id'])->count();
        return JsonService::successfuljson($data);
    }
}