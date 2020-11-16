<?php
namespace app\gzh\controller;

use app\core\model\user\UserBill;
use app\core\model\system\SystemUserLevel;
use app\core\model\system\SystemUserTask;
use app\core\model\user\UserLevel;
use app\gzh\model\store\StoreCategory;
use app\core\model\routine\RoutineFormId;//待完善
use app\gzh\model\store\StoreCouponIssue;
use app\gzh\model\store\StoreProduct;
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
 * @package app\gzh\controller
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
            'get_index_groom_list',
            'get_hot_product',
            'refresh_cache',
            'clear_cache',
            'get_logo_url',
            'get_my_naviga',
        ];
    }

    /*
     * 获取个人中心菜单
     * */
    public function get_my_naviga()
    {
        if (Cache::has('my_index_menu')){
            $my_index_menu = Cache::get('my_index_menu');
        }else{
            $my_index_menu = GroupDataService::getData('my_index_menu');
            Cache::set('my_index_menu',$my_index_menu,300);
        }
        return JsonService::successfuljson($my_index_menu);
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
//        if(Cache::has('start_uploads_'.$this->uid) && Cache::get('start_uploads_'.$this->uid) >= 100) return $this->fail('非法操作');
        if(Cache::has('start_uploads_'.$this->uid) && Cache::get('start_uploads_'.$this->uid) >= 100) return JsonService::failjson('非法操作');
        $res = UploadService::image($data['filename'],$dir ? $dir: 'store/comment');
        if($res->status == 200){
            if(Cache::has('start_uploads_'.$this->uid))
                $start_uploads=(int)Cache::get('start_uploads_'.$this->uid);
            else
                $start_uploads=0;
            $start_uploads++;
            Cache::set('start_uploads_'.$this->uid,$start_uploads,86400);
            return JsonService::successfuljson('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => UploadService::pathToUrl($res->dir)]);
        }else
            return JsonService::failjson($res->error);
    }
}