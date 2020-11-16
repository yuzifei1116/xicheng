<?php

namespace app\seller\model\merchant;

use service\PHPExcelService;
use think\Db;
use think\Session;
use traits\ModelTrait;
use basic\ModelBasic;
use app\seller\model\store\StoreCategory as CategoryModel;
use app\seller\model\order\StoreOrder;
use app\seller\model\system\SystemConfig;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\seller\model\store
 */
class MerchantList extends ModelBasic
{
    use ModelTrait;

    /**
     * 检查用户登陆状态
     * @return bool
     */
    public static function hasActiveSeller()
    {
        return Session::has('sellerId') && Session::has('sellerInfo');
    }

    /**
     * 获得登陆用户信息
     * @return mixed
     */
    public static function activeSellerInfoOrFail()
    {
        $sellerInfo = Session::get('sellerInfo');
        if(!$sellerInfo)  exception('请登陆');
        if(!$sellerInfo['status']) exception('该账号已被禁止!');
        return $sellerInfo;
    }

    /**
     * 获得有效管理员信息
     * @param $id
     * @return static
     */
    public static function getValidSellerInfoOrFail($id)
    {
        $sellerInfo = self::get($id);
        if(!$sellerInfo) exception('用户不存在!');
        if(!$sellerInfo['status']) exception('该账号已被禁止!');
        return $sellerInfo;
    }

    /**
     *  保存当前登陆用户信息
     */
    public static function setLoginInfo($sellerInfo)
    {
        Session::set('sellerId',$sellerInfo['id']);
        Session::set('sellerInfo',$sellerInfo);
    }

    /**
     * 用户登陆
     * @param string $account 账号
     * @param string $pwd 密码
     * @param string $verify 验证码
     * @return bool 登陆成功失败
     */
    public static function login($account,$pwd)
    {
        $sellerInfo = self::get(compact('account'));
        if(!$sellerInfo) return self::setErrorInfo('登陆的账号不存在!');
        if($sellerInfo['pwd'] != md5($pwd)) return self::setErrorInfo('账号或密码错误，请重新输入');
        if(!$sellerInfo['status']) return self::setErrorInfo('该账号已被关闭!');
        self::setLoginInfo($sellerInfo);
//        HookService::afterListen('system_seller_login',$sellerInfo,null,false,SystemBehavior::class);
        return true;
    }

    /**
     * 清空当前登陆用户信息
     */
    public static function clearLoginInfo()
    {
        Session::delete('sellerInfo');
        Session::delete('sellerId');
        Session::clear();
    }

    /**
     * 查询改商铺的商品是否需要审核
     * @param $id
     * @return mixed
     * @auth:pyp
     * @date:2020/6/12 15:09
     */
    public static function getProductExamine($id)
    {
        return self::where('id',$id)->value('product_examine');
    }

    /**
     * 查询改商铺的商铺等级
     * @param $id
     * @return mixed
     * @auth:pyp
     * @date:2020/6/12 15:10
     */
    public static function getMerchantGrade($id)
    {
        return self::where('id',$id)->value('grade');
    }

    /**
     * 获取店铺资金
     * @param $id
     * @auth:pyp
     * @date:2020/6/13 17:06
     */
    public static function getMerchantPrice($id)
    {
        return self::where('id',$id)->value('price');
    }

    public static function getGrade($id)
    {
        return self::where('id',$id)->value('grade');
    }
}