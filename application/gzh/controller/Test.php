<?php

namespace app\gzh\controller;


use app\gzh\model\user\User;
use app\gzh\model\user\WechatUser;
use service\UtilService;
use think\Cookie;
use think\Request;
use think\Session;
use think\Url;

class Test extends GzhBasic
{
    public function index($ref = '',$spreadUid = 0)
    {
        if(Session::has('loginUid','gzh')) {
            $uid = Session::get('loginUid','gzh');
            echo 1;
            dump($uid);
        }
        if(Session::has('loginOpenid','gzh') && ($openid = Session::get('loginOpenid','gzh'))){
            dump($openid);
            $uid = WechatUser::openidToUid($openid);
            echo 2;
            dump($uid);

        }
    }

    public function check(Request $request)
    {
        list($account,$pwd,$ref) = UtilService::postMore(['account','pwd','ref'],$request,true);
        if(!$account || !$pwd) return $this->failed('请输入登录账号');
        if(!$pwd) return $this->failed('请输入登录密码');
        if(!User::be(['account'=>$account])) return $this->failed('登陆账号不存在!');
        $userInfo = User::where('account',$account)->find();
        $errorInfo = Session::get('login_error_info','gzh')?:['num'=>0];
        $now = time();
        if($errorInfo['num'] > 5 && $errorInfo['time'] < ($now - 900))
            return $this->failed('错误次数过多,请稍候再试!');
        if($userInfo['pwd'] != md5($pwd)){
            Session::set('login_error_info',['num'=>$errorInfo['num']+1,'time'=>$now],'gzh');
            return $this->failed('账号或密码输入错误!');
        }
        if(!$userInfo['status']) return $this->failed('账号已被锁定,无法登陆!');
        $this->_logout();
        Session::set('loginUid',$userInfo['uid'],'gzh');
        $userInfo['last_time'] = time();
        $userInfo['last_ip'] = $request->ip();
        $userInfo->save();
        Session::delete('login_error_info','gzh');
        Cookie::set('is_login',1);
        exit($this->redirect(empty($ref) ? Url::build('Index/index') : $ref));
    }

    public function logout()
    {
        $this->_logout();
        $this->successful('退出登陆成功',Url::build('Index/index'));
    }

    private function _logout()
    {
        Session::clear('gzh');
        Cookie::delete('is_login');
    }

    public function aaa()
    {
        $wechatInfo = [
            'uid'=>2
        ];
        dump(User::newGift($wechatInfo));
    }

    public function bbb($spreadUid = 1)
    {
        User::updateSp($spreadUid = 1);
    }

    public function captcha()
    {
        ob_clean();
        $captcha = new \think\captcha\Captcha([
            'codeSet'=>'0123456789',
            'length'=>100,
            'fontttf'=>'1.ttf',
            'fontSize'=>8,
            'useNoise'=>false,
            'useCurve'=>false,
            'imageW'=>100,
            'imageH'=>100,
        ]);
        return $captcha->entry();
    }

    public function gd2()
    {
        //创建画布
        $image = imagecreatetruecolor(400,400);
        //填充背景色
        $gray = imagecolorallocate($image,210,210,210);
        imagefill($image,0,0,$gray);
        //干扰线
        $color = imagecolorallocate($image,mt_rand(10,240),mt_rand(10,240),mt_rand(10,240));
//        imageline($image,mt_rand(0,10),mt_rand(10,90),mt_rand(90,100),mt_rand(10,90),$color);
        //绘制验证码文字
        $fontfile = 'vendor/topthink/think-captcha/assets/ttfs/4.ttf';
        $text = array('0','1','2','3','4','4','5','6','7','8','9');
        //绘制验证码
        for ($j=0;$j<10;$j++){
            for ($i=0;$i<10;$i++){
                $num = $text[mt_rand(0,10)];
                $text_color = imagecolorallocate($image,mt_rand(10,240),mt_rand(10,240),mt_rand(10,240));
                imagettftext($image, 30, mt_rand(-20,20), $i*40+3, $j*40+37, $text_color, $fontfile, $num);
            }
        }


        header('Content-Type:image/jpeg');
        imagejpeg($image,'public/system/images/11.jpg');
        imagedestroy($image);
    }

    public function gd22($length = 200)
    {
        //创建画布
        $width = $height = $length; //画布的大小
        $size = $length/10/10*6; //字体的大小
        $item = 10; //行,列
        $line_item = 5; //线条
        $total_num = 0; //和
        $image = imagecreatetruecolor($width,$height);

        //填充背景色
        $gray = imagecolorallocate($image,220,220,220);
        imagefill($image,0,0,$gray);

        for ($a=1;$a<=$line_item;$a++) { //干扰线
            $this->writeCurve($image, $length, $size);
        }
        /**
         * 往图片上写不同颜色的字母或数字
         */
        $this->writeNoise($image,$length=200); //往图片上写不同颜色的字母或数字

        //绘制验证码文字
        $font_file = 'vendor/topthink/think-captcha/assets/ttfs/4.ttf';
        $text = array('0','1','2','3','4','4','5','6','7','8','9');
        //绘制验证码
        for ($i=0;$i<$item;$i++){ //列
            for ($j=0;$j<$item;$j++){ //行
                $num = $text[mt_rand(0,10)]; //随机数
                $total_num += $num;
                $angle = mt_rand(-20,20); //字体的切斜角度
                $x = $j*($length/10)+($length/100);
                $y = $i*($length/10)+$size+($length/100*2);
                $text_color = imagecolorallocate($image,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200));
                imagettftext($image, $size, $angle, $x, $y, $text_color, $font_file, $num);
            }
        }

        header('Content-Type:image/jpeg');
        imagejpeg($image,'public/system/images/22.jpg');
        imagedestroy($image);
        return $total_num;
    }

    /**
     *  //干扰线
     * @param $image
     * @param int $length
     * @param $size
     * @auth:pyp
     * @date:2020/6/16 9:05
     */
    public function writeCurve($image,$length = 200,$size)
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, $length / 2); // 振幅
        $b = mt_rand(-$length / 4, $length / 4); // Y轴方向偏移量
        $f = mt_rand(-$length / 4, $length / 4); // X轴方向偏移量
        $T = mt_rand($length, $length * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand($length / 2, $length * 0.8); // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $length / 2; // y = Asin(ωx+φ) + b
                $i  = (int)($size / 5);
                while ($i > 0) {
                    $color = imagecolorallocate($image,mt_rand(10,220),mt_rand(10,220),mt_rand(10,220));
                    imagesetpixel($image, $px + $i, $py + $i, $color); // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A   = mt_rand(1, $length / 2); // 振幅
        $f   = mt_rand(-$length / 4, $length / 4); // X轴方向偏移量
        $T   = mt_rand($length, $length * 2); // 周期
        $w   = (2 * M_PI) / $T;
        $b   = $py - $A * sin($w * $px + $f) - $length / 2;
        $px1 = $px2;
        $px2 = $length;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $length / 2; // y = Asin(ωx+φ) + b
                $i  = (int)($size / 5);
                while ($i > 0) {
                    $color = imagecolorallocate($image,mt_rand(10,220),mt_rand(10,220),mt_rand(10,220));
                    imagesetpixel($image, $px + $i, $py + $i, $color);
                    $i--;
                }
            }
        }
    }

    /**
     * 往图片上写不同颜色的字母或数字
     * @param $image
     * @param int $length
     * @auth:pyp
     * @date:2020/6/16 9:05
     */
    public function writeNoise($image,$length=200)
    {
        $codeSet = '0123456789';
        for ($i = 0; $i < 10; $i++) {
            //杂点颜色
            $noiseColor = imagecolorallocate($image, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($image, 5, mt_rand(-10, $length), mt_rand(-10, $length), $codeSet[mt_rand(0, 9)], $noiseColor);
            }
        }
    }
}