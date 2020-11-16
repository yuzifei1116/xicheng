<?php


namespace app\mini\controller;


use app\core\model\activity\Activity;
use app\core\model\ad\Ad;
use app\core\model\banner\Banner;
use app\core\model\menu\Menu;
use app\core\model\shop\ShopCategory;
use app\core\model\shop\ShopProduct;
use app\core\model\stay\StayRoom;
use app\core\model\stay\StayType;
use app\core\util\GroupDataService;
use app\gzh\model\user\User;
use service\JsonService;
use think\Cache;

class PublicApi extends AuthController
{
    /*
    * 白名单不验证token 如果传入token执行验证获取信息，没有获取到用户信息
    */
    public static function whiteList()
    {
        return [
            'index',
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

    public function my()
    {
        $userInfo = User::get($this->uid);
        return JsonService::successfuljson($userInfo);
    }
}