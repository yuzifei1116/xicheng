<?php

namespace app\wap\controller;


use app\admin\model\system\SystemConfig;
use app\wap\model\store\StoreBargain;
use app\wap\model\store\StoreBargainUser;
use app\wap\model\store\StoreBargainUserHelp;
use app\wap\model\store\StoreCategory;
use app\wap\model\store\StoreCoupon;
use app\wap\model\store\StoreSeckill;
use app\wap\model\store\StoreCouponIssue;
use app\wap\model\store\StoreCouponIssueUser;
use app\wap\model\store\StoreCouponUser;
use app\wap\model\store\StorePink;
use app\wap\model\store\StoreProductReply;
use app\wap\model\store\StoreCart;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StoreProduct;
use app\wap\model\store\StoreProductAttr;
use app\wap\model\store\StoreProductRelation;
use app\wap\model\user\User;
use app\wap\model\user\WechatUser;
use app\wap\model\store\StoreCombination;
use app\core\util\GroupDataService;
use app\core\util\SystemConfigService;
use service\UtilService;
use think\Cache;
use think\Request;
use think\Url;
use service\JsonService;
use app\wap\model\site\SiteAdminister;

/**
 * 加气站点类
 * Class Site
 * @package app\wap\controller
 */
class Site extends WapBasic
{
    /**
     *加气站点首页
     * @return mixed
     *
     */
    public function index()
    {
        $list=SiteAdminister::getMapSitelist();
        //print_r($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 加气站详情
     * @param $id
     */
    public function detail($id)
    {
        $detail=SiteAdminister::getDetail($id);
//        print_r($detail);
        $this->assign('detail',$detail);
        return $this->fetch();
    }


    /**
     * 站点列表
     * gyz
     */
    public function site_list()
    {
        $list=SiteAdminister::getSitelist();

        $this->assign(compact('list'));
        return $this->fetch();
    }

    /**
     * 百度地图导航
     * pyp
     */
//    public function map()
//    {
//        return $this->fetch();
//    }
}