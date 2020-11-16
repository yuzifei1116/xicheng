<?php
// +----------------------------------------------------------------------
// | chengzhangxiu [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.chengzhangxiu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liyongxing <1115600769@qq.com>
// +----------------------------------------------------------------------


namespace app\mini\controller;


use app\core\model\user\User;
use service\JsonService;

class UserApi extends AuthController
{
    public function user_info()
    {
        $uid = $this->uid;
        $userInfo = User::getUserInfo($uid,'uid,nickname,avatar,phone,integral,add_time,now_money');
        return JsonService::successfuljson($userInfo);
    }
}