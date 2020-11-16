<?php


namespace app\ebapi\model\store;


use basic\ModelBasic;
use app\core\util\GroupDataService;

class StoreSeckill extends ModelBasic
{

    protected function getImagesAttr($value)
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
    protected function getImageAttr($v)
    {
        $v = request()->domain().$v;
        return $v;
    }

    public static function getSeckillCount()
    {
        $seckillTime = GroupDataService::getData('routine_seckill_time')?:[];//秒杀时间段
        $timeInfo=['time'=>0,'continued'=>0];
        foreach($seckillTime as $key=>$value){
            $currentHour = date('H');
            $activityEndHour = bcadd((int)$value['time'],(int)$value['continued'],0);
            if($currentHour >= (int)$value['time'] && $currentHour < $activityEndHour && $activityEndHour < 24){
                $timeInfo=$value;
                break;
            }
        }
        if($timeInfo['time']==0) return 0;
        $activityEndHour = bcadd((int)$timeInfo['time'],(int)$timeInfo['continued'],0);
        $startTime = bcadd(strtotime(date('Y-m-d')),bcmul($timeInfo['time'],3600,0));
        $stopTime = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
        return self::where('is_del',0)->where('status',1)->where('start_time','<=',$startTime)->where('stop_time','>=',$stopTime)->count();
    }
    /*
     * 获取秒杀列表
     *
     * */
    public static function seckillList($page,$limit,$mer_id=0)
    {
        $list = self::where('is_del',0)
            ->where('status',1)
            ->where('stop_time','>=',time());
        if ($mer_id>0) $list = $list->where('mer_id',$mer_id);
        $list = $list->order('sort desc,start_time asc')
            ->page($page,$limit)
            ->cache('seckill_'.$page.'_'.$limit."_".$mer_id,5)
            ->select();
        if ($list->isEmpty()) return [];
        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
            $v['stop_time'] = date('Y-m-d H:i:s',$v['stop_time']);
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        return $list;
    }

    public static function getSkillProductInfo($id,$field = '*')
    {
        $time = time();
        return self::where('id',$id)
            ->where('is_del',0)
            ->where('status',1)
            ->where('stop_time','>',$time)
            ->field($field)->find();
    }

    /**
     * 获取所有秒杀产品
     * @param string $field
     * @return array
     */
    public static function getListAll($offset = 0,$limit = 10,$field = 'id,product_id,image,title,price,ot_price,start_time,stop_time,stock,sales'){
        $time = time();
        $model = self::where('is_del',0)->where('status',1)->where('stock','>',0)->field($field)
            ->where('start_time','<',$time)->where('stop_time','>',$time)->order('sort DESC,add_time DESC');
        $model = $model->limit($offset,$limit);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }
    /**
     * 获取热门推荐的秒杀产品
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
     * 获取一条秒杀产品
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

    public static function initFailSeckill()
    {
        self::where('is_hot',1)->where('is_del',0)->where('status','<>',1)->where('stop_time','<',time())->update(['status'=>'-1']);
    }

    public static function idBySimilaritySeckill($id,$limit = 4,$field='*')
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

    /** 获取秒杀产品库存
     * @param $id
     * @return mixed
     */
    public static function getProductStock($id){
        return self::where('id',$id)->value('stock');
    }

    /**
     * 修改秒杀库存
     * @param int $num
     * @param int $seckillId
     * @return bool
     */
    public static function decSeckillStock($num = 0,$seckillId = 0){
        $res = false !== self::where('id',$seckillId)->dec('stock',$num)->inc('sales',$num)->update();
        return $res;
    }

    /**
     * 增加库存较少销量
     * @param int $num
     * @param int $seckillId
     * @return bool
     */
    public static function incSeckillStock($num = 0,$seckillId = 0){
        $res = false !== self::where('id',$seckillId)->inc('stock',$num)->dec('sales',$num)->update();
        return $res;
    }
}