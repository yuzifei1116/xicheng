<?php

namespace app\seller\model\system;


use traits\ModelTrait;
use basic\ModelBasic;
use think\Request;
use app\seller\model\system\SystemMenus;
use app\seller\model\system\SystemAdmin;

/**
 * 管理员操作记录
 * Class SystemLog
 * @package app\seller\model\system
 */
class SystemLog extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }


    /**
     * 管理员访问记录
     * @param Request $request
     */
    public static function sellerVisit($sellerId,$sellerName,$type)
    {
        $request = Request::instance();
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $route = $request->route();
        $data = [
            'method'=>$request->method(),
            'seller_id'=>$sellerId,
            'seller_name'=>$sellerName,
            'path'=>SystemMenus::getAuthName($action,$controller,$module,$route),
            'page'=>SystemMenus::getVisitName($action,$controller,$module,$route)?:'未知',
            'ip'=>$request->ip(),
            'type'=>$type
        ];
        return self::set($data);
    }

    /**
     * 手动添加管理员当前页面访问记录
     * @param array $sellerInfo
     * @param string $page 页面名称
     * @return object
     */
    public static function setCurrentVisit($sellerInfo, $page)
    {
        $request = Request::instance();
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $route = $request->route();
        $data = [
            'method'=>$request->method(),
            'seller_id'=>$sellerInfo['id'],
            'path'=>SystemMenus::getAuthName($action,$controller,$module,$route),
            'page'=>$page,
            'ip'=>$request->ip()
        ];
        return self::set($data);
    }

    /**
     * 获取管理员访问记录
     * */
    public static function systemPage($where = array()){
        $model = new self;
        $model = $model->alias('l');
        if($where['pages'] !== '') $model = $model->where('l.page','LIKE',"%$where[pages]%");
        if($where['path'] !== '') $model = $model->where('l.path','LIKE',"%$where[path]%");
        if($where['ip'] !== '') $model = $model->where('l.ip','LIKE',"%$where[ip]%");
        if($where['seller_id'] != '')
            $sellerIds = $where['seller_id'];
        else
            $sellerIds = SystemAdmin::where('level','>=',$where['level'])->column('id');
        $model = $model->where('l.seller_id','IN',$sellerIds);
        if($where['data'] !== ''){
            list($startTime,$endTime) = explode(' - ',$where['data']);
            $model = $model->where('l.add_time','>',strtotime($startTime));
            $model = $model->where('l.add_time','<',strtotime($endTime));
        }
        $model->where('l.type','system');
        $model = $model->order('l.id desc');
        return self::page($model,$where);
    }
    /**
     * 删除超过90天的日志
     */
    public static function deleteLog(){
        $model = new self;
        $model->where('add_time','<',time()-7776000);
        $model->delete();
    }
}