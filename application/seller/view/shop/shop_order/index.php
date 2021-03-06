{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff;margin-top: -10px;">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">订单号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="order_id" class="layui-input" placeholder="请输入订单号">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-block">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="0">待发货</option>
                                        <option value="1">待收货</option>
                                        <option value="2">已完成</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                    <button class="layui-btn layui-btn-primary layui-btn-sm export"  lay-submit="export" lay-filter="export">
                                        <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">订单列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <!--图片-->
                    <script type="text/html" id="image">
                        <img style="cursor: pointer" lay-event="open_image" src="{{d.image}}">
                    </script>
                    <!--上架|下架-->
                    <script type="text/html" id="status">
                        {{# if(d.status==1){ }}
                        待收货
                        {{# }else if(d.status==2){ }}
                        已完成
                        {{# } }}
                    </script>
                    <!--产品名称-->
                    <script type="text/html" id="user_name">
                        <h4>{{d.user_name}}/{{d.uid}}</h4>
                    </script>
                    <!--产品名称-->
                    <script type="text/html" id="order_id">
                        <span>{{d.order_id}}</span><br>
                        <span style="color: green">[积分订单]</span>
                    </script>
                    <!--商品信息-->
                    <script type="text/html" id="info">
                        <p>
                            <span>
                                <img style="width: 30px;height: 30px;margin:0;cursor: pointer;" src="{{d.image}}">
                            </span>
                            <span>{{d.store_name}}&nbsp;</span>
                            <span> | ￥{{d.integral}}×{{d.total_num}}</span>
                        </p>
                    </script>
                    <!--操作-->
                    <script type="text/html" id="act">
                        {{# if(d.status==0){ }}
                        <button class="btn btn-primary btn-xs" type="button" onclick="$eb.createModalFrame('去发货','{:Url('deliver_goods')}?id={{d.id}}',{w:400,h:300})">
                            <i class="fa fa-cart-plus"></i> 去发货</button>
                        <button class="btn btn-primary btn-xs" lay-event='danger'>
                            <i class="fa fa-cart-arrow-down"></i> 收货
                        </button>
                        {{# }else if(d.status==1){ }}
                        <button class="btn btn-primary btn-xs" lay-event='danger'>
                            <i class="fa fa-cart-arrow-down"></i> 收货
                        </button>
                        <button class="btn btn-primary btn-xs" lay-event='order_info'>
                            <i class="fa fa-file-text"></i> 订单详情
                        </button>
                        {{# }else{ }}
                        <button class="btn btn-primary btn-xs" lay-event='order_info'>
                            <i class="fa fa-file-text"></i> 订单详情
                        </button>
                        {{# } }}
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('order_list')}",function (){
        return [
            {field: 'order_id', title: '订单号',templet:"#order_id",width:'15%'},
            {field: 'user_name', title: '用户名',width:'10%'},
            {field: 'info', title: '商品信息',templet:"#info"},
            {field: 'total_integral', title: '总价格',width:'8%'},
            {field: 'status', title: '订单状态',templet:'#status',width:'8%'},
            {field: 'add_time', title: '下单时间',width:'10%',sort: true},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });
    //导出
    layList.search('export',function(where){
        var q={},where=where || {};
        q.order_id=where.order_id || '';
        q.status=where.status || '';
        location.href=layList.U({c:'shop.shop_order',a:'export',q:q});
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'price':
                action.set_product('price',id,value);
                break;
            case 'stock':
                action.set_product('stock',id,value);
                break;
            case 'order':
                action.set_product('order',id,value);
                break;
            case 'ficti':
                action.set_product('ficti',id,value);
                break;
        }
    });
    //上下加产品
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({c:'shop.shop_order',a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'shop.shop_order',a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({c:'shop.shop_order',a:'delete',q:{id:data.id}});
                if(data.is_del) var code = {title:"操作提示",text:"确定恢复产品操作吗？",type:'info',confirm:'是的，恢复该产品'};
                else var code = {title:"操作提示",text:"确定将该产品移入回收站吗？",type:'info',confirm:'是的，移入回收站'};
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
                },code);
                break;
            case 'danger':
                var url =layList.U({c:'shop.shop_order',a:'take_delivery',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '收货失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修改收货状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
            case 'order_info':
                $eb.createModalFrame(data.user_name+'-订单详情',layList.U({a:'order_info',q:{oid:data.id}}));
                break;
        }
    })
    //排序
    layList.sort(function (obj) {
        var type = obj.type;
        switch (obj.field){
            case 'id':
                layList.reload({order: layList.order(type,'p.id')},true,null,obj);
                break;
            case 'sales':
                layList.reload({order: layList.order(type,'p.sales')},true,null,obj);
                break;
        }
    });
    //查询
    layList.search('search',function(where){
        layList.reload(where);
    });
    //自定义方法
    var action={
        set_product:function(field,id,value){
            layList.baseGet(layList.Url({c:'shop.shop_order',a:'set_product',q:{field:field,id:id,value:value}}),function (res) {
                layList.msg(res.msg);
            });
        },
        show:function(){
            var ids=layList.getCheckData().getIds('id');
            if(ids.length){
                layList.basePost(layList.Url({c:'shop.shop_order',a:'product_show'}),{ids:ids},function (res) {
                    layList.msg(res.msg);
                    layList.reload();
                });
            }else{
                layList.msg('请选择要上架的产品');
            }
        }
    };
    //多选事件绑定
    $('.layui-btn-container').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function(){
            action[type] && action[type]();
        })
    });
</script>
{/block}
