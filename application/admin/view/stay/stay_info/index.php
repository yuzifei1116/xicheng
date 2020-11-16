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
                                <label class="layui-form-label">房间号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="number" value="{$number}" class="layui-input" placeholder="请输入房间号">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="name" class="layui-input" placeholder="请输入用户昵称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">房间状态</label>
                                <div class="layui-input-block">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="0">未入住</option>
                                        <option value="1">已入住</option>
                                        <option value="2">已退房</option>
                                        <option value="3">已退款</option>
                                    </select>
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
                                <label class="layui-form-label">房间类型</label>
                                <div class="layui-input-block">
                                    <select name="type_id">
                                        <option value="">全部</option>
                                        {volist name="type" id="vo"}
                                        <option value="{$vo.id}">{$vo.title}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">选择时间：</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input time-w" name="add_time" id="add_time" placeholder=" - ">
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
                <div class="layui-card-header">入住信息表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>

                    <script type="text/html" id="status">
                        {{# if(d.status == 0){ }}
                            <span>未入住</span>
                        {{# }else if(d.status == 1){ }}
                            <span style="color: #0bb20c">已入住</span><br/>
                            <span>时间；{{d.check_in_time}}</span>
                        {{# }else if(d.status == 2){ }}
                            <span style="color: darkred">已退房</span><br/>
                            <span>时间；{{d.check_out_time}}</span>
                        {{# }else{ }}
                            <span>已退款</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="type_status">
                        {{# if(d.type_status == 0){ }}
                            <span style="color: red">禁用</span>
                        {{# }else if(d.type_status == 1){ }}
                            正常
                        {{# } }}
                    </script>
                    <script type="text/html" id="pay">
                        总金额:{{d.money}} <br/>
                        优惠金额:{{d.discount_price}}<br/>
                        积分抵扣:{{d.integral_deduction}}<br/>
                        实付金额:<span style="color:red">{{d.pay_price}}</span>
                    </script>
                    <script type="text/html" id="info">
                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('入住信息','{:Url('stay_info')}?id={{d.id}}')">查看</a>
                    </script>
                    <script type="text/html" id="act">
                        {{# if(d.status == 0 && d.paid == 1){ }}
                            <button class="layui-btn layui-btn-xs" lay-event='check_in' href="javascript:void(0);">
                                入住
                            </button>
                        {{# }else if(d.status == 1 && d.paid == 1){ }}
                            <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event='check_out' href="javascript:void(0);">
                                退房
                            </button>
                        {{# } }}
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
    layList.tableList('List',"{:Url('info_list')}",function (){
        return [
            {field: 'id', title: 'ID', sort: true,event:'id',width:'5%'},
            {field: 'title', title: '房间类型',width:'7%'},
            {field: 'number', title: '房间号',width:'6%'},
            {field: 'name', title: '用户名',width:'6%'},
            {field: 'price', title: '价格/天',width:'6%'},
            {field: 'people_num', title: '人数',width:'5%'},
            {field: 'day_num', title: '天数',width:'5%'},
            {field: 'pay', title: '支付信息',width:'10%',templet:'#pay'},
            {field: '_paid', title: '支付状态',width:'7%'},
            {field: 'status', title: '房间状态',templet:'#status',width:'11%'},
            {field: 'type_status', title: '房间类型状态',templet:'#type_status',width:'9%'},
            {field: 'start_time', title: '预定时间',width:'8%'},
            {field: 'add_time', title: '添加时间',width:'8%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'}
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
