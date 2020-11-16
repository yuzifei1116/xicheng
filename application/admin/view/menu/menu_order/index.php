{extend name="public/container"}
{block name="content"}

<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
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
                                <label class="layui-form-label">支付状态</label>
                                <div class="layui-input-block">
                                    <select name="paid">
                                        <option value="">全部</option>
                                        <option value="0">未支付</option>
                                        <option value="1">已支付</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">订单状态</label>
                                <div class="layui-input-block">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="0">待付款</option>
                                        <option value="1">待完成</option>
                                        <option value="2">待评价</option>
                                        <option value="3">已完成</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">选择时间：</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="add_time" id="add_time" placeholder=" - ">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
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
                <div class="layui-card-header">单餐表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="pay">
                        总金额:{{d.money}} <br/>
                        优惠金额:{{d.discount_price}}<br/>
                        积分抵扣:{{d.integral_deduction}}<br/>
                        实付金额:<span style="color:red">{{d.pay_price}}</span>
                    </script>
                    <script type="text/html" id="menu">
                        {{# layui.each(d.menu_info, function(index,item){ }}
                        <div class="layui-col-md12">
                            <div class="layui-col-md6">
                                {{item.menu_title}}
                            </div>
                            <div class="layui-col-md2">
                                X
                            </div>
                            <div class="layui-col-md4">
                                {{item.num}}
                            </div>
                        </div>
                        {{# }); }}
                    </script>
                </div>
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
    layList.tableList('List',"{:Url('order_list')}",function (){
        return [
            {field: 'id', title: 'ID', sort: true,event:'id',width:'6%'},
            {field: 'order_id', title: '订单号',width:'14%'},
            {field: 'table_num', title: '桌号',width:'8%'},
            {field: 'people_num', title: '人数',width:'8%'},
            {field: 'menu', title: '菜单',templet:'#menu'},
            {field: 'pay', title: '支付信息',templet:'#pay',width:'12%'},
            {field: '_paid', title: '支付状态',width:'8%'},
            {field: '_status', title: '订单状态 ',width:'8%'},
            {field: 'add_time', title: '添加时间',width:'15%'},
            // {field: 'right', title: '操作',align:'center',toolbar:'#act'}
        ];
    });
    layList.date('add_time');
    //自定义方法
    var action= {
        set_category: function (field, id, value) {
            layList.baseGet(layList.Url({
                c: 'store.store_category',
                a: 'set_category',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where);
    });
    layList.switch('status',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({c:'stay.stay_type',a:'set_status',p:{status:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'stay.stay_type',a:'set_status',p:{status:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'cate_name':
                action.set_category('cate_name',id,value);
                break;
            case 'sort':
                action.set_category('sort',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'check_in':
                var url =layList.U({c:'stay.stay_info',a:'check_in',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload();
                            setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '操作失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'确定要入住吗？','text':'确定后将无法修改,请谨慎操作！','confirm':'是的，我要修改'})
                break;
                case 'check_out':
                var url =layList.U({c:'stay.stay_info',a:'check_out',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload();
                            setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '操作失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'确定要退房吗？','text':'确定后将无法修改,请谨慎操作！','confirm':'是的，我要修改'})
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
</script>
{/block}
