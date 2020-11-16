<?php
namespace app\gzh\controller;

use app\core\logic\Pay;

/**
 * 支付回调
 * Class Notify
 * @package app\gzh\controller
 */
//待完善
class Notify
{
    /**
     *   支付  异步回调
     */
    public function notify()
    {
        Pay::notify();
    }
}


