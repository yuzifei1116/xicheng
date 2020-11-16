<?php

namespace app\admin\controller\menu;

use app\admin\controller\AuthController;
use service\JsonService;
use service\UtilService;
use app\core\model\menu\MenuComment as MenuCommentModel;

/**
 * 评论管理 控制器
 * Class StoreProductReply
 * @package app\admin\controller\store
 */
class MenuComment extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    /*
    *  异步获取分类列表
    *  @return json
    */
    public function comment_list(){
        $where = UtilService::getMore([
            ['nickname',''],
            ['add_time',''],
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(MenuCommentModel::commentList($where));
    }

    public function delete($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $comment = MenuCommentModel::get($id);
        if (empty($comment)) return JsonService::fail('数据不存在');
        if (MenuCommentModel::del($id)){
            return JsonService::success('删除成功');
        }else{
            return JsonService::fail('删除失败');
        }
    }
}
