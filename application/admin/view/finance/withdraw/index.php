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
                                <label class="layui-form-label">提现状态</label>
                                <div class="layui-input-block">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="0">申请中</option>
                                        <option value="1">申请成功</option>
                                        <option value="2">申请失败</option>

                                    </select>
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
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                </div>
                <script type="text/html" id="act">
                    {{# if(d.status==0){ }}
                    <button class="layui-btn layui-btn-success layui-btn-xs" lay-event='success'>
                        通过
                    </button>
                    <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event='danger'>
                        拒绝
                    </button>
                    {{# } }}
                </script>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('merchant_withdraw_ist')}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'order_id',width:'6%',templet:'#order_id'},
            {field: 'money', title: '申请金额'},
            {field: 'bank_name', title: '银行名称',width:'15%'},
            {field: 'account_bank', title: '银行账号',width:'15%'},
            {field: 'account_name', title: '银行账户',width:'15%'},
            {field: '_status', title: '申请状态',width:'8%'},
            {field: 'remark', title: '备注',width:'10%'},
            {field: 'add_time', title: '申请时间',width:'10%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });

    //查询
    layList.search('search',function(where){
        layList.reload(where);
    });
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'success':
                var url =layList.U({c:'finance.withdraw',a:'yes',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '操作失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要通过审核吗？','text':'确定后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'});
                break;
            case 'danger':
                var url =layList.U({c:'finance.withdraw',a:'no',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '操作失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要拒绝提现吗？','text':'确定后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                break;
            case 'order_info':
                $eb.createModalFrame(data.nickname+'订单详情',layList.U({a:'order_info',q:{oid:data.id}}));
                break;
        }
    })
</script>
{/block}
