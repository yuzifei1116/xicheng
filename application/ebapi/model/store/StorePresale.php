<?php

namespace app\ebapi\model\store;


use basic\ModelBasic;

class StorePresale extends ModelBasic
{

    protected function getImagesAttr($value)
    {
        return json_decode($value,true)?:[];
    }
//    protected function getStartTimeAttr($value)
//    {
//        return date('Y-m-d H:i:s',$value);
//    }
//    protected function getStopTimeAttr($value)
//    {
//        return date('Y-m-d H:i:s',$value);
//    }
//    protected function getAddTimeAttr($value)
//    {
//        return date('Y-m-d H:i:s',$value);
//    }


    protected function getWkStartTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    protected function getWkStopTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }



    /**
     * 获取所有预售产品
     * @param string $field
     * @return array
     */
    public static function getListAll($field = 'id,product_id,image,title,price,ot_price,start_time,stop_time,stock,sales'){
        $time = time();
        $model = self::where('is_del',0)->where('status',1)->where('stock','>',0)->field($field)
            ->where('start_time','<',$time)->where('stop_time','>',$time)->order('sort DESC,add_time DESC');
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }
    /**
     * 获取热门推荐的预售产品
     * @param int $limit
     * @param string $field
     * @return array
     */
    public static function getHotList($limit = 0,$field = 'id,product_id,image,title,price,ot_price,start_time,stop_time,stock')
    {
        $time = time();
        $model = self::where('is_hot',1)->where('is_del',0)->where('status',1)->where('stock','>',0)->field($field)
            ->where('start_time','<',$time)->where('stop_time','>',$time)->order('sort DESC,add_time DESC');
        if($limit) $model->limit($limit);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }

    /**
     * 获取一条预售产品
     * @param $id
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getValidProduct($id,$field = '*')
    {
        $time = time();
        return self::where('id',$id)->where('is_del',0)->where('status',1)->where('start_time','<',$time)->where('stop_time','>',$time)
            ->field($field)->find();
    }

    public static function getPresaleProductInfo($id,$field = '*')
    {
        $time = time();
        $data = self::where('id',$id)
            ->where('is_del',0)
            ->where('status',1)
            ->where('stop_time','>',$time)
            ->field($field)->find();
        if (empty($data)) return [];
        $data = $data->toArray();
        $data['image'] = request()->domain() . $data['image'];
        $images = $data['images'];
        foreach ($images as $k=>&$v){
            $v = request()->domain() . $v;
        }
        $data['images'] = $images;
        return $data;

    }

    public static function initFailPresale()
    {
        self::where('is_hot',1)->where('is_del',0)->where('status','<>',1)->where('stop_time','<',time())->update(['status'=>'-1']);
    }

    public static function idBySimilarityPresale($id,$limit = 4,$field='*')
    {
        $time = time();
        $list = [];
        $productId = self::where('id',$id)->value('product_id');
        if($productId){
            $list = array_merge($list, self::where('product_id',$productId)->where('id','<>',$id)
                ->where('is_del',0)->where('status',1)->where('stock','>',0)
                ->field($field)->where('start_time','<',$time)->where('stop_time','>',$time)
                ->order('sort DESC,add_time DESC')->limit($limit)->select()->toArray());
        }
        $limit = $limit - count($list);
        if($limit){
            $list = array_merge($list,self::getHotList($limit,$field));
        }

        return $list;
    }

    public static function getProductStock($id){
        $stock = self::where('id',$id)->value('stock');
        if(self::where('id',$id)->where('limit_num','gt',$stock)->count()){//单次购买的产品多于库存
            return $stock;
        }else{
            return self::where('id',$id)->value('limit_num');
        }
    }

    /**
     * 修改预售库存
     * @param int $num
     * @param int $presaleId
     * @return bool
     */
    public static function decPresaleStock($num = 0,$presaleId = 0){
        $res = false !== self::where('id',$presaleId)->dec('stock',$num)->inc('sales',$num)->update();
        return $res;
    }

    public static function presaleList($page,$limit,$mer_id=0)
    {
        $list = self::where('is_del',0)
            ->where('status',1)
            ->where('stop_time','>=',time());
        if ($mer_id>0) $list = $list->where('mer_id',$mer_id);
        $list = $list->order('sort desc,start_time asc')
            ->page($page,$limit)
            ->cache('presale_'.$page.'_'.$limit.'_'.$mer_id,60)
            ->select();
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
            $v['stop_time'] = date('Y-m-d H:i:s',$v['stop_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $v['image'] = request()->domain() . $v['image'];
        }
        return $list;
    }

}