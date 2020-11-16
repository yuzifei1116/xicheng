<?php

namespace app\seller\controller\widget;

use app\seller\controller\AuthController;
use app\seller\model\store\StoreProduct;
use app\seller\model\store\StoreCategory;

/**
 * 选择产品控制器
 * Class SystemFile
 * @package app\seller\controller\system
 *
 */
class Product extends AuthController
{
    /**
     * 产品列表
     * @return \think\response\Json
     */
   public function index()
   {
       //分类标题
       $typearray = StoreCategory::getAllOne();
       $cate_id = $typearray[0]['id'];

       if(is_numeric(input('cate_id'))){
           $cate_id = input('cate_id');
           session('cate_id',$cate_id);
       }else{
           $cate_id = session('cate_id')?session('cate_id'):$cate_id;
       }
       $this->assign('cate_id',$cate_id);

       $cate_ids = StoreCategory::getTwoIds($cate_id);
       $product = StoreProduct::getAllByCategory($cate_ids,$this->sellerId);

       $this->assign(compact('typearray'));
       $this->assign($product);
       return $this->fetch('widget/product');
   }



}
