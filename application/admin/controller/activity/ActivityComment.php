<?php

namespace app\admin\controller\activity;

use app\admin\controller\AuthController;
use service\JsonService;
use service\UtilService;
use app\core\model\activity\ActivityComment as ActivityCommentModel;
use app\core\model\activity\Activity;

/**
 * 评论管理 控制器
 * Class StoreProductReply
 * @package app\admin\controller\store
 */
class ActivityComment extends AuthController
{
    public function index()
    {
        $this->assign('list',Activity::ActivityList());
        return $this->fetch();
    }

    /*
    *  异步获取分类列表
    *  @return json
    */
    public function comment_list(){
        $where = UtilService::getMore([
            ['activity_id',0],
            ['nickname',''],
            ['add_time',''],
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(ActivityCommentModel::commentList($where));
    }

    public function delete($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $comment = ActivityCommentModel::get($id);
        if (empty($comment)) return JsonService::fail('数据不存在');
        if (ActivityCommentModel::del($id)){
            return JsonService::success('删除成功');
        }else{
            return JsonService::fail('删除失败');
        }
    }
}
