<?php

namespace app\seller\model\ump;


use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class StoreCategory
 * @package app\admin\model\store
 */
class StoreCoupon extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where,$mer_id){
        $model = new self;
        if($where['status'] != '')  $model = $model->where('status',$where['status']);
        if($where['title'] != '')  $model = $model->where('title','LIKE',"%$where[title]%");
//        if($where['is_del'] != '')  $model = $model->where('is_del',$where['is_del']);
        $model = $model->where('is_del',0)->where('mer_id',$mer_id);
        $model = $model->order('sort desc,id desc');
        return self::page($model,$where);
    }

    /**
     * @param $where
     * @return array
     */
    public static function systemPageCoupon($where){
        $model = new self;
        if($where['status'] != '')  $model = $model->where('status',$where['status']);
        if($where['title'] != '')  $model = $model->where('title','LIKE',"%$where[title]%");
//        if($where['is_del'] != '')  $model = $model->where('is_del',$where['is_del']);
        $model = $model->where('is_del',0);
        $model = $model->where('status',1);
        $model = $model->order('sort desc,id desc');
        return self::page($model,$where);
    }

    public static  function editIsDel($id){
        $data['status'] = 0;
        self::beginTrans();
        $res1 = self::edit($data,$id);
        $res2 = false !== StoreCouponUser::where('cid',$id)->setField('is_fail',1);
        $res3 = false !== StoreCouponIssue::where('cid',$id)->setField('status',-1);
        $res  = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;

    }
    /**
     * 获取所有优惠券列表 用于产品下拉菜单
     * @return array
     */
    public static function getCouponSelect()
    {
        $model = new self;
        $map = [
            'status' => 1,
            'is_del' => 0,
            're_coupon_num' => array('>',0),
        ];
        $model = $model->where($map);
        $coupon_list = $model->order('sort desc,id desc')->select();
//        echo $model->getLastSql();die;
        return $coupon_list->toArray();
    }

    public static function sendmsg($tpl_code,$parm,$tel = '13853133315',$sign_name='名宿私语')
    {
//        $parm =[  // 短信模板中字段的值
//            "nickname" => 3,
//            "coupon_name" => 1,
//            "last_day" => 1
//        ];
        $res = Vendor('dysms_php.api_demo.SmsDemo');
//        dump($res);die;
        $demo = new \SmsDemo(
            "LTAI1yk4LsJf9lC1",
            "KPlD820d8WzClx5tXhulpvEZ56RJga"
        );
        $response = $demo->sendSms(
            $sign_name,
            $tpl_code,
            $tel, // 短信接收者
            $parm,
            time()
        );

//        dump($response);
        return $response;
    }

    public static function getAll(){
        $model = new self;
        $where = [
            'status'=>1,
            'is_del' => 0,
        ];

        $model->where($where)->order('id desc');
        return $model->page($model,$where,'',28);
    }

    public static function getCouponList()
    {
        $model = new self;
        $where = [
            'status'=>1,
            'is_del' => 0,
        ];
        $list = $model->where($where)->order('id desc')->column('id,title');
        return $list;
    }

    public static function getTitle($id)
    {
        return self::where('id',$id)->value('title');
    }
}