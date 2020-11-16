<?php
namespace app\gzh\controller;

use app\core\model\routine\RoutineFormId;//待完善
use app\core\model\user\UserLevel;
use app\gzh\model\gzh\Ad;
use app\gzh\model\gzh\Banner;
use app\gzh\model\system\SystemGroupData;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UtilService;
use think\Request;
use app\core\behavior\GoodsBehavior;//待完善
use app\gzh\model\store\StoreCouponUser;
use app\gzh\model\store\StoreOrder;
use app\gzh\model\store\StoreProductAttrValue;
use app\gzh\model\store\StoreCart;
use app\gzh\model\user\User;
use app\gzh\model\store\StorePink;
use app\core\util\WechatService;
use app\core\util\GroupDataService;

use app\core\model\user\UserBill;
use app\core\model\system\SystemUserLevel;
use app\core\model\system\SystemUserTask;
use app\gzh\model\store\StoreCategory;
use app\gzh\model\store\StoreCouponIssue;
use app\gzh\model\store\StoreProduct;
use service\HttpService;
use service\UploadService;
use service\CacheService;
use think\Cache;

/**
 * 公众号内容等接口 api接口
 * Class AuthApi
 * @package app\gzh\controller
 *
 */
class AuthApi extends AuthController
{
    /**
     * 获取jsdk
     * @return array|string
     * gyz
     */
    public function get_jsdk()
    {
        $url = request()->get('url');
        $url = empty($url) ? "" :base64_decode($url);
        $res = WechatService::jsSdk($url);
        return $res;
    }

    /**
     * 首页
     */
    public function index()
    {
        $banner = Banner::getAllShow();
        $menus = GroupDataService::getData('store_home_menus')?:[]; //TODO 首页按钮
        $roll = GroupDataService::getData('store_home_roll_news')?:[]; //TODO 首页滚动新闻
        if(!empty($roll)) $roll = array_column($roll,'info');
        $ad = $this->get_ad();
        $is_best = StoreProduct::getTypeProduct('is_best',10,'*');
        $is_hot = StoreProduct::getTypeProduct('is_hot',3,'*');
        $is_benefit = StoreProduct::getTypeProduct('is_benefit',10,'*');
        $is_hot_img = GroupDataService::getData('store_home_hot_bgimg')?:[]; //TODO 首页滚动新闻
        $is_hot_img = empty($is_hot_img) ? '' : $is_hot_img[0]['img'];
//        dump($ad);die;

//        $activity = GroupDataService::getData('routine_home_activity',3)?:[];//TODO 首页活动区域图片
//        $info['fastInfo'] = SystemConfigService::get('fast_info');//TODO 快速选择简介
//        $info['bastInfo'] = SystemConfigService::get('bast_info');//TODO 精品推荐简介
//        $info['firstInfo'] = SystemConfigService::get('first_info');//TODO 首发新品简介
//        $info['salesInfo'] = SystemConfigService::get('sales_info');//TODO 促销单品简介
//        $logoUrl = SystemConfigService::get('routine_index_logo');//TODO 促销单品简介
//        if(strstr($logoUrl,'http')===false) $logoUrl=SystemConfigService::get('site_url').$logoUrl;
//        $logoUrl=str_replace('\\','/',$logoUrl);
//        $fastNumber = (int)SystemConfigService::get('fast_number');//TODO 快速选择分类个数
//        $bastNumber = (int)SystemConfigService::get('bast_number');//TODO 精品推荐个数
//        $firstNumber = (int)SystemConfigService::get('first_number');//TODO 首发新品个数
//        $info['fastList'] = StoreCategory::byIndexList($fastNumber);//TODO 快速选择分类个数
//        $info['bastList'] = StoreProduct::getBestProduct('id,image,store_name,cate_id,price,ot_price,IFNULL(sales,0) + IFNULL(ficti,0) as sales,unit_name,sort',$bastNumber,$this->uid);//TODO 精品推荐个数
//        $info['firstList'] = StoreProduct::getNewProduct('id,image,store_name,cate_id,price,unit_name,sort',$firstNumber);//TODO 首发新品个数
//        $info['bastBanner'] = GroupDataService::getData('routine_home_bast_banner')?:[];//TODO 首页精品推荐图片
//        $benefit = StoreProduct::getBenefitProduct('id,image,store_name,cate_id,price,ot_price,stock,unit_name,sort',3);//TODO 首页促销单品
//        $lovely =[];//TODO 首发新品顶部图
//        $likeInfo = StoreProduct::getHotProduct('id,image,store_name,cate_id,price,unit_name,sort',3);//TODO 热门榜单 猜你喜欢
//        $couponList=StoreCouponIssue::getIssueCouponList($this->uid,3);
        return JsonService::successfuljson(compact('banner','menus','roll','ad','is_best','is_hot','is_benefit','is_hot_img'));
    }

    public function recharge_marketing()
    {
        $list = SystemGroupData::getAllValue('recharge_marketing');
        return JsonService::successfuljson($list);
    }

    /**
     * 获取广告位 按照位置分组
     * @return array
     * @author: gyz
     * @Time: 2020/4/28 17:52
     */
    protected function get_ad()
    {
        $list = Ad::getAllShow();
//        dump($list);
        $res = [];
        foreach ($list as $k=>$v){
            $res[$v['weizhi']][] = $v;
        }
        return $res;
    }

    /**
     * 获取 分销海报
     * @author: gyz
     * @Time: 2020/5/20 18:21
     */
    public function get_spread_poster()
    {
        $poster = GroupDataService::getData('store_spread_poster')?:[];
        return JsonService::successfuljson($poster);
    }


    /**
     * 加入购物车
     * @author: gyz
     * @Time: 2020/4/30 9:56
     */
    public function set_cart()
    {
        $productId = input('productId/d',0);
        $cartNum = input('cartNum/d',1);
        $uniqueId = input('uniqueId','');

        $mer_id = StoreProduct::where('id',$productId)->value('mer_id');

        if (!$productId) return JsonService::failjson('参数错误');
        $res = StoreCart::setCart($this->uid, $productId, $cartNum, $uniqueId, 'product',0,0,0,0,0,$mer_id);
        if (!$res) return JsonService::failjson(StoreCart::getErrorInfo());
        return JsonService::successfuljson('ok', ['cartId' => $res->id]);
    }

    /**
     * 获取购物车数量
     * @author: gyz
     * @Time: 2020/4/30 10:06
     */
    public function get_cart_num()
    {
        $res = StoreCart::getUserCartNum($this->uid, 'product');
        return JsonService::successfuljson('ok', $res);
    }

    /**
     * 购物车列表
     * @author: gyz
     * @Time: 2020/4/30 10:10
     */
    public function get_cart_list()
    {
        $page = input('page/d',1);
        $limit = input('limit/d',10);
        $res = StoreCart::getUserProductCartList($this->userInfo['uid'],'',0,$page,$limit);
        return JsonService::successfuljson($res);
    }

    /**
     * 删除购物车产品
     * @author: gyz
     * @Time: 2020/4/30 10:23
     */
    public function remove_cart()
    {
        $ids = input('ids','');
        if (!$ids) return JsonService::failjson('参数错误!');
        $res = StoreCart::removeUserCart($this->uid, $ids);
        if($res)
            return JsonService::successfuljson();
        else
            return JsonService::failjson('清除失败！');
    }

    /**
     * 修改购物车产品数量
     * @author: gyz
     * @Time: 2020/4/30 13:39
     */
    public function change_cart_num()
    {
        $cartId = input('cartId/d',0);
        $cartNum = input('cartNum/d',0);

        if (!$cartId || !$cartNum) return JsonService::failjson('参数错误!');
        $res = StoreCart::changeUserCartNum($cartId, $cartNum, $this->uid);
        if ($res)  return JsonService::successfuljson();
        return JsonService::failjson(StoreCart::getErrorInfo('修改失败'));
    }

    /*
     * 获取小程序订单列表统计数据
     *
     * */
    public function get_order_data()
    {
        return JsonService::successfuljson(StoreOrder::getOrderData($this->uid));
    }

    /*
     * 未支付的订单取消订单回退积分,回退优惠券,回退库存
     * @param string $order_id 订单id
     * */
    public function cancel_order($order_id = '')
    {
        if (StoreOrder::cancelOrder($order_id))
            return JsonService::successfuljson('取消订单成功');
        else
            return JsonService::failjson(StoreOrder::getErrorInfo());
    }

    /*
     * 再来一单
     *
     * */
    public function again_order($uni = ''){
        if(!$uni) return JsonService::failjson('参数错误!');
        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'],$uni);
        if(!$order) return JsonService::failjson('订单不存在!');
        $order = StoreOrder::tidyOrder($order,true);
        $res = array();
        foreach ($order['cartInfo'] as $v) {
            if($v['combination_id']) return JsonService::failjson('拼团产品不能再来一单，请在拼团产品内自行下单!');
            else if($v['bargain_id']) return JsonService::failjson('砍价产品不能再来一单，请在砍价产品内自行下单!');
            else if($v['seckill_id']) return JsonService::failjson('秒杀产品不能再来一单，请在砍价产品内自行下单!');
            else $res[] = StoreCart::setCart($this->userInfo['uid'], $v['product_id'], $v['cart_num'], isset($v['productInfo']['attrInfo']['unique']) ? $v['productInfo']['attrInfo']['unique'] : '', 'product', 0, 0);
        }
        $cateId = [];
        foreach ($res as $v){
            if(!$v) return JsonService::failjson('再来一单失败，请重新下单!');
            $cateId[] = $v['id'];
        }
        return JsonService::successfuljson('ok',implode(',',$cateId));
    }

    /**
     * 订单页面
     * @param Request $request
     * @return \think\response\Json
     */
    public function confirm_order(Request $request)
    {
        $data = UtilService::postMore(['cartId'], $request);
        $cartId = $data['cartId'];
        if (!is_string($cartId) || !$cartId) return JsonService::failjson('请提交购买的商品');
        $cartGroup = StoreCart::getUserProductCartList($this->userInfo['uid'], $cartId, 1);
//        dump($cartGroup);
        if (!empty($cartGroup)){
            foreach ($cartGroup['valid'] as $k=>$v){
                $pids[] = $v['product_id'];
            }
        }
        if (isset($pids)){
            $data['pids'] = implode(',',$pids);
        }else{
            $data['pids'] = '';
        }
        if (count($cartGroup['invalid'])) return JsonService::failjson($cartGroup['invalid'][0]['productInfo']['store_name'] . '已失效!');
        if (!$cartGroup['valid']) return JsonService::failjson('请提交购买的商品');
        $cartInfo = $cartGroup['valid'];
        $priceGroup = StoreOrder::getOrderPriceGroup($cartInfo);
        $other = [
            'offlinePostage' => SystemConfigService::get('offline_postage'),
            'integralRatio' => SystemConfigService::get('integral_ratio')
        ];
        $usableCoupon = StoreCouponUser::beUsableCoupon($this->userInfo['uid'], $priceGroup['totalPrice']);
        $cartIdA = explode(',', $cartId);
        if (count($cartIdA) > 1) $seckill_id = 0;
        else {
            $seckillinfo = StoreCart::where('id', $cartId)->find();
            if ((int)$seckillinfo['seckill_id'] > 0) $seckill_id = $seckillinfo['seckill_id'];
            else $seckill_id = 0;
        }
        $data['usableCoupon'] = $usableCoupon;
        $data['seckill_id'] = $seckill_id;
        $data['cartInfo'] = $cartInfo;
        $data['priceGroup'] = $priceGroup;
        $data['orderKey'] = StoreOrder::cacheOrderInfo($this->userInfo['uid'], $cartInfo, $priceGroup, $other);
        $data['offlinePostage'] = $other['offlinePostage'];
        $vipId=UserLevel::getUserLevel($this->uid);
        $this->userInfo['vip']=$vipId !==false ? true : false;
        if($this->userInfo['vip']){
            $this->userInfo['vip_id']=$vipId;
            $this->userInfo['discount']=UserLevel::getUserLevelInfo($vipId,'discount');
        }
        $data['userInfo']=$this->userInfo;
        $data['integralRatio'] = $other['integralRatio'];
        return JsonService::successfuljson($data);
    }

//--------------------------------------



    /*
     * 获取个人中心菜单
     * */
    public function get_my_naviga()
    {
        return JsonService::successfuljson(['routine_my_menus'=>GroupDataService::getData('routine_my_menus')]);
    }
    /**
     * 过度查$uniqueId
     * @param string $productId
     * @param int $cartNum
     * @param string $uniqueId
     * @return \think\response\Json
     */
    public function unique()
    {
        $productId = $_GET['productId'];
        if (!$productId || !is_numeric($productId)) return JsonService::failjson('参数错误');
        $uniqueId = StoreProductAttrValue::where('product_id', $productId)->value('unique');
        $data = $this->set_cart($productId, $cartNum = 1, $uniqueId);
        if ($data == true) {
            return JsonService::successfuljson('ok');
        }
    }

    /*
     * 获取授权登录log
     * */
    public function get_logo_url()
    {
        $routine_logo=SystemConfigService::get('routine_logo');
        return JsonService::successfuljson(['logo_url'=>str_replace('\\','/',$routine_logo)]);
    }
    /**
     * TODO 获取首页推荐不同类型产品的轮播图和产品
     * @param int $type
     */
    public function get_index_groom_list($type = 1){
        $info['banner'] = [];
        $info['list'] = [];
        if($type == 1){//TODO 精品推荐
            $info['banner'] = GroupDataService::getData('routine_home_bast_banner')?:[];//TODO 首页精品推荐图片
            $info['list'] = StoreProduct::getBestProduct('id,image,store_name,cate_id,price,ot_price,IFNULL(sales,0) + IFNULL(ficti,0) as sales,unit_name,sort');//TODO 精品推荐个数
        }else if($type == 2){//TODO 热门榜单
            $info['banner'] = GroupDataService::getData('routine_home_hot_banner')?:[];//TODO 热门榜单 猜你喜欢推荐图片
            $info['list'] = StoreProduct::getHotProduct('id,image,store_name,cate_id,price,ot_price,unit_name,sort,IFNULL(sales,0) + IFNULL(ficti,0) as sales',0,$this->uid);//TODO 热门榜单 猜你喜欢
        }else if($type == 3){//TODO 首发新品
            $info['banner'] = GroupDataService::getData('routine_home_new_banner')?:[];//TODO 首发新品推荐图片
            $info['list'] = StoreProduct::getNewProduct('id,image,store_name,cate_id,price,ot_price,unit_name,sort,IFNULL(sales,0) + IFNULL(ficti,0) as sales',0,$this->uid);//TODO 首发新品
        }else if($type == 4){//TODO 促销单品
            $info['banner'] = GroupDataService::getData('routine_home_benefit_banner')?:[];//TODO 促销单品推荐图片
            $info['list'] = StoreProduct::getBenefitProduct('id,image,store_name,cate_id,price,ot_price,stock,unit_name,sort');//TODO 促销单品
        }
        return JsonService::successfuljson($info);
    }


    /**
     * 猜你喜欢  加载
     * @param Request $request
     */
    public function get_hot_product(){
        $data = UtilService::getMore([['offset',0],['limit',0]],$this->request);
        $hot = StoreProduct::getHotProductLoading('id,image,store_name,cate_id,price,unit_name,sort',$data['offset'],$data['limit']);//猜你喜欢
        return $this->successful($hot);
    }

    /*
     * 根据经纬度获取当前地理位置
     * */
    public function getlocation($latitude='',$longitude=''){
        $location=HttpService::getRequest('https://apis.map.qq.com/ws/geocoder/v1/',
            ['location'=>$latitude.','.$longitude,'key'=>'U65BZ-F2IHX-CGZ4I-73I7L-M6FZF-TEFCH']);
        $location=$location ? json_decode($location,true) : [];
        if($location && isset($location['result']['address'])){
            try{
                $address=$location['result']['address_component']['street'];
                return $this->successful(['address'=>$address]);
            }catch (\Exception $e){
                return $this->fail('获取位置信息失败!');
            }
        }else{
            return $this->fail('获取位置信息失败!');
        }
    }

    /*
     * 根据key来取系统的值
     * */
    public function get_system_config_value($name=''){
        if($name=='') return JsonService::failjson('缺少参数');
        $name=str_replace(SystemConfigService::$ProtectedKey,'',$name);
        if(strstr($name,',')!==false){
            return $this->successful(SystemConfigService::more($name));
        }else{
            $value=SystemConfigService::get($name);
            $value=is_array($value) ? $value[0] : $value;
            return $this->successful([$name=>$value]);
        }
    }

    /*
     * 获取系统
     * */
    public function get_system_group_data_value($name='',$multi=0){
        if($name=='') return $this->successful([$name=>[]]);
        if($multi==1){
            $name=json_decode($name,true);
            $value=[];
            foreach ($name as $item){
                $value[$item]=GroupDataService::getData($item)?:[];
            }
            return $this->successful($value);
        }else{
            $value= GroupDataService::getData($name)?:[];
            return $this->successful([$name=>$value]);
        }
    }
    /*
     * 删除指定资源
     *
     * */
    public function delete_image(){
        $post=UtilService::postMore([
            ['pic',''],
        ]);
        if($post['pic']=='') return $this->fail('缺少删除资源');
        $type=['php','js','css','html','ttf','otf'];
        $post['pic']=substr($post['pic'],1);
        $ext=substr($post['pic'],-3);
        if(in_array($ext,$type)) return $this->fail('非法操作');
        if(strstr($post['pic'],'uploads')===false) return $this->fail('非法操作');
        try{
            if(file_exists($post['pic'])) unlink($post['pic']);
            if(strstr($post['pic'],'s_')!==false){
                $pic=str_replace(['s_'],'',$post['pic']);
                if(file_exists($pic)) unlink($pic);
            }
            return $this->successful('删除成功');
        }catch (\Exception $e){
            return $this->fail('刪除失败',['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }
    }

    /**
     * 上传图片
     * @param string $filename
     * @return \think\response\Json
     */
    public function upload($dir='')
    {
        $data = UtilService::postMore([
            ['filename',''],
        ],$this->request);
        if(Cache::has('start_uploads_'.$this->uid) && Cache::get('start_uploads_'.$this->uid) >= 100) return $this->fail('非法操作');
        $res = UploadService::image($data['filename'],$dir ? $dir: 'store/comment');
        if($res->status == 200){
            if(Cache::has('start_uploads_'.$this->uid))
                $start_uploads=(int)Cache::get('start_uploads_'.$this->uid);
            else
                $start_uploads=0;
            $start_uploads++;
            Cache::set('start_uploads_'.$this->uid,$start_uploads,86400);
            return $this->successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => UploadService::pathToUrl($res->dir)]);
        }else
            return $this->fail($res->error);
    }



    /**
     * 获取提现银行
     */
    public function get_user_extract_bank(){
        $extractBank = SystemConfigService::get('user_extract_bank')?:[];//提现银行
        $extractBank = str_replace("\r\n","\n",$extractBank);//防止不兼容
        $data['extractBank'] = explode("\n",$extractBank);
        $data['minPrice'] = SystemConfigService::get('user_extract_min_price');//提现最低金额
        return $this->successful($data);
    }

    /**
     * 收集发送模板信息的formID
     * @param string $formId
     */
    public function get_form_id($formId = ''){
        if($formId==''){
            list($formIds)=UtilService::postMore([
                ['formIds',[]]
            ],$this->request,true);
            foreach ($formIds as $formId){
                RoutineFormId::SetFormId($formId,$this->uid);
            }
        }else
            RoutineFormId::SetFormId($formId,$this->uid);
        return $this->successful('');
    }

    /**
     * 刷新数据缓存
     */
    public function refresh_cache(){
        `php think optimize:schema`;
        `php think optimize:autoload`;
        `php think optimize:route`;
        `php think optimize:config`;
    }

    /*
    * 清除系统全部缓存
    * @return
    * */
    public function clear_cache()
    {
        \think\Cache::clear();
    }

    /*
     * 获取会员等级
     * */
    public function get_level_list()
    {
        return JsonService::successfuljson(SystemUserLevel::getLevelList($this->uid));
    }

    /*
     * 获取某个等级的任务
     * @param int $level_id 等级id
     * @return json
     * */
    public function get_task($level_id=''){
        return JsonService::successfuljson(SystemUserTask::getTashList($level_id,$this->uid));
    }

    /*
     * 检测用户是否可以成为会员
     * */
    public function set_level_complete()
    {
        return JsonService::successfuljson(UserLevel::setLevelComplete($this->uid));
    }

    /*
     * 记录用户分享次数
     * */
    public function set_user_share()
    {
        return JsonService::successfuljson(UserBill::setUserShare($this->uid));
    }


}
