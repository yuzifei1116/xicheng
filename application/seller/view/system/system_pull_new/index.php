<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>新人礼配置</title>
    <link href="{__PLUG_PATH}layui/css/layui.css" rel="stylesheet">

</head>
<body>
<div style="margin-top: 40px;margin-left:20px;width: 60%">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="开启" {if condition="$data['status']==1"} checked {/if}>
                <input type="radio" name="status" value="0" title="关闭" {if condition="$data['status']==0"} checked {/if}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">赠送余额(元)</label>
            <div class="layui-input-block">
                <input type="text" name="give_now_money" value="{$data['give_now_money']}" lay-verify="number" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">赠送积分</label>
            <div class="layui-input-block">
                <input type="text" name="give_integral" value="{$data['give_integral']}" lay-verify="number" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">赠送优惠券</label>
            <div class="layui-input-inline">
                <select name="" lay-search="" id="select_coupon"  lay-filter="select_filter">
                    {volist name="coupon" id="vo"}
                    <option value="{$key}">{$vo}</option>
                    {/volist}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <ul id="coupon_li" class="layui-row">
                    {volist name="coupon_list" id="vo"}
                    <li>
                        <div class="layui-col-sm6"><h2>{$vo.title}</h2></div>
                        <div class="layui-col-sm4"><input type="number" name="num[]" value="{$vo.num}" class="layui-input" style="width:40%;"></div>
                        <div class="layui-col-sm2"><button class="layui-btn layui-btn-primary layui-btn-md del">删除</button></div><hr>
                        <input type="hidden" name="cid[]" value="{$vo.cid}">
                    </li>
                    {/volist}
                </ul>
            </div>
        </div>
        <button type="button" class="layui-btn layui-btn-normal" style="width: 100%;" lay-submit lay-filter="demo1">提交</button>
    </form>
</div>
</body>
<script src="{__PLUG_PATH}layui/layui.all.js"></script>
<script src="{__FRAME_PATH}js/jquery.min.js"></script>
<script>
    layui.use(['form','laydate','upload'], function(){
        var form = layui.form
            ,laydate = layui.laydate
            ,$ = layui.jquery
            ,upload = layui.upload;

        var cids = <?php echo json_encode($cids); ?>;
        console.log(cids);
        form.on('select(select_filter)', function(data){
            var cid = data.value; //得到被选中的值
            if (cids.indexOf(cid) == -1){
                cids.push(cid);
                var title = $('#select_coupon').find("option:selected").text();
                var num = '<input type="number" name="num[]" value="1" class="layui-input" style="width:40%;">';
                var btn = '<button class="layui-btn layui-btn-primary layui-btn-md del">删除</button>';
                var item = '<li>'+
                    '<div class="layui-col-sm6">'+'<h2>'+title+'</h2>'+'</div>' +
                    '<div class="layui-col-sm4">'+num+'</div>'+
                    '<div class="layui-col-sm2">'+btn+'</div>'+'<hr>' +
                    '<input type="hidden" name="cid[]" value="'+cid+'">' +
                    '</li>';
                $('#coupon_li').append(item);
                console.log(cids);
            }else{
                alert('此优惠券已添加');
            }
        });

        $('#coupon_li').on('click','.del',function (d) {
            var a = $(this).parent().siblings('input').val();
            cids.splice(cids.indexOf(a),1);
            console.log(cids);
            $(this).parent().parent('li').remove();
        });

        form.on('submit(demo1)',function (data) {
            var index=layer.load();
            $.ajax({
                url:'save',
                data:data.field,
                type:'post',
                dataType:'json',
                success:function (d) {
                    layer.close(index);
                    alert(d.msg);
                }
            })
            return false;
        })
    })
</script>
</html>
