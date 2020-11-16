<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/5/16
 * Time: 10:11
 */

namespace app\gzh\controller;


use app\gzh\model\activity\PrizeAct;
use app\gzh\model\activity\PrizeList;
use app\gzh\model\activity\PrizeUser;
use EasyWeChat\Js\Js;
use service\JsonService;

class ActivityApi extends AuthController
{

    /**
     * 获取某用户的中奖列表
     * @author: gyz
     * @Time: 2020/5/23 16:55
     */
    public function my_prize()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);

        $list = PrizeUser::getUserPrize($this->uid,$page,$limit);
        return JsonService::successfuljson($list);
    }

    public function my_prize_detail()
    {
        $id = input('get.id/d',0);
        if (empty($id)) return JsonService::failjson('参数错误');


        $res = PrizeUser::get($id);
        return JsonService::successfuljson($res);

    }

    /**
     * 修改中奖信息 地址等。。。
     * @author: gyz
     * @Time: 2020/5/23 16:55
     */
    public function up_prize_info()
    {
        $my_prize_id = input('post.id/d',0);
        $realname = input('post.realname','');
        $tel = input('post.tel','');
        $addr = input('post.addr','');
        if (empty($my_prize_id)) return JsonService::failjson('参数错误');

        $data = [
            'realname' => $realname,
            'tel' => $tel,
            'addr' => $addr
        ];
        $res = PrizeUser::updatePrizeInfo($this->uid,$my_prize_id,$data);
        if ($res){
            return JsonService::successfuljson('修改成功');
        }else{
            return JsonService::failjson('修改失败');
        }

    }

    /**
     * 活动列表
     * @auth:pyp
     * @date:2020/5/16 10:12
     */
    public function activity_list()
    {
        $page = input('get.page/d',1);
        $limit = input('get.limit/d',10);
        $list = PrizeAct::getList($page,$limit);
        return JsonService::successfuljson($list);
    }

    /**
     * 活动详情
     * @auth:pyp
     * @date:2020/5/16 11:11
     */
    public function activity_detail()
    {
        $act_id = input('get.act_id/d',0);
        if (!$act_id) return JsonService::failjson('数据错误');
        $detail = PrizeAct::getActivityDetail($act_id);

        $count = PrizeUser::getPrizeUserCount($this->uid,$act_id);
        $detail['prize_num'] = $detail['prize_num']-$count;

        return JsonService::successfuljson($detail);
    }


    /**
     * 抽奖
     * @auth:pyp
     * @date:2020/5/18 10:35
     */
    public function prize_draw()
    {
        $act_id = input('get.act_id/d',0);
        if (!$act_id) return JsonService::failjson('数据错误');
        $detail = PrizeAct::get($act_id);
        if (empty($detail)) return JsonService::failjson('数据错误');
        if ($detail['prize_num'] == 0) return JsonService::failjson('此活动已不能抽奖');
        $conf = PrizeList::prize_conf($act_id); //抽奖概率
        if (empty($conf)) return JsonService::failjson('此活动奖品已抽完');
//        echo $this->userInfo['integral'];
        if ($this->userInfo['integral'] < $detail['use_score']) return JsonService::failjson('积分不足');
        $count = PrizeUser::getPrizeUserCount($this->uid,$act_id);
        if ($count >= $detail['prize_num']) JsonService::failjson('最多可抽奖'.$detail['prize_num'].'次');
        $res = PrizeList::prizeDraw($act_id,$this->userInfo,$detail,$conf); //抽奖
        if (!empty($res)){
            return JsonService::successfuljson($res);
        }else{
            return JsonService::failjson('抽奖失败,请稍后再试');
        }
    }
}