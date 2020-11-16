{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">奖品列表</div>
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        <p style="color: red">奖品概率为千分制,请填写0-1000之间的整数,添加奖品时请确保总概率为1000,否则影响中奖的概率<p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}?aid={$aid}',{h:700,w:1100})">添加奖品</button>
                        <a class="layui-btn layui-btn-sm" href="{:url('admin/activity.prize_act/index')}">活动首页</a>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="number">
                        {{# if(d.is_limit==0){ }}
                        不限量
                        {{# }else{ }}
                        {{d.number}}
                        {{# } }}
                    </script>
                    <script type="text/html" id="checkboxstatus">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='开启|关闭'  {{ d.status == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                            <li>
                                <a href="javascript:void(0)" onclick="$eb.createModalFrame(this.innerText,'{:Url(\'edit\')}?id={{d.id}}')">
                                    <i class="fa fa-pencil"></i> 编辑
                                </a>
                            </li>
                            <li>
                                <a lay-event='delete' href="javascript:void(0)" >
                                    <i class="fa fa-trash"></i> 删除
                                </a>
                            </li>
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
    layList.tableList('List',"{:Url('get_prize_list')}?aid={$aid}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id'},
            {field: 'prize_name', title: '奖品名称',width:'18%'},
            {field: 'title', title: '活动标题',width:'18%'},
            {field: 'image', title: '图片',templet:'#image',width:'15%'},
            {field: 'number', title: '数量',templet:'#number',width:'8%'},
            {field: 'probability', title: '概率',edit:'probability',width:'8%'},
            {field: 'order', title: '排序',width:'8%'},
            {field: 'add_time', title: '添加时间',width:'10%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
        ];
    });
//    //自定义方法
//    var action= {
//        set_value: function (field, id, value) {
//            layList.baseGet(layList.Url({
//                a: 'set_value',
//                q: {field: field, id: id, value: value}
//            }), function (res) {
//                layList.msg(res.msg);
//            });
//        },
//    };
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'probability':
                action.set_probability('probability',id,value);
                break;
        }
    });
    //自定义方法
    var action={
        set_probability:function(field,id,value){
            layList.baseGet(layList.Url({c:'activity.prize_list',a:'set_probability',q:{field:field,id:id,value:value}}),function (res) {
                layList.msg(res.msg);
            });
        },
    };
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({c:'activity.prize_act',a:'set_show',p:{status:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'activity.prize_act',a:'set_show',p:{status:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
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
                $eb.openImage(data.image);
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
