<?php



namespace app\core\model\user;

use app\core\util\SystemConfigService;
use app\ebapi\model\store\StoreOrder;
use think\Request;
use think\Session;
use traits\ModelTrait;
use basic\ModelBasic;

class User extends ModelBasic
{
    use ModelTrait;
    public static function updateWechatUser($wechatUser,$uid)
    {
        $userinfo=self::where('uid',$uid)->find();
        if($userinfo->spread_uid){
            return self::edit([
                'nickname'=>$wechatUser['nickname']?:'',
                'avatar'=>$wechatUser['headimgurl']?:'',
            ],$uid,'uid');
        }else {
            $data=[
                'nickname' => $wechatUser['nickname'] ?: '',
                'avatar' => $wechatUser['headimgurl'] ?: '',
                'is_promoter' =>$userinfo->is_promoter,
                'spread_uid' => 0,
                'spread_time' =>0,
                'last_time' => time(),
                'last_ip' => Request::instance()->ip(),
            ];
            if(isset($wechatUser['code']) && !$userinfo->is_promoter && $wechatUser['code']){
                $data['is_promoter']=1;
                $data['spread_uid']=$wechatUser['code'];
                $data['spread_time']=time();
            }
            return self::edit($data, $uid, 'uid');
        }
    }

    /**
     * 小程序用户添加
     * @param $routineUser
     * @param int $spread_uid
     * @return object
     */
    public static function setRoutineUser($routineUser,$spread_uid = 0){
        self::beginTrans();
        $res1 = true;
        if($spread_uid) $res1 = self::where('uid',$spread_uid)->setInc('spread_count',1);
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ? : 1;//获取后台分销类型
        $res2 = self::set([
            'account'=>'rt'.$routineUser['uid'].time(),
            'pwd'=>md5(123456),
            'nickname'=>$routineUser['nickname']?:'',
            'avatar'=>$routineUser['headimgurl']?:'',
            'spread_uid'=>$spread_uid,
            'is_promoter'=>$storeBrokerageStatu != 1 ? 1: 0,
            'spread_time'=>$spread_uid ? time() : 0,
            'uid'=>$routineUser['uid'],
            'add_time'=>$routineUser['add_time'],
            'add_ip'=>Request::instance()->ip(),
            'last_time'=>time(),
            'last_ip'=>Request::instance()->ip(),
            'user_type'=>$routineUser['user_type']
        ]);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res2;
    }

    /**
     * 获得当前登陆用户UID
     * @return int $uid
     */
    public static function getActiveUid()
    {
        $uid = null;
        $uid = Session::get('LoginUid');
        if($uid) return $uid;
        else return 0;
    }

    /**
     * TODO 查询当前用户信息
     * @param $uid  $uid 用户编号
     * @param string $field $field 查询的字段
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserInfo($uid,$field = '')
    {
        if(strlen(trim($field))) $userInfo = self::where('uid',$uid)->field($field)->find();
        else  $userInfo = self::where('uid',$uid)->find();
        if(!$userInfo) return false;
        return $userInfo->toArray();
    }

    /*
     * 修改个人信息
     * */
    public static function editUser($avatar,$nickname,$uid)
    {
        return self::edit(['avatar'=>$avatar,'nickname'=>$nickname],$uid,'uid');
    }

    /**
     * 判断当前用户是否推广员
     * @param int $uid
     * @return bool
     */
    public static function isUserSpread($uid = 0){
        if(!$uid) return false;
        $status = (int)SystemConfigService::get('store_brokerage_statu');
        $isPromoter = true;
        if($status == 1) $isPromoter = self::where('uid',$uid)->value('is_promoter');
        if($isPromoter) return true;
        else return false;
    }
}