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
                                <label class="layui-form-label">优惠券类型</label>
                                <div class="layui-input-block">
                                    <select name="type">
                                        <option value="">全部</option>
                                        <option value="0">通用</option>
                                        <option value="1">商城</option>
                                        <option value="2">住宿</option>
                                        <option value="3">活动</option>
                                        <option value="4">点餐</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">使用状态</label>
                                <div class="layui-input-block">
                                    <select name="is_use">
                                        <option value="">全部</option>
                                        <option value="0">未使用</option>
                                        <option value="1">已使用</option>
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
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="status">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='status' lay-text='上架|下架'  {{ d.status == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit')}?id={{d.id}}')">
                            编辑
                        </button>
                        <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delstor'>
                            删除
                        </button>
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
    setTimeout(function () {
        $('.alert-info').hide();
    },3000);
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('use_list')}",function (){
        return [
            {field: 'id', title: 'ID', sort: true,event:'id',width:'10%'},
            {field: 'nickname', title: '用户昵称',width:'10%'},
            {field: 'title', title: '优惠券名称'},
            {field: '_type', title: '优惠券类型',width:'10%'},
            {field: '_is_use', title: '状态',width:'10%'},
            {field: 'time', title: '有效期',width:'25%'},
            {field: 'add_time', title: '添加时间',width:'15%'},
            // {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'20%'},
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
            layList.baseGet(layList.Url({c:'coupon.coupon',a:'set_status',p:{status:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'coupon.coupon',a:'set_status',p:{status:0,id:value}}),function (res) {
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
            case 'delstor':
                var url=layList.U({c:'coupon.coupon_use',a:'delete',q:{id:data.id}});
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
                $eb.openImage(data.pic);
                break;
        }
    })
</script>
{/block}
