<?php

namespace app\admin\model\merchant;

use service\JsonService;
use service\PHPExcelService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\store\StoreCategory as CategoryModel;
use app\admin\model\order\StoreOrder;
use app\admin\model\system\SystemConfig;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class MerchantGrade extends ModelBasic
{
    use ModelTrait;

    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public static function MerchantGradeList($where)
    {
        $data = self::order('id asc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $count=self::count();
        return compact('data','count');
    }

    public static function getMerchantGrade($grade)
    {
        return self::where('grade',$grade)->count();
    }

    /**
     * 获取该商铺等级的抽成
     * @param $grade
     * @auth:pyp
     * @date:2020/6/13 10:06
     */
    public static function getMerchantGradePercentage($grade)
    {
        return self::where('grade',$grade)->value('percentage');
    }

    /**
     * 获取商铺等级列表
     * @auth:pyp
     * @date:2020/6/13 11:32
     */
    public static function getMerchantGradeName()
    {
        return self::order('grade asc')->column('id,title');
    }
}