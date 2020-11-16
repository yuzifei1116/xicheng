<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:106:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng/application/admin/view/setting/system_config_tab/sonconfigtab.php";i:1579139306;s:84:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\container.php";i:1579139306;s:85:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\frame_head.php";i:1579139306;s:80:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\style.php";i:1579139306;s:87:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng\application\admin\view\public\frame_footer.php";i:1579139306;}*/ ?>
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

        <div class="ibox float-e-margins">

            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary add-filed">配置分类</button>
                <button type="button" class="btn btn-w-m btn-primary add_filed_base">添加配置</button>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>配置名称</th>
                            <th>字段变量</th>
                            <th>字段类型</th>
                            <th>值</th>
                            <th>是否显示</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-center">
                                <?php echo $vo['id']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['info']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['menu_name']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['type']; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    if($vo['type'] == 'text' || $vo['type'] == 'textarea' || $vo['type'] == 'radio' || $vo['type'] == 'checkbox'){
                                              echo $vo['value'];
                                    }else if($vo['type'] == 'upload'){
                                        if($vo['upload_type'] == 3){
                                            if($vo['value']) {
                                                ?>
                                                <div class="attachment">
                                                    <div class="file-box">
                                                        <div class="file">
                                                            <a href="http://<?php echo $_SERVER['SERVER_NAME'].$vo['value'];?>" target="_blank">
                                                                <span class="corner"></span>

                                                                <div class="icon">
                                                                    <i class="fa fa-file"></i>
                                                                </div>
                                                                <div class="file-name">
                                                                    <?php
                                                                    //显示带有文件扩展名的文件名
                                                                    echo basename($vo['value']);
                                                                    ?>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <?php
                                            }
                                        }else{?>
                                            <div class="attachment">
                                                <?php
                                                if(is_array($vo['value'])){
                                                    foreach ($vo['value'] as $v){
                                                        ?>
                                                        <div class="file-box">
                                                            <div class="file">
                                                                <span class="corner"></span>
                                                                <div class="image" style="cursor: pointer">
                                                                    <img alt="image" class="img-responsive open_image" data-image="<?php echo $v; ?>" src="<?php echo $v; ?>">
                                                                </div>
                                                                <div class="file-name">
                                                                    <?php
                                                                    //显示带有文件扩展名的文件名
                                                                    echo basename($v);
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                    }
                                                }else{
                                                    ?>
                                                    <div class="file-box">
                                                        <div class="file">
                                                            <span class="corner"></span>
                                                            <div class="image" style="cursor: pointer">
                                                                <img alt="image" class="img-responsive open_image" data-image="<?php echo $vo['value']; ?>" src="<?php echo $vo['value']; ?>">
                                                            </div>
                                                            <div class="file-name">
                                                                <?php
                                                                //显示带有文件扩展名的文件名
                                                                echo basename($vo['value']);
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="clearfix"></div>
                                            </div>
                                        <?php
                                        }
                                    }
                                ?>

                            </td>

                            <td class="text-center">

                                <?php if($vo['status'] == 1): ?>
                                <i class="fa fa-check text-navy"></i>
                                <?php elseif($vo['status'] == 2): ?>
                                <i class="fa fa-close text-danger"></i>
                                <?php endif; ?>

                            </td>

                            <td class="text-center">

                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','<?php echo Url('setting.system_config/edit_cinfig',array('id'=>$vo['id'])); ?>')"><i class="fa fa-paste"></i> 编辑</button>

                                <button class="btn btn-warning btn-xs del_config_tab" data-id="<?php echo $vo['id']; ?>" type="button" data-url="<?php echo Url('setting.system_config/delete_cinfig',array('id'=>$vo['id'])); ?>" ><i class="fa fa-warning"></i> 删除

                                </button>

                            </td>

                        </tr>

                        <?php endforeach; endif; else: echo "" ;endif; ?>

                        </tbody>


                    </table>

                </div>

            </div>

        </div>

    </div>

</div>



<script>

    $('.add-filed').on('click',function (e) {
        window.location.replace("<?php echo Url('index'); ?>");
    })
    $('.open_image').on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
    $('.del_config_tab').on('click',function(){

        var _this = $(this),url =_this.data('url');

        $eb.$swal('delete',function(){

            $eb.axios.get(url).then(function(res){

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
    $('.add_filed_base').on('click',function (e) {
        $eb.swal({
            title: '请选择数据类型',
            input: 'radio',
            inputOptions: ['文本框','多行文本框','单选框','文件上传','多选框'],
            inputValidator: function(result) {
                return new Promise(function(resolve, reject) {
                    if (result) {
                        resolve();
                    } else {
                        reject('请选择数据类型');
                    }
                });
            }
        }).then(function(result) {
            if (result) {
                $eb.createModalFrame(this.innerText,"<?php echo Url('setting.systemConfig/create',array('tab_id'=>\think\Request::instance()->param('tab_id'))); ?>?type="+result);
            }
        })
    })
</script>


</div>
</body>
</html>
