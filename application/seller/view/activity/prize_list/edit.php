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

</head>
<body>
<br>
<form class="layui-form" action="" style="width: 80%;margin-left: 5%">
    <div class="layui-form-item">
        <label class="layui-form-label">奖品名称</label>
        <div class="layui-input-block">
            <input type="text" lay-verify="required" name="prize_name" value="{$data['prize_name']}" placeholder="请输入奖品名称" class="layui-input">
        </div>
    </div>
    <div class="layui-upload">
        <label class="layui-form-label">图片</label>
        <div class="layui-upload-list">
            <img src="{$data['image']}" class="layui-upload-img image" id="demo1" width="100px" height=100px">
            <p id="demoText"></p>
            <input type="hidden" name="image" value="{$data['image']}" id="image">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" id="label">商品数量</label>
        <div class="layui-input-block" id="xl">
            <input type="radio" name="is_limit" value="0" title="不限量" {if condition="$data['is_limit']==0"}checked=""{/if}>
            <input type="radio" name="is_limit" value="1" title="限量" id="xl" {if condition="$data['is_limit']==1"}checked=""{/if}>
        </div>
    </div>
    <div class="layui-form-item prize_num" {if condition="$data['is_limit']==0"} style="display: none" {/if}>
        <label class="layui-form-label" id="label">数量</label>
        <div class="layui-input-block" id="block" style="width: 10%">
            <input type="text" name="number" lay-verify="number" value="0" class="layui-input" placeholder="0">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">概率</label>
        <div class="layui-input-block">
            <input type="text" name="probability" lay-verify="number" class="layui-input" value="{$data['probability']}" placeholder="千分制,请填写0-1000的整数">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input type="text" name="order" class="layui-input" value="{$data['order']}">
            <input type="hidden" name="id" class="layui-input" value="{$id}">
        </div>
    </div>
    <div class="layui-form-item" style="margin-top: 10px">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>
</body>
<script src="{__PLUG_PATH}layui/layui.all.js"></script>
<script type="text/javascript">

    $('#xl').click(function () {
        var val=$('input:radio[name="is_limit"]:checked').val();
        console.log(val);
        if (val == 1){
            $('.prize_num').css('display','block')
        }
        if (val == 0){
            $('.prize_num').css('display','none');
        }
    });

    layui.use(['form','laydate','upload','colorpicker'], function(){
        var form = layui.form
            ,laydate = layui.laydate
            ,$ = layui.jquery
            ,colorpicker = layui.colorpicker
            ,upload = layui.upload;

        //日期时间范围
        laydate.render({
            elem: '#test10'
            ,type: 'datetime'
            ,range: true
        });

        //表单赋值
        colorpicker.render({
            elem: '#test-form'
            ,color: '#c25347'
            ,done: function(color){
                $('#test-form-input').val(color);
            }
        });

        //监听提交
        form.on('submit(demo1)', function(data){
            console.log(data);
            $.ajax({
                type:"post",
                url:"update",
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
        var imgtan = 'image'; //input  的name，img 的 class，必须相同；
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

    });
</script>
</html>