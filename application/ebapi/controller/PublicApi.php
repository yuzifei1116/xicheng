<?php
namespace app\ebapi\controller;

use app\core\model\activity\Activity;
use app\core\model\ad\Ad;
use app\core\model\banner\Banner;
use app\core\model\menu\Menu;
use app\core\model\stay\StayRoom;
use app\core\model\user\UserBill;
use app\core\model\system\SystemUserLevel;
use app\core\model\system\SystemUserTask;
use app\core\model\user\UserLevel;
use app\ebapi\model\gzh\RoutineAd;
use app\ebapi\model\store\StoreCategory;
use app\core\model\routine\RoutineFormId;//待完善
use app\ebapi\model\store\StoreCouponIssue;
use app\ebapi\model\store\StoreProduct;
use app\core\util\GroupDataService;
use service\HttpService;
use service\JsonService;
use app\core\util\SystemConfigService;
use service\UploadService;
use service\UtilService;
use service\CacheService;
use think\Cache;

/**
 * 小程序公共接口
 * Class PublicApi
 * @package app\ebapi\controller
 *
 */
class PublicApi extends AuthController
{
    /*
     * 白名单不验证token 如果传入token执行验证获取信息，没有获取到用户信息
     * */
    public static function whiteList()
    {
        return [
            'index',
            'activity',
            'get_index_groom_list',
            'get_hot_product',
            'refresh_cache',
            'clear_cache',
            'get_logo_url',
            'get_my_naviga',
        ];
    }

    /**
     * 首页
     */
    public function index()
    {
        if (Cache::has('banner')){
            $banner = Cache::get('banner');
        }else{
            $banner = Banner::getAllShow(); //TODO 轮播图
            Cache::set('banner',$banner);
        }
        if (Cache::has('roll')){
            $roll = Cache::get('roll');
        }else{
            $roll = GroupDataService::getData('routine_home_roll_news')?:[]; //TODO 首页滚动新闻
            Cache::set('roll',$roll);
        }

        if (Cache::has('menus')){
            $menus = Cache::get('menus');
        }else{
            $menus = GroupDataService::getData('routine_home_menus')?:[]; //TODO 首页按钮
            Cache::set('menus',$menus);
        }
        $ad = Ad::getAd(1);
        $activity = Activity::getTicketList(1,4); //TODO 门票列表
        $menu = Menu::getMenuList(1,4); //TODO 美食列表
        $start_time = strtotime(date('Y-m-d')) + 12*3600;
        $end_time = strtotime(date('Y-m-d',strtotime(date('Y-m-d')) + 24*3600)) + 12*3600;
        $num = (int)(($end_time - $start_time) / (3600*24)); //预定的几天
        $room = StayRoom::roomList(0,$start_time,$end_time,$num,1,4); //TODO 房间列表
        return JsonService::successfuljson(compact('banner','roll','menus','ad','activity','menu','room'));
    }

    public function activity()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',4);
        $activity = Activity::getTicketList($page,$limit); //TODO 门票
        return JsonService::successfuljson($activity);
    }

    /*
     * 获取个人中心菜单
     * */
    public function get_my_naviga()
    {
        if (Cache::has('my_index_menu')){
            $my_index_menu = Cache::get('my_index_menu');
        }else{
            $my_index_menu = GroupDataService::getData('routine_my_menus');
            Cache::set('my_index_menu',$my_index_menu,300);
        }
        return JsonService::successfuljson($my_index_menu);
    }
    /*
     * 获取授权登录log
     * */
    public function get_logo_url()
    {
        $routine_logo=SystemConfigService::get('routine_logo');
        return JsonService::successful(['logo_url'=>str_replace('\\','/',$routine_logo)]);
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
        return JsonService::successful($info);
    }

    /**
     * 获取广告位 按照位置分组
     * @return array
     * @author: gyz
     * @Time: 2020/4/28 17:52
     */
    protected function get_ad()
    {
        $list = RoutineAd::getAllShow();
//        dump($list);
        $res = [];
        foreach ($list as $k=>$v){
            $res[$v['weizhi']][] = $v;
        }
//        dump($res);die;
        return $res;
    }

    /**
     * 首页
     */
    public function index1111(){
        $banner = RoutineBanner::getAllShow();
        $menus = GroupDataService::getData('routine_home_menus')?:[]; //TODO 首页按钮
        $roll = GroupDataService::getData('routine_home_roll_news')?:[]; //TODO 首页滚动新闻
        if(!empty($roll)) $roll = array_column($roll,'info');
        $ad = $this->get_ad();

        $is_best = StoreProduct::getTypeProduct('is_best',10,'*');
        $is_hot = StoreProduct::getTypeProduct('is_hot',3,'*');
        $is_benefit = StoreProduct::getTypeProduct('is_benefit',10,'*');
        $is_hot_img = GroupDataService::getData('store_home_hot_bgimg')?:[]; //TODO 首页滚动新闻
        $is_hot_img = empty($is_hot_img) ? '' : $is_hot_img[0]['img'];
        return JsonService::successfuljson(compact('banner','menus','roll','ad','is_best','is_hot','is_benefit','is_hot_img'));
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
        if($name=='') return JsonService::fail('缺少参数');
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
     * 获取退款理由
     */
    public function get_refund_reason(){
        $reason = SystemConfigService::get('stor_reason')?:[];//退款理由
        $reason = str_replace("\r\n","\n",$reason);//防止不兼容
        $reason = explode("\n",$reason);
        return $this->successful($reason);
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
        return JsonService::successful(SystemUserLevel::getLevelList($this->uid));
    }

    /*
     * 获取某个等级的任务
     * @param int $level_id 等级id
     * @return json
     * */
    public function get_task($level_id=''){
        return JsonService::successful(SystemUserTask::getTashList($level_id,$this->uid));
    }

    /*
     * 检测用户是否可以成为会员
     * */
    public function set_level_complete()
    {
        return JsonService::successful(UserLevel::setLevelComplete($this->uid));
    }

    /*
     * 记录用户分享次数
     * */
    public function set_user_share()
    {
        return JsonService::successful(UserBill::setUserShare($this->uid));
    }

}