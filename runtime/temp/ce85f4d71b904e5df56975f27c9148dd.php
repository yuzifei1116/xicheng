<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"D:\phpStudy\PHPTutorial\WWW\2009_xicheng/application/admin/view/ad/ad/edit.php";i:1600324778;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="/public/static/plug/layui/css/layui.css" rel="stylesheet">
    <script src="/public/system/frame/js/jquery.min.js"></script>

</head>
<body>
<br>
<form class="layui-form" action="">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block">
            <input type="text" lay-verify="required" name="title" value="<?php echo $data['title']; ?>" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">链接</label>
        <div class="layui-input-block">
            <input type="text" name="url" class="layui-input" value="<?php echo $data['url']; ?>">
        </div>
    </div>
<!--    <div class="layui-upload" style="margin-left: 70px;">-->
<!--        <button type="button" class="layui-btn" id="test1">上传图片</button>-->
<!--        <div class="layui-upload-list">-->
<!--            <img class="layui-upload-img img" id="demo1" width="100px" height=100px" src="<?php echo $data['img']; ?>">-->
<!--            <input type="hidden" name="img" value="<?php echo $data['img']; ?>" id="img">-->
<!--        </div>-->
<!--    </div>-->
    <div class="layui-form-item" >
        <label class="layui-form-label">图片</label>
        <div class="layui-input-block imginput">
            <img src="<?php echo $data['img']; ?>" class="layui-upload-img" id="demo1" width="100px" height=100px">
            <input type="hidden" data-value="<?php echo $data['img']; ?>" value="<?php echo $data['img']; ?>" name="img" id="img">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <div class="layui-btn layui-btn-sm imgtan">添加单图</div>
        </div>
    </div>
<!--    <div class="layui-form-item">-->
<!--        <label class="layui-form-label">选择位置</label>-->
<!--        <div class="layui-input-block">-->
<!--            <select name="weizhi" lay-verify="">-->
<!--                <option value="1">精品推荐上方</option>-->
<!--                <option value="2">热门榜单上方</option>-->
<!--                <option value="3">促销商品上方</option>-->
<!--                <option value="4">首页最下方</option>-->
<!--            </select>-->
<!--        </div>-->
<!--    </div>-->

    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input type="text" name="order" class="layui-input" value="<?php echo $data['order']; ?>">
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">显示</label>
        <div class="layui-input-block">
            <input type="radio" name="is_show" value="1" title="是" <?php if($data['is_show']==1): ?> checked <?php endif; ?>>
            <input type="radio" name="is_show" value="0" title="否" <?php if($data['is_show']==0): ?> checked <?php endif; ?>>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
<!--            <button type="reset" class="layui-btn layui-btn-primary">重置</button>-->
        </div>
    </div>
</form>
</body>
<script src="/public/static/plug/layui/layui.all.js"></script>
<script type="text/javascript">
    layui.use(['form','laydate','upload','colorpicker'], function(){
        var form = layui.form
            ,laydate = layui.laydate
            ,$ = layui.jquery
            ,colorpicker = layui.colorpicker
            ,upload = layui.upload;


        //普通图片上传
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: "<?php echo url('admin/ad.ad/upload'); ?>" //改成您自己的上传接口
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            }
            ,done: function(res){
                console.log(res);
                //如果上传失败
                if(res.code > 0){
                    return layer.msg('上传失败');
                }else{ //上传成功
                    var img = document.getElementById('img');
                    img.value = res.data.dir;
                    return layer.msg('上传成功');
                }
            }
            ,error: function(){
                //演示失败状态，并实现重传
                var demoText = $('#demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
            }
        });

        //表单赋值
        colorpicker.render({
            elem: '#test-form'
            ,color: '#1c97f5'
            ,done: function(color){
                $('#test-form-input').val(color);
            }
        });

        //监听提交
        form.on('submit(demo1)', function(data){
            console.log(data);
            $.ajax({
                type:"post",
                url:"<?php echo url('admin/ad.ad/update'); ?>",
                data:data.field,
                dataType:"json",
                success:function(res){
                    console.log(res);
                    if (res.code == 200){
                        layer.msg(res.msg);
                        parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},1000);//提交成功父级页面刷新并关闭当前页面
                    }
                    if (res.code == 400){
                        layer.msg(res.msg);
                    }
                }
            });
            return false;
        });
        var imginput = 'imginput'; //input  的name，img 的 class，必须相同；
        $('.imgtan').on('click',function(e){
            console.log(e);
            layer.open({
                area: ['80%', '80%'],
                type:2,
                content:'/admin/widget.images/index/fodder/'+imginput,
            });
        });
        $('.'+imginput).on('click','img',function(){
            var con = confirm('确定删除图片？');
            if (!con) return false;
            var datavalue = $(this).attr('src');
            $(this).remove();
            $('.'+imginput).find('input').each(function (i, e) {
                if ($(this).attr('data-value') == datavalue){
                    $(this).remove();
                }
            });
        });
        function setAttrPic(index, pic,more) {
            var img = '<img src="'+pic+'" class="layui-upload-img"  width="100px" height=100px" style="margin-right: 10px;margin-bottom: 10px">';
            var input = '<input type="hidden" data-value="'+pic+'" name="img[]" value="'+pic+'" >';
            if (more){
                $('.'+imginput).prepend(img);
                $('.'+imginput).append(input);
            }else{
                $('.'+imginput).html(img+input);
            }
        }
        window.changeIMG = (index,pic)=>{
            setAttrPic(index,pic,0);
        };

    });
</script>
</html>