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
                                <label class="layui-form-label">商铺</label>
                                <div class="layui-input-block">
                                    <select name="mer_id">
                                        <option value=" ">全部</option>
                                        {volist name='merchants' id='vo'}
                                        <option value="{$vo.id}">{$vo.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">订单号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="order_id" class="layui-input" placeholder="请输入订单号">
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
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('merchant_percentage_list')}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'order_id',width:'10%',templet:'#order_id'},
            {field: 'mer_name', title: '商铺',templet:'#userinfo',width:'20%'},
            {field: 'order_id', title: '订单号',templet:'#userinfo',width:'15%'},
            {field: 'total_price', title: '总价格',width:'10%'},
            {field: 'percentage', title: '抽成比例',width:'10%'},
            {field: 'percentage_price', title: '抽成',width:'10%'},
            {field: 'merchant_price', title: '订单应结'},
            {field: 'add_time', title: '结算时间',width:'15%'},
        ];
    });

    //查询
    layList.search('search',function(where){
        layList.reload(where);
    });
</script>
{/block}
