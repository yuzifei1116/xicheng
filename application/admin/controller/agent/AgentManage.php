<?php

namespace app\admin\controller\agent;

use app\admin\controller\AuthController;
use app\admin\model\order\StoreOrder;
use app\admin\model\user\User;
use app\admin\model\wechat\WechatUser as UserModel;
use app\admin\library\FormBuilder;
use app\admin\model\user\UserBill;
use service\JsonService;
use service\UtilService as Util;
use service\UtilService;

/**
 * 分销商管理控制器
 * Class AgentManage
 * @package app\admin\controller\agent
 */
class AgentManage extends AuthController
{

    /**
     * @return mixed
     */
    public function index()
    {
        $where = Util::getMore([
            ['nickname',''],
            ['data',''],
            ['tagid_list',''],
            ['groupid','-1'],
            ['sex',''],
            ['export',''],
            ['stair',''],
            ['second',''],
            ['order_stair',''],
            ['order_second',''],
            ['subscribe',''],
            ['now_money_spread',''],
            ['is_promoter',1],
        ],$this->request);
        $this->assign([
            'where'=>$where,
        ]);
        $limitTimeList = [
            'today'=>implode(' - ',[date('Y/m/d'),date('Y/m/d',strtotime('+1 day'))]),
            'week'=>implode(' - ',[
                date('Y/m/d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
            ]),
            'month'=>implode(' - ',[date('Y/m').'/01',date('Y/m').'/'.date('t')]),
            'quarter'=>implode(' - ',[
                date('Y').'/'.(ceil((date('n'))/3)*3-3+1).'/01',
                date('Y').'/'.(ceil((date('n'))/3)*3).'/'.date('t',mktime(0,0,0,(ceil((date('n'))/3)*3),1,date('Y')))
            ]),
            'year'=>implode(' - ',[
                date('Y').'/01/01',date('Y/m/d',strtotime(date('Y').'/01/01 + 1year -1 day'))
            ])
        ];
        $uidAll = UserModel::getAll($where);
        $this->assign(compact('limitTimeList','uidAll'));
        $this->assign(UserModel::agentSystemPage($where));
        return $this->fetch();
    }

    /**
     * 一级推荐人页面
     * @return mixed
     */
    public function stair($uid = ''){
        if($uid == '') return $this->failed('参数错误');
        if (!User::be(['uid'=>$uid])) return $this->failed('参数错误');
        $this->assign('uid',$uid);
        return $this->fetch();
    }

    public function get_first()
    {
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid')],
            ['page',1],
            ['limit',10],
        ]);
        return JsonService::successlayui(User::getFirst($where));
    }

    /**
     * 二级推荐人页面
     * @auth:pyp
     * @date:2020/6/15 14:58
     */
    public function second($uid)
    {
        if($uid == '') return $this->failed('参数错误');
        if (!User::be(['uid'=>$uid])) return $this->failed('参数错误');
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    public function get_second()
    {
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid')],
            ['page',1],
            ['limit',10],
        ]);
        return JsonService::successlayui(User::getSecond($where));
    }

    public function order_stair($uid)
    {
        if($uid == '') return $this->failed('参数错误');
        if (!User::be(['uid'=>$uid])) return $this->failed('参数错误');
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    public function get_order_stair()
    {
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid')],
            ['page',1],
            ['limit',10],
        ]);
        return JsonService::successlayui(StoreOrder::getOrderStair($where));
    }

    public function order_second($uid)
    {
        if($uid == '') return $this->failed('参数错误');
        if (!User::be(['uid'=>$uid])) return $this->failed('参数错误');
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    public function get_order_second()
    {
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid')],
            ['page',1],
            ['limit',10],
        ]);
        return JsonService::successlayui(StoreOrder::getOrderSecond($where));
    }

    public function now_money_spread($uid)
    {
        if($uid == '') return $this->failed('参数错误');
        if (!User::be(['uid'=>$uid])) return $this->failed('参数错误');
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    public function get_now_money_spread()
    {
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid')],
            ['page',1],
            ['limit',10],
        ]);
        return JsonService::successlayui(UserBill::getNowMoneySpread($where));
    }

    /**
     * 个人资金详情页面
     * @return mixed
     */
    public function now_money($uid = ''){
        if($uid == '') return $this->failed('参数错误');
        $list = UserBill::where('uid',$uid)->where('category','now_money')
            ->field('mark,pm,number,add_time')
            ->where('status',1)->order('add_time DESC')->select()->toArray();
        foreach ($list as &$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
}
