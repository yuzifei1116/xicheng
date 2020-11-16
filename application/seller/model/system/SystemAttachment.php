<?php

namespace app\seller\model\system;

use service\UploadService;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 文件检验model
 * Class SystemFile
 * @package app\seller\model\system
 */
class SystemAttachment extends ModelBasic
{
    use ModelTrait;
    /**添加附件记录
     */
    public static function attachmentAdd($name,$att_size,$att_type,$att_dir,$satt_dir='',$pid = 0, $mer_id)
    {
        $data['name'] = $name;
        $data['mer_id'] = $mer_id;
        $data['att_dir'] = UploadService::pathToUrl($att_dir);
        $data['satt_dir'] = UploadService::pathToUrl($satt_dir);
        $data['att_size'] = $att_size;
        $data['att_type'] = $att_type;
        $data['time'] = time();
        $data['pid'] = $pid;
        return self::create($data);
    }
    /**
     * 获取分类图
     * */
    public static function getAll($id,$mer_id){
        $model = new self;
        $where = [];
        if ($id) $where['pid'] = $id;
        $model->where($where)->where('mer_id',$mer_id)->order('att_id desc');
        return $model->page($model,$where,'',15);
    }
    /**
     * 获取单条信息
     * */
    public static function getinfo($att_id){
        $model = new self;
        $where['att_id'] = $att_id;
        return $model->where($where)->select()->toArray()[0];
    }

}