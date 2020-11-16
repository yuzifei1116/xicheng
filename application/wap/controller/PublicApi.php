<?php

namespace app\wap\controller;


use app\wap\model\store\StoreCombination;
use app\admin\model\system\SystemGroup;
use app\admin\model\system\SystemGroupData;
use app\wap\model\store\StoreCategory;
use app\wap\model\store\StoreCouponIssue;
use app\wap\model\store\StoreProduct;
use app\wap\model\wap\ArticleCategory;
use service\FileService;
use service\JsonService;
use service\UtilService;
use think\Cache;

class PublicApi
{
    public function get_cid_article($cid = 0,$first = 0,$limit = 8)
    {
        $list = ArticleCategory::cidByArticleList($cid,$first,$limit,'id,title,image_input,visit,add_time,synopsis,url')?:[];

        foreach ($list as &$article){
            $article['add_time'] = date('Y-m-d H:i',$article['add_time']);
        }
        return JsonService::successful('ok',$list);
    }

    public function get_video_list($key = '', $first = 0,$limit = 8)
    {
        $gid = SystemGroup::where('config_name',$key)->value('id');
        if(!$gid){
            $list = [];
        }else{
            $video = SystemGroupData::where('gid',$gid)->where('status',1)->order('sort DESC,add_time DESC')->limit($first,$limit)->select();
            $list = SystemGroupData::tidyList($video);
        }
        return JsonService::successful('ok',$list);
    }

    public function get_issue_coupon_list($limit = 2)
    {
        $list = StoreCouponIssue::validWhere('A')->join('__STORE_COUPON__ B','A.cid = B.id')
            ->field('A.*,B.coupon_price,B.use_min_price')->order('id DESC')->limit($limit)->select()->toArray();
        return JsonService::successful($list);
    }

    public function get_category_product_list($limit = 4)
    {
        $cateInfo = StoreCategory::where('is_show',1)->where('pid',0)->field('id,cate_name,pic')
            ->order('sort DESC')->select()->toArray();
        $result = [];
        $StoreProductModel = new StoreProduct;
        foreach ($cateInfo as $k=>$cate){
            $cate['product'] = $StoreProductModel::alias('A')->where('A.is_del',0)->where('A.is_show',1)
                ->where('A.mer_id',0)->where('B.pid',$cate['id'])
                ->join('__STORE_CATEGORY__ B','B.id = A.cate_id')
                ->order('A.is_benefit DESC,A.sort DESC,A.add_time DESC')
                ->limit($limit)->field('A.id,A.image,A.store_name,A.sales,A.price,A.unit_name')->select()->toArray();
            if(count($cate['product']))
                $result[] = $cate;
        }
        return JsonService::successful($result);
    }

    /** 首页获取推荐产品
     * @param int $first
     * @param int $limit
     */
    public function get_product_list($first = 0,$limit = 8)
    {
        $StoreProductmodel = StoreProduct::validWhere();
        if(input('type')=='is_best')
            $StoreProductmodel = $StoreProductmodel->where('is_best',1);
        if(input('type')=='is_hot')
            $StoreProductmodel = $StoreProductmodel->where('is_hot',1);
        if(input('type')=='is_benefit')
            $StoreProductmodel = $StoreProductmodel->where('is_benefit',1);
        if(input('type')=='is_new')
            $StoreProductmodel = $StoreProductmodel->where('is_new',1);
        if(input('type')=='is_postage')
            $StoreProductmodel = $StoreProductmodel->where('is_postage',1);

        $list = $StoreProductmodel->where('mer_id',0)->order('is_best DESC,sort DESC,add_time DESC')
            ->limit($first,$limit)->field('id,image,store_name,sales,price,unit_name')->select()->toArray();

        return JsonService::successful($list);
    }

    public function get_best_product_list($first = 0,$limit = 8)
    {
        $list = StoreProduct::validWhere()->where('mer_id',0)->order('is_best DESC,sort DESC,add_time DESC')
            ->limit($first,$limit)->field('id,image,store_name,sales,price,unit_name')->select()->toArray();
        return JsonService::successful($list);
    }

    public function wechat_media_id_by_image($mediaIds = '')
    {
        if(!$mediaIds) return JsonService::fail('参数错误');
        $mediaIds = explode(',',$mediaIds);
        $temporary = \app\core\util\WechatService::materialTemporaryService();
        $pathList = [];
        $dir='public'.DS.'uploads'.DS.'wechat'.DS.'media';
        foreach ($mediaIds as $mediaId){
            if(!$mediaId) continue;
            try{
                $content = $temporary->getStream($mediaId);
            }catch (\Exception $e){
                continue;
            }
            $name = substr(md5($mediaId),12,20).'.jpg';
            $path = '.'.DS.$dir.DS.$name;
            $file=new FileService();
            $res=$file->create_dir($dir);
            if(!$res) return JsonService::fail('生成文件保存目录失败！请检查权限！');
            $res = file_put_contents($path,$content);
            if($res) $pathList[] = UtilService::pathToUrl($path);
        }
        return JsonService::successful($pathList);
    }


    public function get_pink_host($limit = 0){
        $list = StoreCombination::getCombinationHost($limit);
        if($list) return JsonService::successful($list);
        else return JsonService::successful([]);
    }
}