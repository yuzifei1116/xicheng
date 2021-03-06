<?php

namespace app\seller\model\store;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 评论管理 model
 * Class StoreProductReply
 * @package app\admin\model\store
 */
class StoreProductReply extends ModelBasic
{
    use ModelTrait;

    protected function getPicsAttr($value)
    {
        return json_decode($value,true);
    }
    /*
     * 设置where条件
     * @param array $where
     * @param string $alias
     * @param object $model
     * */
    public static function valiWhere($where,$alias='',$model=null)
    {
        $model=is_null($model) ? new self() : $model;
        if($alias){
            $model=$model->alias($alias);
            $alias.='.';
        }
        if(isset($where['title']) && $where['title']!='') $model=$model->where("{$alias}comment",'LIKE',"%$where[title]%");
        if(isset($where['is_reply']) && $where['is_reply']!='') $model= $where['is_reply'] >= 0 ? $model->where("{$alias}is_reply",$where['is_reply']) : $model->where("{$alias}is_reply",'GT',0);
//        if(isset($where['producr_id']) && $where['producr_id']!=0) $model=$model->where('product_id',$where['producr_id']);
        return $model->where("{$alias}is_del",0);
    }

    public static function getProductImaesList($where,$mer_id)
    {
        $list=self::valiWhere($where,'a')
            ->group('p.id')->join('__WECHAT_USER__ u','u.uid=a.uid')
            ->join("__STORE_PRODUCT__ p",'a.product_id=p.id')
            ->where('p.mer_id',$mer_id)
            ->field(['p.id','p.image','p.store_name','p.price'])
            ->page($where['page'],$where['limit'])->select();
        $list=count($list) ? $list->toArray() : [];
        foreach ($list as &$item){
            $item['store_name']=self::getSubstrUTf8($item['store_name'],10,'UTF-8','');
        }

        return $list;
    }

    public static function getProductReplyList($where,$mer_id)
    {
        $data=self::valiWhere($where,'a')->join("__STORE_PRODUCT__ p",'a.product_id=p.id')
            ->join('__WECHAT_USER__ u','u.uid=a.uid')
            ->where('p.mer_id',$mer_id)
            ->where('a.product_id',$where['producr_id'])
            ->order('a.add_time desc,a.is_reply asc')
            ->field('a.*,u.nickname,u.headimgurl as avatar')
            ->page((int)$where['message_page'],(int)$where['limit'])
            ->select();
        $data=count($data) ? $data->toArray() : [];
        foreach ($data as &$item){
            $item['time']=\service\UtilService::timeTran($item['add_time']);
        }
        $count=self::valiWhere($where,'a')->join('__WECHAT_USER__ u','u.uid=a.uid')->join("__STORE_PRODUCT__ p",'a.product_id=p.id')->where('a.product_id',$where['producr_id'])->where('p.mer_id',$mer_id)->count();
        return ['list'=>$data,'count'=>$count];
    }
    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where,$mer_id){
        $model = new self;
        if($where['comment'] != '')  $model = $model->where('r.comment','LIKE',"%$where[comment]%");
        if($where['is_reply'] != ''){
            if($where['is_reply'] >= 0){
                $model = $model->where('r.is_reply',$where['is_reply']);
            }else{
                $model = $model->where('r.is_reply','GT',0);
            }
        }
        if($where['product_id'])  $model = $model->where('r.product_id',$where['product_id']);
        $model = $model->alias('r')->join('__WECHAT_USER__ u','u.uid=r.uid');
        $model = $model->join('__STORE_PRODUCT__ p','p.id=r.product_id');
        $model = $model->where('r.is_del',0)->where('mer_id',$mer_id);
        $model = $model->field('r.*,u.nickname,u.headimgurl,p.store_name');
        $model = $model->order('r.add_time desc,r.is_reply asc');
        return self::page($model,function($itme){

        },$where);
    }

}