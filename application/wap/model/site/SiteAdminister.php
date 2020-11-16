<?php

namespace app\wap\model\site;
use basic\ModelBasic;
use think\Cache;

class SiteAdminister extends ModelBasic
{
    /**
     * 查询站点
     * pyp
     */
    public static function getSitelist()
    {
        $list = self::cache('getSitelist',600)->where('status',1)->order('order desc,id asc')->select();
        return empty($list) ? [] : $list->toArray();
    }

    /**
     * 查询地图站点
     * pyp
     */
    public static function getMapSitelist()
    {
        $list = self::cache('getSitelist',600)->where('status',1)->order('order desc,id asc')->select();
        return empty($list) ? [] : $list->toArray();
    }

    public static function getDetail($id)
    {
        $detail=self::where('id',$id)->find();
        return $detail;
    }

    public static function getUserValidAddressList($field = '*')
    {
        return self::where('status',1)->order('add_time DESC')->field($field)->cache(600)->select()->toArray()?:[];
    }

    public static function getUserDefaultAddress($field = '*')
    {
        return self::where('status',1)->where('is_default',1)->field($field)->find();
    }

    public static function be($map, $field = '')
    {
        $model = (new self);
        if(!is_array($map) && empty($field)) $field = $model->getPk();
        $map = !is_array($map) ? [$field=>$map] : $map;
        return 0 < $model->where($map)->count();
    }

    public static function getSite($site_id)
    {
        $site=self::where('id',$site_id)->field('id,name')->find();
        if (empty($site)) return [];
        $site = $site->toArray();
        return $site;
    }

    public static function updateUser($data)
    {
        $res=self::isUpdate(true)->save($data);
    }

    /**
     * 修改一条数据
     * @param $data
     * @param $id
     * @param $field
     * @return bool $type 返回成功失败
     */
    public static function edit($data,$uid,$field = null)
    {
        $model = new self;
        if(!$field) $field = $model->getPk();
//        return false !== $model->update($data,[$field=>$id]);
        return 0 < $model->update($data,[$field=>$uid])->result;
    }
}