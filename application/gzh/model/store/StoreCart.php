<?php

namespace app\gzh\model\store;


use app\gzh\model\store\StoreCombination;
use basic\ModelBasic;
use traits\ModelTrait;

class StoreCart extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setCart($uid,$product_id,$cart_num = 1,$product_attr_unique = '',$type='product',$is_new = 0,$combination_id=0,$seckill_id = 0,$bargain_id = 0,$presale_id=0,$mer_id=0)
    {
        if($cart_num < 1) $cart_num = 1;
        if($seckill_id){
            $res_skill = StoreSeckill::getValidProduct($seckill_id);
            if(!$res_skill) return self::setErrorInfo('该产品已下架或删除');
            if ($res_skill['num'] < $cart_num) return self::setErrorInfo('超过限购数量');
            if ($res_skill['stock'] < $cart_num) return self::setErrorInfo('库存不足');

//            $stock=StoreSeckill::where('id',$seckill_id)->value('stock');
//            if(!StoreSeckill::where('id',$seckill_id)->where('num','gt',$stock)->count()){//单次购买的产品多于库存
//                $stock=StoreSeckill::where('id',$seckill_id)->value('num');
//                if($stock<1) return self::setErrorInfo('该产品单次秒杀不足1件');
//            }
//            if($stock<$cart_num) return self::setErrorInfo('该产品库存不足'.$cart_num);
        }elseif($combination_id) {//拼团
            if (!StoreCombination::getCombinationStock($combination_id, $cart_num))
                return self::setErrorInfo('该产品库存不足' . $cart_num);
            if (!StoreCombination::isValidCombination($combination_id))
                return self::setErrorInfo('该产品已下架或删除');
        }elseif($bargain_id){//砍价
            if(!StoreBargain::validBargain($bargain_id))
                return self::setErrorInfo('该产品已下架或删除');
            if(StoreBargain::getBargainStock($bargain_id) < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        }elseif ($presale_id){
            //检查是否在预售期间内，是否有库存
            $res_presale = StorePresale::getValidProduct($presale_id);
            if(!$res_presale) return self::setErrorInfo('该产品已下架或删除');
            if ($res_presale['limit_num'] < $cart_num) return self::setErrorInfo('超过限购数量');
            if ($res_presale['stock'] < $cart_num) return self::setErrorInfo('库存不足');

        }else{
            if(!StoreProduct::isValidProduct($product_id))
                return self::setErrorInfo('该产品已下架或删除');
            if(!StoreProductAttr::issetProductUnique($product_id,$product_attr_unique))
                return self::setErrorInfo('请选择有效的产品属性');
            if(StoreProduct::getProductStock($product_id,$product_attr_unique) < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        }
        $where = [
            'type'=>$type,
            'uid'=>$uid,
            'product_id'=>$product_id,
            'product_attr_unique'=>$product_attr_unique,
            'is_new'=>$is_new,
            'is_pay'=>0,
            'is_del'=>0,
            'combination_id'=>$combination_id,
            'seckill_id'=>$seckill_id,
            'presale_id'=>$presale_id,
            'bargain_id'=>$bargain_id,
            'mer_id' => $mer_id
        ];
        if($cart = self::where($where)->find()){
            $cart->cart_num = $cart_num;
            $cart->add_time = time();
            $cart->save();
            return $cart;
        }else{
            return self::set(compact('uid','product_id','cart_num','product_attr_unique','is_new','type','combination_id','seckill_id','bargain_id','presale_id','mer_id'));
        }

    }

    public static function removeUserCart($uid,$ids)
    {
        return self::where('uid',$uid)->where('id','IN',$ids)->update(['is_del'=>1]);
    }

    public static function getUserCartNum($uid,$type)
    {
        return self::where('uid',$uid)->where('type',$type)->where('is_pay',0)->where('is_del',0)->where('is_new',0)->count();
    }

    public static function changeUserCartNum($cartId,$cartNum,$uid)
    {
        return self::where('uid',$uid)->where('id',$cartId)->update(['cart_num'=>$cartNum]);
    }

    public static function getUserProductCartList($uid,$cartIds='',$status=0,$page=1,$limit=10)
    {
        $productInfoField = 'id,mer_id,image,slider_image,price,cost,ot_price,vip_price,postage,mer_id,give_integral,cate_id,sales,stock,store_name,unit_name,is_show,is_del,is_postage';
        $seckillInfoField = 'id,mer_id,image,images as slider_image,price,cost,ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,is_show,is_del,is_postage';
        $presaleInfoField = 'id,mer_id,image,images as slider_image,price,pre_price,cost,ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,is_show,is_del,is_postage,wk_start_time,wk_stop_time';
        $combinationInfoField = 'id,mer_id,image,images as slider_image,price,cost,ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,is_show,is_del,is_postage,min_people,limit_num';
        $bargainInfoField = 'id,image,min_price as price,price as ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,status as is_show,is_del,is_postage,cost';
        $model = new self();
        $valid = $invalid = [];
        if($cartIds)
            $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)
                ->where('is_del',0);
        else
            $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)->where('is_new',0)
                ->where('is_del',0);
        if($cartIds) $model->where('id','IN',$cartIds);
//        if ($page && $limit) $model = $model->page($page,$limit); //购物车加分页
        $list = $model->select()->toArray();
//        dump($list);die;
        if(!count($list)) return compact('valid','invalid');
        foreach ($list as $k=>$cart) {
            if ($cart['seckill_id']) {
                $product = StoreSeckill::field($seckillInfoField)
                    ->find($cart['seckill_id']);
            }elseif($cart['presale_id']){
                $product = StorePresale::field($presaleInfoField)
                    ->find($cart['presale_id']);
            }elseif($cart['combination_id']){
                $product = StoreCombination::field($combinationInfoField)
                    ->find($cart['combination_id']);
            }elseif($cart['bargain_id']){
                $product = StoreBargain::field($bargainInfoField)
                    ->find($cart['bargain_id']);
            }else{
                $product = StoreProduct::field($productInfoField)
                    ->find($cart['product_id']);
            }
            if(!empty($product)) $product = $product->toArray();
            $cart['productInfo'] = $product;
            //商品不存在
            if (!$product) {
                $model->where('id', $cart['id'])->update(['is_del' => 1]);
                //商品删除或无库存
            } else if (!$product['is_show'] || $product['is_del'] || !$product['stock']) {
                $invalid[] = $cart;
                //商品属性不对应并且没有seckill_id
            } else if (!$cart['bargain_id'] && !$cart['seckill_id'] && !$cart['presale_id'] && !StoreProductAttr::issetProductUnique($cart['product_id'], $cart['product_attr_unique'])) {
                $invalid[] = $cart;
                //正常商品
            } else {
                if ($status) {
//                    if ($cart['seckill_id'] != 0) {
                        if ($cart['product_attr_unique']) {
                            $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                            //商品没有对应的属性
                            if (!$attrInfo || !$attrInfo['stock']){
                                $invalid[] = $cart;
                            }else {
                                $cart['productInfo']['attrInfo'] = $attrInfo;
                                $cart['presaleWkPrice'] = 0;  // 此处是0
                                if ($cart['presale_id']) $cart['presaleWkPrice'] = (float)bcsub($cart['productInfo']['pre_price'],$cart['productInfo']['price'],2);
                                $cart['truePrice'] = (float)bcadd($attrInfo['price'],$cart['presaleWkPrice'],2);

                                $cart['costPrice'] = (float)$attrInfo['cost'];
                                $cart['trueStock'] = $attrInfo['stock'];
                                $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
                                $valid[] = $cart;
                            }
                        } else {
                            $cart['presaleWkPrice'] = 0;
                            if ($cart['presale_id']) $cart['presaleWkPrice'] = (float)bcsub($cart['productInfo']['pre_price'],$cart['productInfo']['price'],2);
//                            $cart['truePrice'] = (float)$cart['productInfo']['price'];
                            $cart['truePrice'] = (float)bcadd($cart['productInfo']['price'],$cart['presaleWkPrice'],2);

                            $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                            $cart['trueStock'] = $cart['productInfo']['stock'];
                            $valid[] = $cart;
                        }
//                    }else{
//
//                    }
                } else {
                    if ($cart['seckill_id'] == 0) {
                        if ($cart['product_attr_unique']) {
                            $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                            //商品没有对应的属性
                            if (!$attrInfo || !$attrInfo['stock'])
                                $invalid[] = $cart;
                            else {
                                $cart['productInfo']['attrInfo'] = $attrInfo;
                                $cart['truePrice'] = (float)$attrInfo['price'];
                                $cart['costPrice'] = (float)$attrInfo['cost'];
                                $cart['trueStock'] = $attrInfo['stock'];
                                $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
                                $valid[] = $cart;
                            }
                        } else {
                            $cart['truePrice'] = (float)$cart['productInfo']['price'];
                            $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                            $cart['trueStock'] = $cart['productInfo']['stock'];
                            $valid[] = $cart;
                        }
                    }

                }

            }
        }
        foreach ($valid as $k=>$cart){
            if($cart['trueStock'] < $cart['cart_num']){
                $cart['cart_num'] = $cart['trueStock'];
                $model->where('id',$cart['id'])->update(['cart_num'=>$cart['cart_num']]);
                $valid[$k] = $cart;
            }
        }
        return compact('valid','invalid');
    }
    public static function getUserProductCartListGwc($uid,$cartIds='',$status=0,$page=1,$limit=10)
    {
        $productInfoField = 'id,image,slider_image,price,cost,ot_price,vip_price,postage,mer_id,give_integral,cate_id,sales,stock,store_name,unit_name,is_show,is_del,is_postage';
        $seckillInfoField = 'id,image,images as slider_image,price,cost,ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,is_show,is_del,is_postage';
        $bargainInfoField = 'id,image,min_price as price,price as ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,status as is_show,is_del,is_postage,cost';
        $model = new self();
        $valid = $invalid = [];
        if($cartIds)
            $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)
                ->where('is_del',0);
        else
            $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)->where('is_new',0)
                ->where('is_del',0);
        if($cartIds) $model->where('id','IN',$cartIds);
        if ($page && $limit) $model = $model->page($page,$limit); //购物车加分页
        $list = $model->select()->toArray();
//        dump($list);die;
        if(!count($list)) return []; //compact('valid','invalid');
        foreach ($list as $k=>&$cart) {
            $list[$k]['is_ok'] = 1;
            if ($cart['seckill_id']) {
                $product = StoreSeckill::field($seckillInfoField)
                    ->find($cart['seckill_id']);
            }elseif($cart['bargain_id']){
                $product = StoreBargain::field($bargainInfoField)
                    ->find($cart['bargain_id']);
            }else{
                $product = StoreProduct::field($productInfoField)
                    ->find($cart['product_id']);
            }
            if(!empty($product)) $product = $product->toArray();
            $cart['productInfo'] = $product;
            //商品不存在
            if (!$product) {
                $model->where('id', $cart['id'])->update(['is_del' => 1]);
                //商品删除或无库存
            } else if (!$product['is_show'] || $product['is_del'] || !$product['stock']) {
//                $invalid[] = $cart;
                $list[$k]['is_ok'] = 0;

                //商品属性不对应并且没有seckill_id
            } else if (!$cart['bargain_id'] && !$cart['seckill_id'] && !StoreProductAttr::issetProductUnique($cart['product_id'], $cart['product_attr_unique'])) {
//                $invalid[] = $cart;
                $list[$k]['is_ok'] = 0;

                //正常商品
            } else {
                if ($status) {
//                    if ($cart['seckill_id'] != 0) {
                    if ($cart['product_attr_unique']) {
                        $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                        //商品没有对应的属性
                        if (!$attrInfo || !$attrInfo['stock']){
//                            $invalid[] = $cart;
                            $list[$k]['is_ok'] = 0;
                        }else {
                            $cart['productInfo']['attrInfo'] = $attrInfo;
                            $cart['truePrice'] = (float)$attrInfo['price'];
                            $cart['costPrice'] = (float)$attrInfo['cost'];
                            $cart['trueStock'] = $attrInfo['stock'];
                            $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
//                            $valid[] = $cart;
                        }
                    } else {
                        $cart['truePrice'] = (float)$cart['productInfo']['price'];
                        $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                        $cart['trueStock'] = $cart['productInfo']['stock'];
//                        $valid[] = $cart;
                    }
//                    }else{
//
//                    }
                } else {
                    if ($cart['seckill_id'] == 0) {
                        if ($cart['product_attr_unique']) {
                            $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                            //商品没有对应的属性
                            if (!$attrInfo || !$attrInfo['stock'])
//                                $invalid[] = $cart;
                                $list[$k]['is_ok'] = 0;
                            else {
                                $cart['productInfo']['attrInfo'] = $attrInfo;
                                $cart['truePrice'] = (float)$attrInfo['price'];
                                $cart['costPrice'] = (float)$attrInfo['cost'];
                                $cart['trueStock'] = $attrInfo['stock'];
                                $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
//                                $valid[] = $cart;
                            }
                        } else {
                            $cart['truePrice'] = (float)$cart['productInfo']['price'];
                            $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                            $cart['trueStock'] = $cart['productInfo']['stock'];
//                            $valid[] = $cart;
                        }
                    }

                }

            }
        }
//        foreach ($valid as $k=>$cart){
//            if($cart['trueStock'] < $cart['cart_num']){
//                $cart['cart_num'] = $cart['trueStock'];
//                $model->where('id',$cart['id'])->update(['cart_num'=>$cart['cart_num']]);
//                $valid[$k] = $cart;
//            }
//        }
//        return compact('valid','invalid');
        $valid = $list;
        return compact('valid');
    }

    /**
     * 拼团
     * @param $uid
     * @param string $cartIds
     * @return array
     */
    public static function getUserCombinationProductCartList($uid,$cartIds='')
    {
        $productInfoField = 'id,image,slider_image,price,cost,ot_price,vip_price,postage,mer_id,give_integral,cate_id,sales,stock,store_name,unit_name,is_show,is_del,is_postage';
        $model = new self();
        $valid = $invalid = [];
        $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)
            ->where('is_del',0);
        if($cartIds) $model->where('id','IN',$cartIds);
        $list = $model->select()->toArray();
        if(!count($list)) return compact('valid','invalid');
        foreach ($list as $k=>$cart){
            $product = StoreProduct::field($productInfoField)
                ->find($cart['product_id'])->toArray();
            $cart['productInfo'] = $product;
            //商品不存在
            if(!$product){
                $model->where('id',$cart['id'])->update(['is_del'=>1]);
            //商品删除或无库存
            }else if(!$product['is_show'] || $product['is_del'] || !$product['stock']){
                $invalid[] = $cart;
            //商品属性不对应
//            }else if(!StoreProductAttr::issetProductUnique($cart['product_id'],$cart['product_attr_unique'])){
//                $invalid[] = $cart;
            //正常商品
            }else{
                $cart['truePrice'] = (float)StoreCombination::where('id',$cart['combination_id'])->value('price');
                $cart['costPrice'] = (float)StoreCombination::where('id',$cart['combination_id'])->value('cost');
                $cart['trueStock'] = StoreCombination::where('id',$cart['combination_id'])->value('stock');
                $valid[] = $cart;
            }
        }

        foreach ($valid as $k=>$cart){
            if($cart['trueStock'] < $cart['cart_num']){
                $cart['cart_num'] = $cart['trueStock'];
                $model->where('id',$cart['id'])->update(['cart_num'=>$cart['cart_num']]);
                $valid[$k] = $cart;
            }
        }

        return compact('valid','invalid');
    }


}