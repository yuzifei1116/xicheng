<?php
namespace app\ebapi\controller;

use app\gzh\model\store\StoreProductRelation;
use app\gzh\model\store\StoreProductReply;
use app\ebapi\model\store\StorePresale;
use app\core\util\GroupDataService;
use service\JsonService;
use service\UtilService;


/**
 * 预售api接口
 * Class PresaleApi
 * @package app\gzh\controller
 *
 */
class PresaleApi extends AuthController
{
    /**
     * 预售列表页
     * @return \think\response\Json
     */
    public function presale_index(){
        $lovely = GroupDataService::getData('routine_lovely')?:[];//banner图
        $presaleTime = GroupDataService::getData('routine_presale_time')?:[];//预售时间段
        $presaleTimeIndex = 0;
        if(count($presaleTime)){
            foreach($presaleTime as $key=>&$value){
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
                        if(!$presaleTimeIndex) $presaleTimeIndex = $key;
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
        $data['presaleTime'] = $presaleTime;
        $data['presaleTimeIndex'] = $presaleTimeIndex;
        return JsonService::successfuljson($data);
    }

//    public function presale_list(){
//        $data = UtilService::postMore([['time',0],['offset',0],['limit',20]]);
//        if(!$data['time']) return JsonService::failjson('参数错误');
//        $timeInfo = GroupDataService::getDataNumber($data['time']);
//        $activityEndHour = bcadd((int)$timeInfo['time'],(int)$timeInfo['continued'],0);
//        $startTime = bcadd(strtotime(date('Y-m-d')),bcmul($timeInfo['time'],3600,0));
//        $stopTime = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
//        $presaleInfo = StorePresale::presaleList($startTime,$stopTime,$data['offset'],$data['limit']);
//        if(count($presaleInfo)){
//            foreach ($presaleInfo as $key=>&$item){
//                $percent = (int)bcmul(bcdiv($item['sales'],bcadd($item['stock'],$item['sales'],0),2),100,0);
//                $item['percent'] = $percent ? $percent : 10;
//            }
//        }
//        return JsonService::successfuljson($presaleInfo);
//    }
    public function presale_list(){
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $mer_id = input('get.mer_id/d',0);
        $presaleInfo = StorePresale::presaleList($page,$limit,$mer_id);
        return JsonService::successfuljson($presaleInfo);
    }
    /**
     * 预售详情页
     * @param Request $request
     * @return \think\response\Json
     */
    public function presale_detail()
    {
//        $data = UtilService::postMore(['id']);
//        $id = $data['id'];
        $id = input('id/d',0);
        if(!$id || !($storeInfo = StorePresale::getPresaleProductInfo($id))) return JsonService::failjson('商品不存在或已下架!');
//        $storeInfo['userLike'] = StoreProductRelation::isProductRelation($storeInfo['product_id'],$this->userInfo['uid'],'like','product_presale');
//        $storeInfo['like_num'] = StoreProductRelation::productRelationNum($storeInfo['product_id'],'like','product_presale');
//        $storeInfo['userCollect'] = StoreProductRelation::isProductRelation($storeInfo['product_id'],$this->userInfo['uid'],'collect','product_presale');
        $storeInfo['uid'] = $this->userInfo['uid'];
        $data['storeInfo'] = $storeInfo;
//        dump($storeInfo);die;
//        setView($this->userInfo['uid'],$id,$storeInfo['product_id'],'viwe');
//        $data['reply'] = StoreProductReply::getRecProductReply($storeInfo['product_id']);
//        $data['replyCount'] = StoreProductReply::productValidWhere()->where('product_id',$storeInfo['id'])->count();
        return JsonService::successfuljson($data);
    }
}