<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:99:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng/application/admin/view/setting/system_group_data/index.php";i:1579139306;s:84:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\container.php";i:1579139306;s:85:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\frame_head.php";i:1579139306;s:80:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\style.php";i:1579139306;s:85:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\inner_page.php";i:1579139306;s:87:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\frame_footer.php";i:1579139306;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(empty($is_layui) || (($is_layui instanceof \think\Collection || $is_layui instanceof \think\Paginator ) && $is_layui->isEmpty())): ?>
    <link href="/public/system/frame/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <?php endif; ?>
    <link href="/public/static/plug/layui/css/layui.css" rel="stylesheet">
    <link href="/public/system/css/layui-admin.css" rel="stylesheet"></link>
    <link href="/public/system/frame/css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <link href="/public/system/frame/css/animate.min.css" rel="stylesheet">
    <link href="/public/system/frame/css/style.min.css?v=3.0.0" rel="stylesheet">
    <script src="/public/system/frame/js/jquery.min.js"></script>
    <script src="/public/system/frame/js/bootstrap.min.js"></script>
    <script src="/public/static/plug/layui/layui.all.js"></script>
    <script>
        $eb = parent._mpApi;
        window.controlle="<?php echo strtolower(trim(preg_replace("/[A-Z]/", "_\\0", think\Request::instance()->controller()), "_"));?>";
        window.module="<?php echo think\Request::instance()->module();?>";
    </script>



    <title></title>
    
    <!--<script type="text/javascript" src="/static/plug/basket.js"></script>-->
<script type="text/javascript" src="/public/static/plug/requirejs/require.js"></script>
<?php /*  <script type="text/javascript" src="/static/plug/requirejs/require-basket-load.js"></script>  */ ?>
<script>
    var hostname = location.hostname;
    if(location.port) hostname += ':' + location.port;
    requirejs.config({
        map: {
            '*': {
                'css': '/public/static/plug/requirejs/require-css.js'
            }
        },
        shim:{
            'iview':{
                deps:['css!iviewcss']
            },
            'layer':{
                deps:['css!layercss']
            }
        },
        baseUrl:'//'+hostname+'/public/',
        paths: {
            'static':'static',
            'system':'system',
            'vue':'static/plug/vue/dist/vue.min',
            'axios':'static/plug/axios.min',
            'iview':'static/plug/iview/dist/iview.min',
            'iviewcss':'static/plug/iview/dist/styles/iview',
            'lodash':'static/plug/lodash',
            'layer':'static/plug/layer/layer',
            'layercss':'static/plug/layer/theme/default/layer',
            'jquery':'static/plug/jquery/jquery.min',
            'moment':'static/plug/moment',
            'sweetalert':'static/plug/sweetalert2/sweetalert2.all.min'

        },
        basket: {
            excludes:['system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
//            excludes:['system/util/mpFormBuilder','system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
        }
    });
</script>
<script type="text/javascript" src="/public/system/util/mpFrame.js"></script>
    
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary" onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('create',array('gid'=>$gid)); ?>')">添加数据</button>
                <div class="ibox-tools">

                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-8 m-b-xs">
                        <?php /*  <form action="" class="form-inline">
                              <i class="fa fa-search" style="margin-right: 10px;"></i>
                              <select name="is_show" aria-controls="editable" class="form-control input-sm">
                                  <option value="">是否显示</option>
                                  <option value="1" <?php if($params['is_show'] == '1'): ?>selected="selected"<?php endif; ?>>显示</option>
                                  <option value="0" <?php if($params['is_show'] == '0'): ?>selected="selected"<?php endif; ?>>不显示</option>
                              </select>
                              <select name="access" aria-controls="editable" class="form-control input-sm">
                                  <option value="">子管理员是否可用</option>
                                  <option value="1" <?php if($params['access'] == '1'): ?>selected="selected"<?php endif; ?>>可用</option>
                                  <option value="0" <?php if($params['access'] == '0'): ?>selected="selected"<?php endif; ?>>不可用</option>
                              </select>
                          <div class="input-group">
                              <input type="text" name="keyword" value="<?php echo $params['keyword']; ?>" placeholder="请输入关键词" class="input-sm form-control"> <span class="input-group-btn">
                                      <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                          </div>
                          </form>  */ ?>
                        <form action="" class="form-inline">
                            <i class="fa fa-search" style="margin-right: 10px;"></i>
                            <select name="status" aria-controls="editable" class="form-control input-sm">
                                <option value="">是否可用</option>
                                <option value="1" <?php if($where['status'] == '1'): ?>selected="selected"<?php endif; ?>>显示</option>
                                <option value="2" <?php if($where['status'] == '2'): ?>selected="selected"<?php endif; ?>>不显示</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">编号</th>
                            <?php if(is_array($fields) || $fields instanceof \think\Collection || $fields instanceof \think\Paginator): $i = 0; $__LIST__ = $fields;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <th class="text-center"><?php echo $vo['name']; ?></th>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <th class="text-center">是否可用</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-center">
                                <?php echo $vo['id']; ?>
                            </td>
                            <?php if(is_array($fields) || $fields instanceof \think\Collection || $fields instanceof \think\Paginator): $i = 0; $__LIST__ = $fields;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                <td class="text-center">
                                    <?php if(!(empty($vo['value'][$item['title']]['value']) || (($vo['value'][$item['title']]['value'] instanceof \think\Collection || $vo['value'][$item['title']]['value'] instanceof \think\Paginator ) && $vo['value'][$item['title']]['value']->isEmpty()))): ?>
                                    <?php echo $vo['value'][$item['title']]['value']; endif; ?>
                                </td>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <td class="text-center">
                                <?php if($vo['status'] == 1): ?>
                                <i class="fa fa-check text-navy"></i>
                                <?php elseif($vo['status'] == 2): ?>
                                <i class="fa fa-close text-danger"></i>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','<?php echo Url('edit',array('gid'=>$gid,'id'=>$vo['id'])); ?>')"><i class="fa fa-paste"></i> 编辑</button>
                                <button class="btn btn-warning btn-xs" data-url="<?php echo Url('delete',array('id'=>$vo['id'])); ?>" type="button"><i class="fa fa-warning"></i> 删除</button>
                            </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                </div>
                <link href="/public/system/frame/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
<div class="row">
    <div class="col-sm-6">
        <div class="dataTables_info" id="DataTables_Table_0_info" role="alert" aria-live="polite" aria-relevant="all">共 <?php echo $total; ?> 项</div>
    </div>
    <div class="col-sm-6">
        <div class="dataTables_paginate paging_simple_numbers" id="editable_paginate">
            <?php echo $page; ?>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>



<script>
    $('.btn-warning').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                console.log(res);
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
    $(".image").on('click',function (e) {
        var images = $(this).data('image');
        $eb.openImage(images);
    })
</script>


</div>
</body>
</html>
