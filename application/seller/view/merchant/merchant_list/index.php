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
    <script src="{__PLUG_PATH}city.js"></script>

</head>
<body>
<br>
<br>
<div style="margin-left: 80px;margin-right: 100px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">店铺名</label>
            <div class="layui-input-block">
                <input type="text" name="mer_name" value="{$merchant.mer_name}" lay-verify="required" placeholder="请输入店铺名称" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">账号</label>
            <div class="layui-input-block">
                <input type="text" name="account" value="{$merchant.account}" lay-verify="required" placeholder="请输入店铺账号" class="layui-input" disabled>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">原密码</label>
            <div class="layui-input-block">
                <input type="password" name="old_pwd" lay-verify="pass" placeholder="请输入店铺原密码,6~12位,不填则不修改密码" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">新密码</label>
            <div class="layui-input-block">
                <input type="password" name="pwd" lay-verify="pass" placeholder="请输入店铺新密码,6~12位,不填则不修改密码" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">重复密码</label>
            <div class="layui-input-block">
                <input type="password" name="re_pwd" lay-verify="pass" class="layui-input">
            </div>
        </div>
        <div class="layui-upload" style="margin-left: 1px;">
            <label class="layui-form-label">logo</label>
            <div class="layui-upload-list">
                <img class="layui-upload-img image" id="demo1" width="100px" height=100px" src="{$merchant.image}">
                <p id="demoText"></p>
                <input type="hidden" name="image" value="{$merchant.image}" value="" id="image">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" >轮播图</label>
            <div class="layui-input-block imginput">
                {volist name="$merchant['banner']" id="vo"}
                <img src="{$vo}?id={$key}" class="layui-upload-img" width="100px" height=100px" style="margin-right: 10px;margin-bottom: 10px">
                <input type="hidden" data-value="{$vo}?id={$key}" name="banner[{$key}]" value="{$key}" >
                {/volist}
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block" >
                <div class="layui-btn layui-btn-sm imgbtn">选择图片</div>
            </div>
        </div>
<!--        <div class="layui-form-item">-->
<!--            <label class="layui-form-label">轮播图</label>-->
<!--            <div class="layui-upload">-->
<!--                <button type="button" class="layui-btn" id="test2">多图片上传</button>-->
<!--                <button type="button" class="layui-btn" id="reset1" onclick="again()" style="background-color: red">清空</button>-->
<!--                <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;margin-left: 100px">-->
<!--                    预览图：<span style="color: #9D1E15">最多可上传3张图片</span>-->
<!--                    <div class="layui-upload-list" id="demo2" style="height: 75px">-->
<!---->
<!--                        {volist name="$merchant['banner']" id="vo"}-->
<!--                        <img src="{$vo}" height="75px">-->
<!--                        {/volist}-->
<!---->
<!--                    </div>-->
<!--                    <div id="img">-->
<!--                        {volist name="$merchant['banner']" id="vo"}-->
<!--                        <input type="hidden" value="{$vo}" name="banner[]">-->
<!--                        {/volist}-->
<!--                    </div>-->
<!--                </blockquote>-->
<!--            </div>-->
<!--        </div>-->
        <div class="layui-form-item">
            <label class="layui-form-label">店主姓名</label>
            <div class="layui-input-block">
                <input type="text" name="real_name" value="{$merchant.real_name}" lay-verify="required" placeholder="请输入店主姓名" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">店铺简介</label>
            <div class="layui-input-block">
                <input type="text" name="info" value="{$merchant.info}" lay-verify="required" placeholder="请输入店铺账号" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">店主电话</label>
            <div class="layui-input-block">
                <input type="text" name="phone" value="{$merchant.phone}" lay-verify="required" placeholder="请输入店主电话" class="layui-input">
            </div>
        </div>
<!--        <div class="layui-form-item">-->
<!--            <div class="layui-inline" id="province-div">-->
<!--                <label class="layui-form-label">省份：</label>-->
<!--                <div class="layui-input-inline">-->
<!--                    <select name="province" lay-filter='province' id="province">-->
<!--                        <option value="" id="province-top">请选择省</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="layui-inline" id="city-div">-->
<!--                <label class="layui-form-label">城市：</label>-->
<!--                <div class="layui-input-inline">-->
<!--                    <select name="city" lay-filter='city' id="city">-->
<!--                        <option value="" id="city-top">请选择市</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="layui-inline" id="city-div">-->
<!--                <label class="layui-form-label">县(区）：</label>-->
<!--                <div class="layui-input-inline">-->
<!--                    <select name="county" lay-filter='county' id="county">-->
<!--                        <option value="" id="county-top">请选择县(区)</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="layui-inline" id="city-div">-->
<!--                <span style="color: red;">不选则代表不修改</span>-->
<!--            </div>-->
<!--        </div>-->
        <div class="layui-form-item">
            <label class="layui-form-label">店铺地址</label>
            <div class="layui-input-block">
                <input type="text" name="address" value="{$merchant.address}" lay-verify="required" placeholder="请选择店铺地址" class="layui-input address">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">具体地址</label>
            <div class="layui-input-block">
                <input type="text" name="specific_address" value="{$merchant.specific_address}" lay-verify="required" placeholder="如:xxx小区" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">经度</label>
            <div class="layui-input-block">
                <input type="text" name="longitude" value="{$merchant.longitude}" lay-verify="number" placeholder="请输入经度,最多保存6为小数" class="layui-input jingdu">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">纬度</label>
            <div class="layui-input-block">
                <input type="text" name="latitude" value="{$merchant.latitude}" lay-verify="number" placeholder="请输入纬度,最多保存6为小数" class="layui-input weidu">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">客服电话</label>
            <div class="layui-input-block">
                <input type="text" name="customer_service" value="{$merchant.customer_service}" lay-verify="required" placeholder="请输入客服电话" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">是否推荐</label>
            <div class="layui-input-block">
                <input type="radio" name="recommend" value="1" title="是" {if condition="$merchant.recommend==1"} checked="" {/if}>
                <input type="radio" name="recommend" value="0" title="否" {if condition="$merchant.recommend==0"} checked="" {/if}>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="正常" {if condition="$merchant.status==1"} checked="" {/if}>
                <input type="radio" name="status" value="0" title="禁止" {if condition="$merchant.status==0"} checked="" {/if}>
                <input type="radio" name="status" value="2" title="审核中" disabled {if condition="$merchant.status==2"} checked="" {/if}>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="text" name="sort" value="{$merchant.sort}" class="layui-input">
                <input type="hidden" name="id" value="{$merchant.id}" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">提交</button>
            </div>
        </div>
    </form>
</div>
</body>
<script src="{__PLUG_PATH}layui/layui.all.js"></script>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    var html = '';
    console.log(city);
    $.each(city,function (index,item) {
        html += '<option value="'+item.label+'">'+item.label+'</option>';
    });
    $('#province-top').after(html);
    $('#province').val('');
    layList.form.render('select');
    var selectedProvince = '';
    layList.select('province',function (odj,value,name) {
        selectedProvince = odj.value;
        var html = '';
        $.each(city,function (index,item) {
            if(item.label == odj.value){
                $.each(item.children,function (index2,item2) {
                    html += '<option value="'+item2.label+'">'+item2.label+'</option>';
                })
                $('#city').val('');
                $('#city-top').siblings().remove();
                $('#city-top').after(html);

                $('#county').val('');
                $('#county-top').siblings().remove();
                layList.form.render('select');
            }
        })
    });
    layList.select('city',function (odj,value,name) {
        console.log(odj);
        var html = '';
        $.each(city,function (index,item) {
            if(item.label == selectedProvince){
                console.log(item);
                $.each(item.children,function (index2,item2) {
                    if(item2.label == odj.value){
                        $.each(item2.children,function (index3,item3) {
                            html += '<option value="'+item3.label+'">'+item3.label+'</option>';
                        })
                        $('#county').val('');
                        $('#county-top').siblings().remove();
                        $('#county-top').after(html);
                        layList.form.render('select');
                    }
                });
            }
        })
    });



    var imgtan = 'image'; //input  的name，image 的 class，必须相同；
    $('.'+imgtan).on('click',function(e){
        console.log(e);
        layer.open({
            area: ['80%', '80%'],
            type:2,
            content:'/seller/widget.images/index/fodder/'+imgtan,
        });
    });
    function setAttrPic(index, pic) {
        $("input[name='"+index+"']").val(pic);
        $("."+index).attr('src',pic);
    }
//    window.changeIMG = (index,pic)=>{
//        setAttrPic(index,pic);
//    };


    //-----------------------
    var imginput = 'imginput'; //input  的name，img 的 class，必须相同；
    $('.imgbtn').on('click',function(e){
        console.log(e);
        layer.open({
            area: ['80%', '80%'],
            type:2,
            content:'/seller/widget.images/index/fodder/'+imginput,
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

    function setAttrPic1(index, pic,more) {
        var img = '<img src="'+pic+'" class="layui-upload-img"  width="100px" height=100px" style="margin-right: 10px;margin-bottom: 10px">';
        var flag = pic.indexOf('=');
        var id = pic.substring(flag+1);
        var input = '<input type="hidden" data-value="'+pic+'" name="banner['+id+']" value="'+id+'" >';
        if (more){
            $('.'+imginput).prepend(img);
            $('.'+imginput).append(input);
        }else{
            $('.'+imginput).html(img+input);
        }
    }
    window.changeIMG = (index,pic)=>{
        if(index==='imginput'){
            setAttrPic1(index,pic,1);
        }
        if(index==='image'){
            setAttrPic(index,pic);
        }
    };

    layui.use(['form','laydate','upload','layer'],function () {
        var form = layui.form
            ,laydate = layui.laydate
            ,layer = layui.layer
            ,$ = layui.jquery
            ,upload = layui.upload;

        //普通图片上传
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: "{:url('admin/merchant.merchant_list/upload')}" //改成您自己的上传接口
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
                    var img = document.getElementById('image');
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
        //多图片上传
        upload.render({

            elem: '#test2'
            ,url: "{:url('admin/merchant.merchant_list/upload_banner')}"
            ,multiple: true
            ,acceptMime: 'image/*'
            ,number:5
            ,before: function(obj){
                index1=layer.load();
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){

                    $('#demo2').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img" '+'height="75px" '+'width="75px">' )
                });
            }
            ,done: function(res){
                //上传完毕
                var i=$('#img input').length;
                $('#img').append('<input type="hidden" name="banner['+i+++']" value="'+res.data.filePath+'"/>');
            }

            ,allDone: function(obj){ //当文件全部被提交后，才触发
                setTimeout(function () {
                    layer.close(index1);
                },500)
                console.log(obj.total); //得到总文件数
                console.log(obj.successful); //请求成功的文件数
                console.log(obj.aborted); //请求失败的文件数
            }
        });
//        //清空
//        var reset = document.getElementById('reset1');
//        reset.onclick = function(){
//            $('#demo2').children().remove();
//            $('#img').children().remove();
//        };
        form.on('submit(demo1)',function (data) {
            $.ajax({
                url:'update',
                data:data.field,
                type:'post',
                dataType:'json',
                success:function (res) {
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

        $('.address').on('click',function (d) {
            layer.open({
                type:2,
                area: ['50%', '80%'],
                moveOut:true,
                shadeClose:true,
                content:"//apis.map.qq.com/tools/locpicker?search=1&type=1&key=TNVBZ-VE6C2-5DCU5-C6SBW-B4EAZ-NLFAR&referer=myapp"

            });

        })
        window.addEventListener('message', function(event) {
            var loc = event.data;
            console.log(loc);
            if (loc.module != 'locationPicker') return false;
            $('.address').val(loc.poiaddress);
            $('.jingdu').val(loc.latlng.lng);
            $('.weidu').val(loc.latlng.lat);
//            alert('你使用的组件是'+loc.module+',刚选择了'+loc.poiname+',它位于'+loc.poiaddress+',它的经纬度是：'+loc.latlng.lat+','+loc.latlng.lng+',所属城市为:'+loc.cityname)
        }, false);
    })
</script>
</html>
