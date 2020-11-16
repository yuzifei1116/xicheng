<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/6/8
 * Time: 16:42
 */

namespace app\gzh\controller;


use app\gzh\model\merchant\MerchantList;
use app\gzh\model\store\StoreOrder;
use app\gzh\model\store\StoreProduct;
use service\JsonService;
use think\Cache;
use think\response\Json;

class MerchantApi extends AuthController
{
    /**
     * 商铺列表
     * @auth:pyp
     * @date:2020/6/8 17:05
     */
    public function merchant_list()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = MerchantList::getMerchantList($page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 申请入驻
     * @auth:pyp
     * @date:2020/6/8 17:14
     */
    public function apply_merchant()
    {
//        $mer_name = input('post.mer_name');
//        $real_name = input('post.real_name');
//        $phone = input('post.phone');
        $data = input('post.');
        if (!$data['mer_name']) return JsonService::failjson('商铺名称不能为空');
        if (!$data['real_name']) return JsonService::failjson('请输入姓名');
        if (!$data['phone']) return JsonService::failjson('请输入手机号');
        if (!$data['verify']) return JsonService::failjson('请输入手机验证码');
        $number=Cache::get('set_rand_' . $data['phone']);
        if ($data['verify'] != $number) return JsonService::failjson('手机验证码错误');
        $data['uid'] = $this->uid;
        $data['status'] = 2;
        $data['add_time'] = time();

        $res = MerchantList::set($data);
        if ($res){
            return JsonService::successfuljson('提交成功');
        }else{
            return JsonService::failjson('提交失败');
        }

    }

    /**
     * 发送短信
     * @auth:pyp
     * @date:2020/6/8 17:42
     */
    public function send()
    {
        if (Cache::has('merchant_send_'.$this->uid)){ //发送的次数
            $count = Cache::get('merchant_send_'.$this->uid);
            if ($count > 3){
                return JsonService::failjson('24小时内只能发送三次');
            }else{
                $phone = input('post.phone');
                if (strlen($phone) != 11) return JsonService::failjson('手机格式错误');
                $number = mt_rand(100000,999999);
                Cache::set('set_rand_' . $phone,$number,300);
                $res = $this::sendmsg($number,$phone);
                if ($res->Message == 'OK' && $res->Code == 'OK') {
                    Cache::inc('merchant_send_'.$this->uid);
                    return JsonService::successfuljson('发送成功');
                }else{
                    return JsonService::failjson('获取失败，请稍后再试!');
                }
            }
        }else{
            $phone = input('post.phone');
            if (strlen($phone) != 11) return JsonService::failjson('手机格式错误');
            $number = mt_rand(100000,999999);
            Cache::set('set_rand_' . $phone,$number,300);
            $res = $this::sendmsg($number,$phone);
            if ($res->Message == 'OK' && $res->Code == 'OK') {
                Cache::set('merchant_send_'.$this->uid,1,3600*24);
                return JsonService::successfuljson('发送成功');
            }else{
                return JsonService::failjson('获取失败，请稍后再试!');
            }
        }

    }

    //发送短信
    public static function sendmsg($code,$tel = '13853133315',$tpl_code='SMS_179285020',$sign_name='高软科技业务通知')
    {
        $parm =[  // 短信模板中字段的值
            "code" => $code,
        ];
        $res = Vendor('dysms_php.api_demo.SmsDemo');
        $demo = new \SmsDemo(
            "LTAI4Fss3HG1T9pvZgRKe3XK",
            "4NUJfp6fcUDHHauf81Zs52pl6I5qhY"
        );
        $response = $demo->sendSms(
            $sign_name,
            $tpl_code,
            $tel, // 短信接收者
            $parm,
            time()
        );
        //dump($response);die;
        return $response;
    }

    /**
     * 店铺
     * @auth:pyp
     * @date:2020/6/9 9:07
     */
    public function merchant_detail()
    {
        $id = input('get.id/d',0);

        if (!$id) return JsonService::failjson('数据错误');
        $merchant = MerchantList::get($id);
        if (empty($merchant)) return JsonService::failjson('数据错误');
        $merchant = $merchant->toArray();
        if ($merchant['status'] != 1) JsonService::failjson('此店铺已关闭');
        unset($merchant['pwd']);
        $merchant['banner'] = json_decode($merchant['banner'],true);
//        $merchant['product'] = StoreProduct::getMerchantProduct($id,$page,$limit);
        return JsonService::successfuljson($merchant);
    }

    /**
     *店铺商品
     * @auth:pyp
     * @date:2020/6/9 11:23
     */
    public function merchant_product()
    {
        $id = input('get.id/d',0);
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $price_order = input('get.price_order','asc');
        $sales_order = input('get.sales_order','desc');
        $type = input('get.type/d',0); //0综合排序 1价格 2 销量
        $products = StoreProduct::getMerchantProduct($id,$price_order,$sales_order,$page,$limit,$type);
        return JsonService::successfuljson($products);
    }

    /**
     * 申请状态
     * @auth:pyp
     * @date:2020/6/9 10:17
     */
    public function apply_status()
    {
        $merchant = MerchantList::getUserMerchant($this->uid);
        $status = 0;
        if (!$merchant){
            $status = 0;//未申请
        }else{
            if ($merchant['is_del'] || !$merchant['status']){
                $status = 2;//异常
            }elseif($merchant['status']==1){
                $status = 1; //正常
            }elseif($merchant['status']==2){
                $status = 3;//审核中
            }elseif($merchant['status']==3){
                $status = 4;//未通过
            }
        }
        return JsonService::successfuljson($status);
    }
}