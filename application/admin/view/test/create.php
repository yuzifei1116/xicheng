<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{__PLUG_PATH}layui/css/layui.css" rel="stylesheet">
    <script src="{__FRAME_PATH}js/jquery.min.js"></script>

    <script src="{__PLUG_PATH}vue/dist/vue.min.js"></script>

</head>
<body>
<br>
<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">链接</label>
        <div class="layui-input-block">
            <input type="text" name="url" class="layui-input" value="#">
        </div>
    </div>
    <div class="layui-upload" style="margin-left: 70px;">
<!--        <button type="button" class="layui-btn" id="test1">上传图片</button>-->
        <div class="layui-upload-list">
            <img class="layui-upload-img imgtan" id="demo16666" width="100px" height=100px">
            <p id="demoText"></p>
            <input type="hidden" name="imgtan" value="" id="img">
        </div>
    </div>
    <div class="layui-form-item" style="margin-left: 70px;">
        <div class="layui-input-inline" style="width: 120px;">
            <input type="text" name="bg_color" value="" placeholder="请选择颜色" class="layui-input" id="test-form-input">
        </div>
        <div class="layui-inline" style="left: -11px;">
            <div id="test-form"></div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input type="text" name="order" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">显示</label>
        <div class="layui-input-block">
            <input type="radio" name="is_show" value="1" title="是" checked="">
            <input type="radio" name="is_show" value="0" title="否">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
</body>
<script src="{__PLUG_PATH}layui/layui.all.js"></script>
<script type="text/javascript">
    layui.use(['form','laydate','upload','colorpicker'], function(){
        var form = layui.form
            ,laydate = layui.laydate
            ,$ = layui.jquery
            ,colorpicker = layui.colorpicker
            ,layer = layui.layer
            ,upload = layui.upload;

        //layui  弹层选择图片++++++++++++++++++++++++++++++++++++++++++
        var imgtan = 'imgtan'; //input  的name，img 的 class，必须相同；
        $('.'+imgtan).on('click',function(e){
            console.log(e);
            layer.open({
                area: ['80%', '80%'],
                type:2,
                content:'/admin/widget.images/index/fodder/'+imgtan,
            });
        })
        function setAttrPic(index, pic) {
            $("input[name='"+index+"']").val(pic);
            $("."+index).attr('src',pic);
        }
        window.changeIMG = (index,pic)=>{
            setAttrPic(index,pic);
        };
        //layui  弹层选择图片+++++++++++++++++++++++++++++++++++++++++++


    });
</script>
</html>