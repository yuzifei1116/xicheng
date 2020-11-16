<?php

namespace app\admin\model\store;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService;

/**
 * Class StoreCategory
 * @package app\admin\model\store
 */
class StoreCategory extends ModelBasic
{
    use ModelTrait;

    public static function getCateList()
    {
        return self::where('is_show',1)->select();
    }

    /*
     * 异步获取分类列表
     * @param $where
     * @return array
     */
    public static function CategoryList($where)
    {
        $data = self::setWhere($where)->order('id desc')->page($where['page'],$where['limit'])->select();
        foreach ($data as &$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $count = self::setWhere($where)->count();
        return compact('count','data');
    }

    public static function setWhere($where)
    {
        $model = new self();
        if ($where['cate_name'] != '') $model = $model->where('cate_name','like',"%$where[cate_name]%");
        if ($where['is_show'] != '') $model = $model->where('is_show',$where['is_show']);
        return $model;
    }
    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where,$isAjax=false){
        $model = new self;
        if($where['pid'] != '')  $model = $model->where('pid',$where['pid']);
        else if($where['pid']=='' && $where['cate_name']=='') $model = $model->where('pid',0);
        if($where['is_show'] != '')  $model = $model->where('is_show',$where['is_show']);
        if($where['cate_name'] != '')  $model = $model->where('cate_name','LIKE',"%$where[cate_name]%");
        if($isAjax===true){
            if(isset($where['order']) && $where['order']!=''){
                $model=$model->order(self::setOrder($where['order']));
            }else{
                $model=$model->order('sort desc');
            }
            return $model;
        }
        return self::page($model,function ($item){
            if($item['pid']){
                $item['pid_name'] = self::where('id',$item['pid'])->value('cate_name');
            }else{
                $item['pid_name'] = '顶级';
            }
        },$where);
    }

    /**
     * 获取顶级分类
     * @return array
     */
    public static function getCategory($field = 'id,cate_name')
    {
        return self::where('is_show',1)->column($field);
    }

    /**
     * 分级排序列表
     * @param null $model
     * @return array
     */
    public static function getTierList($model = null)
    {
        if($model === null) $model = new self();
        return UtilService::sortListTier($model->order('sort desc,id desc')->select()->toArray());
    }

    public static function delCategory($id){
        $count = self::where('pid',$id)->count();
        if($count)
            return false;
        else{
            return self::del($id);
        }
    }

    /**
     * 获取全部 一级分类
     * @return array
     * @author: gyz
     * @Time: 2020/5/9 16:57
     */
    public static function getAllOne()
    {
        return self::where('is_show',1)->where('pid',0)->select()->toArray();

    }

    public static function getTwoIds($cate_one_id)
    {
        return self::where('is_show',1)->where('pid',$cate_one_id)->column('id');

    }
}