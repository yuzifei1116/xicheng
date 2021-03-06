<?php

namespace app\seller\controller\system;

use app\seller\model\system\SystemAttachment as SystemAttachmentModel;
use app\seller\controller\AuthController;
use service\UploadService as Upload;
/**
 * 附件管理控制器
 * Class SystemAttachment
 * @package app\seller\controller\system
 *
 */
class SystemAttachment extends AuthController
{

    /**
     * 编辑器上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('upfile','editor/'.date('Ymd'));
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
//        $thumbPath = Upload::thumb($res->dir);
        $thumbPath = '';
        SystemAttachmentModel::attachmentAdd($res->fileInfo->getSaveName(),$fileInfo['size'],$fileInfo['type'],$res->dir,$thumbPath,0,$this->sellerId);
        $info = array(
            "originalName" => $fileInfo['name'],
            "name" => $res->fileInfo->getSaveName(),
//            "url" => '.'.$res->dir,
            "url" => request()->domain().$res->dir,
            "size" => $fileInfo['size'],
            "type" => $fileInfo['type'],
            "state" => "SUCCESS"
        );
        echo json_encode($info);
    }
}
