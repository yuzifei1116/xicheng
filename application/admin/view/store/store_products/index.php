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
                                <label class="layui-form-label">产品名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="store_name" class="layui-input" placeholder="请输入产品名称,关键字,编号">
                                    <input type="hidden" name="type" value="{$type}">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">所有分类</label>
                                <div class="layui-input-block">
                                    <select name="cate_id">
                                        <option value=" ">全部</option>
                                        {volist name='cate' id='vo'}
                                        <option value="{$vo.id}">{$vo.html}{$vo.cate_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">商铺分类</label>
                                <div class="layui-input-block">
                                    <select name="mer_id">
                                        <option value="">全部</option>
                                        {volist name='merchants' id='vo'}
                                        <option value="{$vo.id}">{$vo.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
<!--                                    <button class="layui-btn layui-btn-primary layui-btn-sm export"  lay-submit="export" lay-filter="export">-->
<!--                                        <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出</button>-->
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
                <div class="layui-card-header">分类列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <!--图片-->
                    <script type="text/html" id="image">
                        <img style="cursor: pointer" lay-event="open_image" src="{{d.image}}">
                    </script>
                    <!--热卖-->
                    <script type="text/html" id="is_hot">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_hot' lay-text='是|否'  {{ d.is_hot == 1 ? 'checked' : '' }}>
                    </script>
                    <!--促销-->
                    <script type="text/html" id="is_benefit">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_benefit' lay-text='是|否'  {{ d.is_benefit == 1 ? 'checked' : '' }}>
                    </script>
                    <!--精品-->
                    <script type="text/html" id="is_best">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_best' lay-text='是|否'  {{ d.is_best == 1 ? 'checked' : '' }}>
                    </script>
                    <!--上架|下架-->
                    <script type="text/html" id="checkboxstatus">
                        {{# if(d.is_show==1){ }}
                        <p>上架</p>
                        {{# }else{ }}
                        <p>下架</p>
                        {{# } }}
                    </script>

                    <script type="text/html" id="status">
                        {{# if(d.status==1){ }}
                        <p>审核通过</p>
                        {{# }else{ }}
                        <p>违规下架</p>
                        {{# } }}
                    </script>
                    <!--收藏-->
                    <script type="text/html" id="like">
                        <span><i class="layui-icon layui-icon-praise"></i> {{d.like}}</span>
                    </script>
                    <!--点赞-->
                    <script type="text/html" id="collect">
                        <span><i class="layui-icon layui-icon-star"></i> {{d.collect}}</span>
                    </script>
                    <!--产品名称-->
                    <script type="text/html" id="store_name">
                        <h4>{{d.store_name}}</h4>
                        <p>价格:<font color="red">{{d.price}}</font> </p>
                        {{# if(d.cate_name!=''){ }}
                        <p>分类:{{d.cate_name}}</p>
                        {{# } }}
                        <p>访客量:{{d.visitor}}</p>
                        <p>浏览量:{{d.browse}}</p>
                    </script>
                    <script type="text/html" id="act">
                        {{# if(d.status==0){ }}
                        <button class="layui-btn layui-btn-xs layui-btn-normal" lay-event='adopt'>
                            审核通过
                        </button>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-xs layui-btn-danger" lay-event='violations'>
                            违规
                        </button>
                        {{# } }}
                        <button type="button" class="layui-btn layui-btn-xs btn-success" onclick="$eb.createModalFrame('属性','{:Url('attr')}?id={{d.id}}',{h:600,w:800})">
                            属性
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
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('product_list')}",function (){
        return [
            {field: 'id', title: 'ID', sort: true,event:'id',width:'6%'},
            {field: 'mer_name', title: '商铺', sort: true,event:'id',width:'6%'},
            {field: 'image', title: '产品图片',templet:'#image',width:'8%'},
            {field: 'store_name', title: '产品名称',templet:'#store_name'},
            {field: 'ficti', title: '虚拟销量',width:'6%'},
            {field: 'stock', title: '库存',width:'6%'},
            {field: 'sort', title: '排序',width:'4%'},
            {field: 'sales', title: '销量',sort: true,event:'sales',width:'4%'},
            {field: 'collect', title: '点赞',templet:'#like',width:'4%'},
            {field: 'like', title: '收藏',templet:'#collect',width:'4%'},
            {field: 'show', title: '上/下架',templet:"#checkboxstatus",width:'5%'},
            {field: 'is_hot', title: '热卖',templet:"#is_hot",width:'6%'},
            {field: 'is_hot', title: '促销',templet:"#is_benefit",width:'6%'},
            {field: 'is_hot', title: '精品',templet:"#is_best",width:'6%'},
            {field: 'status', title: '审核状态',templet:"#status",width:'6%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
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
    //热卖
    layList.switch('is_hot',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'is_hot',p:{is_hot:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'is_hot',p:{is_hot:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //促销
    layList.switch('is_benefit',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'is_benefit',p:{is_benefit:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'is_benefit',p:{is_benefit:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //精品
    layList.switch('is_best',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'is_best',p:{is_best:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'is_best',p:{is_best:0,id:value}}),function (res) {
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
            case 'adopt':
                var url=layList.U({a:'adopt',q:{id:data.id}});
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
                },{'title':'您确定要此操作吗？','text':'操作后此商品将上架,请谨慎操作！','confirm':'是的，我要修改'})
                break;
            case 'violations':
                var url=layList.U({a:'violations',q:{id:data.id}});
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
                },{'title':'您确定要此操作吗？','text':'操作后此商品将违规下架,请谨慎操作！','confirm':'是的，我要修改'})
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
