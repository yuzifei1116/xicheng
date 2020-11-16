<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{__PLUG_PATH}layui/css/layui.css" rel="stylesheet">
    <style>

        .layui-form-label{
            width:150px;
        }
    </style>
</head>
<body>
<br>
<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label" >优惠券名称</label>
        <div class="layui-input-inline" >
            <input type="text" name="title" value="{$data['title']}" lay-verify="required" class="layui-input">
            <input type="hidden" name="id" value="{$data['id']}">
        </div>
    </div>
    <div class="layui-form-item coupon_type">
        <label class="layui-form-label" >优惠券类型</label>
        <div class="layui-input-inline">
            <input type="radio" name="coupon_type" value="1" title="代金券" {if condition="$data['coupon_type']==1"}checked=""{/if}>
            <input type="radio" name="coupon_type" value="2" title="折扣券" {if condition="$data['coupon_type']==2"}checked=""{/if}>
        </div>
    </div>
    <div class="layui-form-item coupon_price" {if condition="$data['coupon_type']==2"} style="display: none"{/if}>
        <label class="layui-form-label" >优惠券面值</label>
        <div class="layui-input-inline"  style="width: 10%">
            <input type="text" name="coupon_price" lay-verify="number"  value="{$data['coupon_price']}" class="layui-input" placeholder="例:100">
        </div>
    </div>
    <div class="layui-form-item coupon_discount" {if condition="$data['coupon_type']==1"} style="display: none"{/if}>
        <label class="layui-form-label" >折扣率</label>
        <div class="layui-input-inline" style="width: 10%;margin-left: 1%">
            <input type="text" name="coupon_discount" vlay-verify="number" placeholder="0~1"  value="{$data['coupon_discount']}" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">填写0-1之间的两位小数,例:0.50 是5折</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" >是否限量</label>
        <div class="layui-input-inline" id="xl">
            <input type="radio" name="is_limit" value="0" title="不限量" {if condition="$data['is_limit']==0"}checked=""{/if}>
            <input type="radio" name="is_limit" value="1" title="限量" id="xl" {if condition="$data['is_limit']==1"}checked=""{/if}>
        </div>
    </div>
    <div class="layui-form-item coupon_num" {if condition="$data['is_limit']==0"} style="display: none" {/if}>
        <label class="layui-form-label" >优惠券数量</label>
        <div class="layui-input-inline"  style="width: 10%">
            <input type="text" name="coupon_num" lay-verify="number" value="{$data['coupon_num']}" class="layui-input" placeholder="0">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" >用户前台可否领取</label>
        <div class="layui-input-inline self_can_get">
            <input type="radio" name="self_can_get" value="1" title="可以" {if condition="$data['self_can_get']==1"}checked=""{/if}>
            <input type="radio" name="self_can_get" value="0" title="不可以" {if condition="$data['self_can_get']==0"}checked=""{/if}>
        </div>
        <div class="layui-form-mid layui-word-aux">是否可以让用户自己在页面领取。选不可以，则只能后台发放给用户</div>
    </div>
    <div class="layui-form-item self_max_num" {if condition="$data['self_can_get']==0"} style="display: none" {/if}>
        <label class="layui-form-label" >每人最多可领取几次</label>
        <div class="layui-input-inline"  style="width: 10%">
            <input type="text" name="self_max_num" value="{$data['self_max_num']}" lay-verify="required|number" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label" >优惠券最低消费</label>
        <div class="layui-input-inline"  >
            <input type="text" name="use_min_price" value="{$data['use_min_price']}" lay-verify="required|number" class="layui-input" placeholder="1">
        </div>
        <div class="layui-form-mid layui-word-aux">单次消费满多少钱才能使用</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" >可使用商品</label>
        <div class="layui-input-block imginput">
            {volist name="$data['coupon_products']" id="vo"}
            <img src="{$vo}?id={$key}" class="layui-upload-img" width="100px" height=100px" style="margin-right: 10px;margin-bottom: 10px">
            <input type="hidden" data-value="{$vo}?id={$key}" name="coupon_products[{$key}]" value="{$key}" >
            {/volist}
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block" >
            <div class="layui-btn layui-btn-sm imgtan">选择商品</div>
            <span style="color: red">如果不选，则全部商品都可以使用</span>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" >优惠券持续时间</label>
        <div class="layui-input-inline time_type">
            <input type="radio" name="time_type" value="1" title="天数" {if condition="$data['time_type']==1"}checked=""{/if}>
            <input type="radio" name="time_type" value="2" title="期限" {if condition="$data['time_type']==2"}checked=""{/if}>
        </div>
    </div>
    <div class="layui-form-item coupon_long_time" {if condition="$data['time_type']==2"}style="display: none"{/if}>
        <label class="layui-form-label" >有效天数</label>
        <div class="layui-input-inline"  >
            <input type="text" name="coupon_long_time" value="{$data['coupon_long_time']}" lay-verify="number" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">会员领取后多少天内有效</div>
    </div>
    <div class="layui-form-item time" {if condition="$data['time_type']==1"}style="display: none"{/if}>
        <label class="layui-form-label"  style="padding-right: 28px">有效时间段</label>
        <div class="layui-input-inline">
            <input type="text" name="time" readonly value="{$data['time']}" class="layui-input" id="test7" placeholder=" - ">
        </div>
        <div class="layui-form-mid layui-word-aux">优惠券固定的有效期</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" >排序</label>
        <div class="layui-input-inline"  style="width: 10%">
            <input type="text" name="sort" value="{$data['sort']}" lay-verify="number" class="layui-input" value="0">
        </div>
    </div>


    <div class="layui-form-item">
        <label class="layui-form-label" >状态</label>
        <div class="layui-input-block">
            <input type="radio" name="status" value="1" title="开启" {if condition="$data['status']==1"}checked=""{/if}>
            <input type="radio" name="status" value="0" title="关闭" {if condition="$data['status']==0"}checked=""{/if}>
            <input type="radio" name="status" value="0" title="过期" {if condition="$data['status']==2"}checked=""{/if}>
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
<script src="{__FRAME_PATH}js/jquery.min.js"></script>
<script src="{__PLUG_PATH}layui/layui.all.js"></script>
<script type="text/javascript">
    $('#xl').click(function () {
        var val=$('input:radio[name="is_limit"]:checked').val();
        if (val == 1){
            $('.coupon_num').css('display','block')
        }
        if (val == 0){
            $('.coupon_num').css('display','none');
        }
    });

    $('.coupon_type').click(function () {
        var val=$('input:radio[name="coupon_type"]:checked').val();
        if (val == 1){
            $('.coupon_price').css('display','block');
            $('.coupon_discount').css('display','none');
        }
        if (val == 2){
            $('.coupon_discount').css('display','block');
            $('.coupon_price').css('display','none');
        }
    });

    $('.self_can_get').click(function () {
        var val=$('input:radio[name="self_can_get"]:checked').val();
        console.log(val);
        if (val == 0){
            $('.self_max_num').css('display','none');
        }
        if (val == 1){
            $('.self_max_num').css('display','block');
        }
    });

    $('.time_type').click(function () {
        var val=$('input:radio[name="time_type"]:checked').val();
        if (val == 1){
            $('.coupon_long_time').css('display','block');
            $('.time').css('display','none');
        }
        if (val == 2){
            $('.coupon_long_time').css('display','none');
            $('.time').css('display','block');
        }
    });

    layui.use(['form','laydate'],function () {
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;
        //日期范围
        laydate.render({
            elem: '#test7'
            ,range: true
        });

        //监听提交
        form.on('submit(demo1)', function(data1){
            console.log(data1);
//            var index = layui.load();
            $.ajax({
                type:"post",
                url:"{:url('admin/ump.store_coupon/update')}",
                data:data1.field,
                dataType:"json",
                success:function(res){
//                    layui.close(index);
                    alert(res.msg);
                    if (res.code == 200){
                        parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);//提交成功父级页面刷新并关闭当前页面
                    }
                }
            });
            return false;
        });

        //-----------------------
        var imginput = 'imginput'; //input  的name，img 的 class，必须相同；
        $('.imgtan').on('click',function(e){
            console.log(e);
            layer.open({
                area: ['80%', '80%'],
                type:2,
                content:'/seller/widget.product/index/fodder/'+imginput,
            });
        });
        $('.'+imginput).on('click','img',function(){
            var con = confirm('确定删除？');
            if (!con) return false;
            var datavalue = $(this).attr('src');
            $(this).siblings('img').each(function(i,e){
                if ($(this).attr('src') == datavalue){
                    $(this).remove();
                }
            });
            $(this).remove();

            $('.'+imginput).find('input').each(function (i, e) {
                if ($(this).attr('data-value') == datavalue){
                    $(this).remove();
                }
            });
        });
        function setAttrPic(index, pic,more) {
            var img = '<img src="'+pic+'" class="layui-upload-img"  width="100px" height=100px" style="margin-right: 10px;margin-bottom: 10px">';
            var flag = pic.indexOf('=');
            var id = pic.substring(flag+1);
            var input = '<input type="hidden" data-value="'+pic+'" name="coupon_products['+id+']" value="'+id+'" >';
            if (more){
                $('.'+imginput).prepend(img);
                $('.'+imginput).append(input);
            }else{
                $('.'+imginput).html(img+input);
            }
        }
        window.changeIMG = (index,pic)=>{
            setAttrPic(index,pic,1);
        };
        //-------------------------------
    })
</script>
</html>