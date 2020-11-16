<?php

namespace app\seller\controller;

use app\seller\model\merchant\MerchantList;
use app\seller\model\system\SystemAdmin;
use app\seller\model\system\SystemMenus;
use app\seller\model\system\SystemRole;
use behavior\admin\SystemBehavior;
use service\HookService;
use think\Url;

/**
 * 基类 所有控制器继承的类
 * Class AuthController
 * @package app\seller\controller
 */
class AuthController extends SystemBasic
{
    /**
     * 当前登陆管理员信息
     * @var
     */
    protected $sellerInfo;

    /**
     * 当前登陆管理员ID
     * @var
     */
    protected $sellerId;

    /**
     * 当前管理员权限
     * @var array
     */
//    protected $auth = [];

    protected $skipLogController = ['index','common'];

    protected function _initialize()
    {
        parent::_initialize();
        if(!MerchantList::hasActiveSeller()) return $this->redirect('Login/index');
        try{
            $sellerInfo = MerchantList::activeSellerInfoOrFail();
        }catch (\Exception $e){
            return $this->failed(MerchantList::getErrorInfo($e->getMessage()),Url::build('Login/index'));
        }
        $this->sellerInfo = $sellerInfo;
        $this->sellerId = $sellerInfo['id'];
        $this->getActiveSellerInfo();
//        $this->auth = SystemAdmin::activeAdminAuthOrFail();
//        $this->sellerInfo->level === 0 || $this->checkAuth();
        $this->assign('_Seller',$this->sellerInfo);
//        HookService::listen('seller_visit',$this->sellerInfo,'system',false,SystemBehavior::class);
    }


    protected function checkAuth($action = null,$controller = null,$module = null,array $route = [])
    {
        static $allAuth = null;
        if($allAuth === null) $allAuth = SystemRole::getAllAuth();
        if($module === null) $module = $this->request->module();
        if($controller === null) $controller = $this->request->controller();
        if($action === null) $action = $this->request->action();
        if(!count($route)) $route = $this->request->route();
        if(in_array(strtolower($controller),$this->skipLogController,true)) return true;
        $nowAuthName = SystemMenus::getAuthName($action,$controller,$module,$route);
        $baseNowAuthName =  SystemMenus::getAuthName($action,$controller,$module,[]);
        if((in_array($nowAuthName,$allAuth) && !in_array($nowAuthName,$this->auth)) || (in_array($baseNowAuthName,$allAuth) && !in_array($baseNowAuthName,$this->auth)))
            exit($this->failed('没有权限访问!'));
        return true;
    }


    /**
     * 获得当前用户最新信息
     * @return SystemAdmin
     */
    protected function getActiveSellerInfo()
    {
        $sellerId = $this->sellerId;
        $sellerInfo = MerchantList::getValidSellerInfoOrFail($sellerId);
        if(!$sellerInfo) $this->failed(MerchantList::getErrorInfo('请登陆!'));
        $this->sellerInfo = $sellerInfo;
        MerchantList::setLoginInfo($sellerInfo);
        return $sellerInfo;
    }
}