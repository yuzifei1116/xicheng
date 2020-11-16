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
                <div class="alert alert-info" role="alert">
                    可提现金<b style="color: red">{$money}</b>元
                </div>
                <div class="layui-btn-container">
                    <button class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}',{h:700,w:1100})">申请提现</button>
                </div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
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
    layList.tableList('List',"{:Url('merchant_withdraw_ist')}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'order_id',width:'14%',templet:'#order_id'},
            {field: 'money', title: '申请金额'},
            {field: 'bank_name', title: '银行名称',width:'15%'},
            {field: 'account_bank', title: '银行账号',width:'15%'},
            {field: 'account_name', title: '银行账户',width:'15%'},
            {field: '_status', title: '申请状态',width:'10%'},
            {field: 'remark', title: '备注',width:'10%'},
            {field: 'add_time', title: '申请时间',width:'10%'},
        ];
    });

    //查询
    layList.search('search',function(where){
        layList.reload(where);
    });
</script>
{/block}
