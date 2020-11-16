<?php


namespace app\ebapi\model\store;


use basic\ModelBasic;
use think\Cache;

class StoreCategory extends ModelBasic
{

    /**
     * 给图片增加域名
     * @param $value
     * @return array
     * gyz
     */
    protected function getSliderImageAttr($value)
    {
        $list = json_decode($value,true)?:[];
        if (!empty($list)){
            foreach ($list as $k=>&$v){
//                $v = substr(request()->domain(),0,-1).$v;
                $v = request()->domain().$v;
            }
        }
        return $list;
//        return json_decode($value,true)?:[];
    }

    /**
     * 给单个图片增加域名
     * @param $value
     * gyz
     */
    protected function getPicAttr($v)
    {
        $v = request()->domain().$v;
        return $v;
    }

    public static function pidByCategory($pid,$field = '*',$limit = 0)
    {
        $model = self::where('pid',$pid)->where('is_show',1)->order('sort desc,id desc')->field($field);
        if($limit) $model->limit($limit);
        return $model->select();
    }

    public static function pidBySidList($pid)
    {
        return self::where('pid',$pid)->field('id,cate_name,pid')->select();
    }

    public static function cateIdByPid($cateId)
    {
        return self::where('id',$cateId)->value('pid');
    }

    /*
     * 获取一级和二级分类
     * @return array
     * */
    public static function getProductCategory($expire=800)
    {
        $list = self::where('is_show',1)->order('sort asc,id asc')->select();
        return $list;
    }

    /**
     * TODO  获取首页展示的二级分类  排序默认降序
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function byIndexList($limit = 4,$field = 'id,cate_name,pid,pic'){
        return self::where('pid','>',0)->where('is_show',1)->field($field)->order('sort DESC')->limit($limit)->select();
    }

}