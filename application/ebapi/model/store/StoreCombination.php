<?php

namespace app\ebapi\model\store;


use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 拼团model
 * Class StoreCombination
 * @package app\admin\model\store
 */
class StoreCombination extends ModelBasic
{
    use ModelTrait;

    /**
     * 检查 开团状态，能否开团，是否到购买限制，是否有库存，是否。。。。
     * @param $combination_id
     * @param $uid
     * @return array|bool
     * @author: gyz
     * @Time: 2020/6/2 10:33
     */
    public static function checkComStatus($combination_id, $uid)
    {
        $return = [];

        $map = [
            'id' => $combination_id,
            'is_del' =>0,
            'status' => 1,
            'is_show' => 1,
        ];
        $cominfo = self::where($map)->find();
        if (empty($cominfo)){
            self::setErrorInfo('拼团商品已下架或无此商品！');
            return false;
        }
        //判断限购
        $map1 = [
            'uid' => $uid,
            'cid' => $combination_id,
            'com_type' => $cominfo['com_type'],
            'is_refund' => 0
        ];
        $hasbuy = StorePink::where($map1)->sum('total_num');
        if ($hasbuy>=$cominfo['limit_num']){
            self::setErrorInfo('超过购买限制：'.$cominfo['limit_num']);
            return false;
        }

        switch ($cominfo['com_type']){
            case 1:
                //检查拼团人数是否到最大 修改status  2
                //待优化：刚到人数的时候不会修改状态，。。。。。。
                $map1 = [
                    'cid' => $combination_id,
                    'com_type' => $cominfo['com_type'],
                ];
                $haspeople = StorePink::where($map1)->count();
                if ($haspeople >= $cominfo['min_people']){
                    StorePink::where($map1)->update(['status'=>2]);
                }

                break;
            case 2:
                //判断是否超过小团开团数量
                $map2 = [
                    'cid' => $combination_id,
                    'com_type' => $cominfo['com_type'],
//                    'is_refund' => 0,
                    'k_id' => 0
                ];
                $haskai = StorePink::where($map2)->count();
                if ($haskai>=$cominfo['max_team']){
                    self::setErrorInfo('已达到最大开团数量');
                    return false;
                }
                //判断重复参团，但是可以重复开团
                if ($cominfo['is_rejoin'] == 0 && $hasbuy>0){
                    self::setErrorInfo('不可重复开团');
                    return false;
                }

                //判断团长免单,免多少
                if ($cominfo['is_kai_free']==1){
                    $return['free_price'] = 999999999;
                }elseif ($cominfo['is_kai_free']==2){
                    $return['free_price'] = $cominfo['free_price'];
                }

                break;
            case 3:
                //判断是否超过小团开团数量
                $map2 = [
                    'cid' => $combination_id,
                    'com_type' => $cominfo['com_type'],
//                    'is_refund' => 0,
                    'k_id' => 0
                ];
                $haskai = StorePink::where($map2)->count();
                if ($haskai>=$cominfo['max_team']){
                    self::setErrorInfo('已达到最大开团数量');
                    return false;
                }
                //判断重复参团，但是可以重复开团
                if ($cominfo['is_rejoin'] == 0 && $hasbuy>0){
                    self::setErrorInfo('不可重复开团');
                    return false;
                }

                //判断团长免单,免多少
                if ($cominfo['is_kai_free']==1){
                    $return['free_price'] = 99999999;
                }elseif ($cominfo['is_kai_free']==2){
                    $return['free_price'] = $cominfo['free_price'];
                }

                //----以上同2，以下是老带新

                //判断老带新，只能老用户开团
                $userinfo = User::getUserInfo($uid);
//                dump($userinfo);die;
                if($userinfo['pay_count']==0){
                    self::setErrorInfo('新用户只能参团');
                    return false;
                }

                break;
            case 4:
                //检查拼团是否到最大人数 ，关闭此团 已完成
                if (!$cominfo['full_people_auto_over']) break;//不是满人成团 就不关闭团

                $map4 = [
                    'cid' => $combination_id,
                    'com_type' => $cominfo['com_type'],
                ];
                $haspeople = StorePink::where($map4)->count();

                $fullpeople = 0;
                for($i=10;$i>=1;$i--){
                    if ($cominfo['stair_people'.$i]>0) {
                        $fullpeople = $cominfo['stair_people'.$i];
                        break;
                    }
                }
                if ($haspeople >= $fullpeople){
                    StorePink::where($map4)->update(['status'=>2]);
                    $ress = StoreCombination::where('id',$combination_id)->update(['is_over'=>1]);
                    self::setErrorInfo('团已满');
                    return false;
                }

                break;
        }

        return $return;//true;
    }

    public static function combinationList($page,$limit,$mer_id=0)
    {
        $list = self::where('is_del',0)
            ->where('status',1)
            ->where('com_type',1)
            ->where('stop_time','>=',time());
        if ($mer_id>0) $list = $list->where('mer_id', $mer_id);
        $list = $list->order('sort desc,start_time asc')
            ->page($page,$limit)
            ->cache('combination_'.$page.'_'.$limit.'_'.$mer_id,60)
            ->select();
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['images'] = json_decode($v['images']);
            $v['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
            $v['stop_time'] = date('Y-m-d H:i:s',$v['stop_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        return $list;
    }

    public static function getValidProduct($id,$field = '*')
    {
        $time = time();
        $res = self::where('id',$id)
            ->where('is_del',0)
            ->where('status',1)
            ->where('start_time','<',$time)
            ->where('stop_time','>',$time)
            ->field($field)
            ->find();
        $res['images'] = json_decode($res['images']);
        return $res;
    }



    //----------------------------------

    /**
     * @param $where
     * @return array
     */
    public static function get_list($length=10){
        if($post=input('post.')){
            $where=$post['where'];
            $model = new self();
            $model = $model->alias('c');
            $model = $model->join('StoreProduct s','s.id=c.product_id');
            $model = $model->where('c.is_show',1)->where('c.is_del',0)->where('c.start_time','LT',time())->where('c.stop_time','GT',time());
            if(!empty($where['search'])){
                $model = $model->where('c.title','like',"%{$where['search']}%");
                $model = $model->whereOr('s.keyword','like',"{$where['search']}%");
            }
            $model = $model->field('c.*,s.price as product_price');
            if($where['key']){
                if($where['sales']==1){
                    $model = $model->order('c.sales desc');
                }else if($where['sales']==2){
                    $model = $model->order('c.sales asc');
                }
                if($where['price']==1){
                    $model = $model->order('c.price desc');
                }else if($where['price']==2){
                    $model = $model->order('c.price asc');
                }
                if($where['people']==1){
                    $model = $model->order('c.people asc');
                }
                if($where['default']==1){
                    $model = $model->order('c.sort desc,c.id desc');
                }
            }else{
                $model = $model->order('c.sort desc,c.id desc');
            }
            $page=is_string($where['page'])?(int)$where['page']+1:$where['page']+1;
            $list = $model->page($page,$length)->select()->toArray();   
            return ['list'=>$list,'page'=>$page];
        }
    }
    /**
     * 获取所有拼团数据
     * @param int $limit
     * @param int $length
     * @return mixed
     */
    public static function getAll($limit = 0,$length = 0){
        $model = new self();
        $model = $model->alias('c');
        $model = $model->join('StoreProduct s','s.id=c.product_id');
        $model = $model->field('c.*,s.price as product_price');
        $model = $model->order('c.sort desc,c.id desc');
        $model = $model->where('c.is_show',1);
        $model = $model->where('c.is_del',0);
        $model = $model->where('c.start_time','LT',time());
        $model = $model->where('c.stop_time','GT',time());
        if($limit && $length) $model = $model->limit($limit,$length);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }
    /**
     * 获取一条拼团数据
     * @param $id
     * @return mixed
     */
    public static function getCombinationOne($id){
        $model = new self();
        $model = $model->alias('c');
        $model = $model->join('StoreProduct s','s.id=c.product_id');
        $model = $model->field('c.*,s.price as product_price,s.cate_id');
        $model = $model->where('c.is_show',1);
        $model = $model->where('c.is_del',0);
        $model = $model->where('c.id',$id);
        $model = $model->where('c.start_time','LT',time());
        $model = $model->where('c.stop_time','GT',time()-86400);
        $list = $model->find();
        if($list) return $list->toArray();
        else return [];
    }
    /**
     * 获取产品状态
     * @param $id
     * @return mixed
     */
    public static function isValidCombination($id){
        $model = new self();
        $model = $model->where('id',$id);
        $model = $model->where('is_del',0);
        $model = $model->where('is_show',1);
        return $model->count();
    }
    /**
     * 判断库存是否足够
     * @param $id
     * @param $cart_num
     * @return int|mixed
     */
    public static function getCombinationStock($id,$cart_num){
        $stock = self::where('id',$id)->value('stock');
        return $stock > $cart_num ? $stock : 0;
    }

    /**
     * 获取推荐的拼团产品
     * @return mixed
     */
    public static function getCombinationHost($limit = 0){
        $model = new self();
        $model = $model->alias('c');
        $model = $model->join('StoreProduct s','s.id=c.product_id');
        $model = $model->field('c.id,c.image,c.price,c.sales,c.title,c.people,s.price as product_price');
        $model = $model->where('c.is_del',0);
        $model = $model->where('c.is_host',1);
        $model = $model->where('c.is_show',1);
        $model = $model->where('c.start_time','LT',time());
        $model = $model->where('c.stop_time','GT',time());
        if($limit) $model = $model->limit($limit);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }

    /**
     * 修改销量和库存
     * @param $num
     * @param $CombinationId
     * @return bool
     */
    public static function decCombinationStock($num,$CombinationId)
    {
        $res = false !== self::where('id',$CombinationId)->dec('stock',$num)->inc('sales',$num)->update();
        return $res;
    }

    /**
     * 增加浏览量
     * @param int $id
     * @return bool
     */
    public static function editIncBrowse($id = 0){
        if(!$id) return false;
        $browse = self::where('id',$id)->value('browse');
        $browse = bcadd($browse,1,0);
        self::edit(['browse'=>$browse],$id);
    }
}