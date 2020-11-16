{extend name="public/container"}
{block name="content"}

<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        </div>
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">活动列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}')">添加活动</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit')}?id={{d.id}}')">
                            编辑
                        </button>
                        {{# if(d.status == 0 || d.status == 2){ }}
                            <button class="layui-btn layui-btn-normal layui-btn-xs" lay-event='online' href="javascript:void(0);">
                                上线
                            </button>
                        {{# }else if(d.status == 1){ }}
                            <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event='offline' href="javascript:void(0);">
                                下线
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
    layList.tableList('List',"{:Url('activity_list')}",function (){
        return [
            {field: 'id', title: 'ID', sort: true,event:'id',width:'8%'},
            {field: 'title', title: '活动名称'},
            {field: 'image', title: '图片',width:'10%',templet:'#image'},
            {field: 'price', title: '价格',width:'6%'},
            {field: 'original_price', title: '原价',width:'6%'},
            {field: 'sales', title: '销量',width:'6%'},
            {field: 'ficti', title: '虚拟销量',width:'8%'},
            {field: '_status', title: '状态',width:'8%'},
            {field: 'time', title: '活动时间',width:'15%'},
            {field: 'right', title: '操作',align:'center',width:'15%',toolbar:'#act'}
        ];
    });
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
            case 'online':
                var url =layList.U({c:'activity.activity',a:'online',p:{id:data.id}});
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
                },{'title':'确定要上线吗？','text':' ','confirm':'是的，我要上线'})
                break;
                case 'offline':
                var url =layList.U({c:'activity.activity',a:'offline',p:{id:data.id}});
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
                },{'title':'确定要下线吗？','text':' ','confirm':'是的，我要下线'})
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
</script>
{/block}
