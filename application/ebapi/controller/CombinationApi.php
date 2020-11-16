<?php
namespace app\ebapi\controller;

use app\ebapi\model\store\StorePink;
use app\gzh\model\store\StoreProductRelation;
use app\gzh\model\store\StoreProductReply;
use app\ebapi\model\store\StoreCombination;
use app\core\util\GroupDataService;
use service\JsonService;
use service\UtilService;


/**
 * 拼团api接口
 * Class CombinationApi
 * @package app\gzh\controller
 *
 */
class CombinationApi extends AuthController
{


    public function combination_list(){
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $mer_id = input('get.mer_id/d',0);
        $combinationInfo = StoreCombination::combinationList($page,$limit,$mer_id);
        return JsonService::successfuljson($combinationInfo);
    }
    /**
     * 拼团详情页 查找多少人参团了。
     * @param Request $request
     * @return \think\response\Json
     */
    public function combination_detail()
    {
//        $data = UtilService::postMore(['id']);
//        $id = $data['id'];
        $id = input('id/d',0);
        if(!$id || !($storeInfo = StoreCombination::getValidProduct($id))) return JsonService::failjson('商品不存在或已下架!');

        $map1 = [
            'com_type' => 1,
            'cid' => $id,
        ];
        $has_people = StorePink::where($map1)->count() ? : 0;

//        $storeInfo['userLike'] = StoreProductRelation::isProductRelation($storeInfo['product_id'],$this->userInfo['uid'],'like','product_combination');
//        $storeInfo['like_num'] = StoreProductRelation::productRelationNum($storeInfo['product_id'],'like','product_combination');
//        $storeInfo['userCollect'] = StoreProductRelation::isProductRelation($storeInfo['product_id'],$this->userInfo['uid'],'collect','product_combination');
        $storeInfo['uid'] = $this->userInfo['uid'];
        $data['storeInfo'] = $storeInfo;
        $data['has_people'] = $has_people;
        $data['need_people'] = $storeInfo['min_people'];
//        setView($this->userInfo['uid'],$id,$storeInfo['product_id'],'viwe');
//        $data['reply'] = StoreProductReply::getRecProductReply($storeInfo['product_id']);
//        $data['replyCount'] = StoreProductReply::productValidWhere()->where('product_id',$storeInfo['id'])->count();
        return JsonService::successfuljson($data);
    }

    public function pink_detail_one()
    {
        $data = UtilService::getMore([
            ['pink_id',0],
            ['product_id',0],
            ['page',1],
            ['limit',200],
        ],$this->request);

        if (empty($data['pink_id'])) return JsonService::failjson('缺少拼团信息');

        $whereid = $data['pink_id'];
        $pinfo = StorePink::where('id',$data['pink_id'])->find();
        $pinfo = empty($pinfo) ? [] : $pinfo->toArray();
        if ($pinfo['com_type'] == 1 || $pinfo['com_type']==4){
            if ($pinfo['k_id']>0) $whereid = $pinfo['k_id'];
        }

//        dump($data);die;

//        $pinkinfo = StorePink::where('id',$data['pink_id'])->find();
//        $pinkinfo = empty($pinkinfo) ? [] : $pinkinfo->toArray();
//        if (empty($pinkinfo)) return JsonService::fail('无此拼团');
//
//        $combination_id = $pinkinfo['cid'];
//        $map = [
//            'id' => $combination_id,
//        ];
//        $cominfo = StoreCombination::where($map)->find();

        $model = new StorePink();
        $model = $model->alias('p');
        $model = $model->field('p.*,u.nickname,u.avatar');
        $model = $model->where('p.k_id|p.id',$whereid);
//        $model = $model->where('is_refund',0);//个人中心查不到
        $model = $model->join('__USER__ u','u.uid = p.uid');
        $model = $model->order('id asc');
        $list = $model->select();
        $list = empty($list) ? [] : $list->toArray();
        $has_people = count($list);
//        echo $model->getLastSql();die;

        $cominfo = StoreCombination::where('id', $list[0]['cid'])->find();
        if (empty($cominfo)) return false;
        $cominfo = $cominfo->toArray();

        $has_people_last = $cominfo['min_people'] - $has_people;

        $res = [
            'has_people' => $has_people,
            'has_people_last' => $has_people_last,
            'cominfo' => $cominfo,
            'people_list' => $list,
        ];

//        dump($res);die;
        return JsonService::successfuljson($res);


    }
}