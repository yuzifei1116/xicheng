{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">一级推广人订单列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                </div>
                <!--订单-->
                <script type="text/html" id="order_id">
                    {{d.order_id}}<br/>
                    <span style="color: {{d.color}};">{{d.pink_name}}</span><br/>　
                    {{#  if(d.is_del == 1){ }}<span style="color: {{d.color}};">用户已删除</span>{{# } }}　
                </script>
                <!--用户信息-->
                <script type="text/html" id="userinfo">
                    {{d.nickname==null ? '暂无信息':d.nickname}}/{{d.uid}}
                </script>
                <!--支付状态-->
                <script type="text/html" id="paid">
                    {{#  if(d.pay_type==1){ }}
                    <p>{{d.pay_type_name}}</p>
                    {{#  }else{ }}
                    {{# if(d.pay_type_info!=undefined){ }}
                    <p><span>线下支付</span></p>
                    <p><button type="button" class="btn btn-w-m btn-white">立即支付</button></p>
                    {{# }else{ }}
                    <p>{{d.pay_type_name}}</p>
                    {{# } }}
                    {{# }; }}
                </script>
                <!--订单状态-->
                <script type="text/html" id="status">
                    {{d.status_name}}
                </script>
                <!--商品信息-->
                <script type="text/html" id="info">
                    {{#  layui.each(d._info, function(index, item){ }}
                    {{#  if(item.cart_info.productInfo.attrInfo!=undefined){ }}
                    <p>
                            <span>
                                <img style="width: 30px;height: 30px;margin:0;cursor: pointer;" src="{{item.cart_info.productInfo.attrInfo.image}}">
                            </span>
                        <span>{{item.cart_info.productInfo.store_name}}&nbsp;{{item.cart_info.productInfo.attrInfo.suk}}</span>
                        <span> | ￥{{item.cart_info.truePrice}}×{{item.cart_info.cart_num}}</span>
                    </p>
                    {{#  }else{ }}
                    <p>
                        <span><img style="width: 30px;height: 30px;margin:0;cursor: pointer;" src="{{item.cart_info.productInfo.image}}"></span>
                        <span>{{item.cart_info.productInfo.store_name}}</span><span> | ￥{{item.cart_info.truePrice}}×{{item.cart_info.cart_num}}</span>
                    </p>
                    {{# } }}
                    {{#  }); }}
                </script>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('get_order_stair',['uid'=>$uid])}",function (){
        return [
            {field: 'order_id', title: '订单号', sort: true,event:'order_id',width:'16%',templet:'#order_id'},
            {field: 'nickname', title: '用户信息',templet:'#userinfo',width:'10%'},
            {field: 'info', title: '商品信息',templet:"#info"},
            {field: 'pay_price', title: '实际支付',width:'10%'},
            {field: 'paid', title: '支付状态',templet:'#paid',width:'10%'},
            {field: 'status', title: '订单状态',templet:'#status',width:'10%'},
            {field: 'add_time', title: '下单时间',width:'10%',sort: true},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'name':
                action.set_value('name',id,value);
                break;
            case 'grade':
                action.set_value('grade',id,value);
                break;
            case 'discount':
                action.set_value('discount',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                })
                break;
            case 'open_image':
                $eb.openImage(data.icon);
                break;
        }
    })
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
</script>
{/block}
