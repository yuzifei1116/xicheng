<?php

namespace app\gzh\model\store;


use basic\ModelBasic;
use think\Cache;

class StoreCategory extends ModelBasic
{
    public static function pidByCategory($pid,$field = '*',$limit = 0)
    {
        $model = self::where('pid',$pid)->where('is_show',1)->order('sort desc,id asc')->field($field);
        if($limit) $model->limit($limit);
        return $model->select();
    }

    public static function pidBySidList($pid)
    {
        return self::where('pid',$pid)->column('id');
    }

    public static function cateIdByPid($cateId)
    {
        return self::where('id',$cateId)->value('pid');
    }

    public static function getProductCategory($expire=800)
    {
//        if(Cache::has('category_one_two')) return Cache::get('category_one_two');

        $parentCategory = self::pidByCategory(0, 'id,cate_name,pic')->toArray();
//        echo self::getLastSql();
//        dump($parentCategory);die;
        foreach ($parentCategory as $k => $category) {
            $category['child'] = self::pidByCategory($category['id'], 'id,cate_name,pic')->toArray();
            $parentCategory[$k] = $category;
        }
//        dump($parentCategory);die;
        Cache::set('category_one_two',$parentCategory,$expire);
        return $parentCategory;
    }

}