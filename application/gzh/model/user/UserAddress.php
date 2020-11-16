<?php

namespace app\gzh\model\user;


use basic\ModelBasic;
use traits\ModelTrait;

class UserAddress extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setDefaultAddress($id,$uid)
    {
        self::beginTrans();
        $res1 = self::where('uid',$uid)->update(['is_default'=>0]);
        $res2 = self::where('id',$id)->where('uid',$uid)->update(['is_default'=>1]);
        $res =$res1 !== false && $res2 !== false;
        self::checkTrans($res);
        return $res;
    }

    public static function userValidAddressWhere($model=null,$prefix = '')
    {
        if($prefix) $prefix .='.';
        $model = self::getSelfModel($model);
        return $model->where("{$prefix}is_del",0);
    }

    public static function getUserValidAddressList($uid,$page=1,$limit=8,$field = '*')
    {
        return self::userValidAddressWhere()->where('uid',$uid)->order('is_default desc,add_time DESC')->field($field)->page((int)$page,(int)$limit)->select()->toArray()?:[];
    }

    public static function getUserDefaultAddress($uid,$field = '*')
    {
        return self::userValidAddressWhere()->where('uid',$uid)->where('is_default',1)->field($field)->find();
    }

    public static function getAddr($uid)
    {
        $addr = self::where('uid',$uid)
            ->where('is_default',1)
            ->where('is_del',0)
            ->field('province,city,district,detail')
            ->find();
        if (empty($addr)) return [];
        return $addr['province'].$addr['city'].$addr['district'].$addr['detail'];
    }

    public static function getPhone($uid)
    {
        return self::where('uid',$uid)
            ->where('is_default',1)
            ->where('is_del',0)
            ->value('phone');
    }
//    public static function setDefaultAddress($id,$uid)
//    {
//        self::beginTrans();
//        $res1 = self::where('uid',$uid)->update(['is_default'=>0]);
//        $res2 = self::where('id',$id)->where('uid',$uid)->update(['is_default'=>1]);
//        $res =$res1 !== false && $res2 !== false;
//        self::checkTrans($res);
//        return $res;
//    }
//
//    public static function userValidAddressWhere($model=null,$prefix = '')
//    {
//        if($prefix) $prefix .='.';
//        $model = self::getSelfModel($model);
//        return $model->where("{$prefix}is_del",0);
//    }
//
//    public static function getUserValidAddressList($uid,$field = '*')
//    {
//        return self::userValidAddressWhere()->where('uid',$uid)->order('add_time DESC')->field($field)->select()->toArray()?:[];
//    }
//
//    public static function getUserDefaultAddress($uid,$field = '*')
//    {
//        return self::userValidAddressWhere()->where('uid',$uid)->where('is_default',1)->field($field)->find();
//    }
}