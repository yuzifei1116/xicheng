{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff;margin-top: -10px;">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card-header">商铺列表</div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">店主姓名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="real_name" class="layui-input" placeholder="请输入店主姓名">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">推荐</label>
                                <div class="layui-input-block">
                                    <select name="recommend">
                                        <option value="">全部</option>
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-block">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="1">正常</option>
                                        <option value="0">禁止</option>
                                        <option value="0">审核中</option>
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
<!--                    <div class="layui-btn-container">-->
<!--                        <button class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}',{h:700,w:1100})">添加商铺</button>-->
<!--                    </div>-->
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <!--图片-->
                    <script type="text/html" id="image">
                        <img style="cursor: pointer" lay-event="open_image" src="{{d.image}}">
                    </script>
                    <!--上架|下架-->
                    <script type="text/html" id="checkboxrecommend">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='recommend' lay-text='是|否'  {{ d.recommend == 1 ? 'checked' : '' }}>
                    </script>
                    <!--产品名称-->
                    <script type="text/html" id="store_name">
                        <h4>{{d.store_name}}</h4>
                        <p>价格:<font color="red">{{d.integral}}</font> </p>
                    </script>
                    <!--操作-->
                    <script type="text/html" id="act">
                        {{# if(d.status==1 || d.status==0){ }}
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('编辑','{:Url('edit')}?id={{d.id}}',{h:700,w:1100})">
                            编辑
                        </button>
                        <button class="layui-btn layui-btn-xs layui-btn-danger" href="javascript:void(0);" lay-event='delstor'>
                            删除
                        </button>

                        {{# }else if(d.status==2){ }}
                        <button class="layui-btn layui-btn-xs layui-btn-success" href="javascript:void(0);" lay-event='agree'>
                            审核通过
                        </button>
                        <button class="layui-btn layui-btn-xs layui-btn-danger" href="javascript:void(0);" lay-event='noagree'>
                            拒绝
                        </button>
                        {{# }else if(d.status==3){ }}
                        <button class="layui-btn layui-btn-xs layui-btn-normal" href="javascript:void(0);" lay-event='agree'>
                            通过
                        </button>
                        {{# } }}
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-warm" onclick="$eb.createModalFrame('产品详情','{:Url('detail')}?id={{d.id}}',{h:700,w:1100})">
                            店铺详情
                        </button>
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
    layList.tableList('List',"{:Url('merchant_list')}",function (){
        return [
            {field: 'id', title: 'ID', sort: true,event:'id',width:'6%'},
            {field: 'mer_name', title: '店铺名称'},
            {field: 'account', title: '店铺账号'},
            {field: 'image', title: 'logo',templet:'#image',width:'15%'},
            {field: '_status', title: '状态',width:'10%'},
            {field: 'sort', title: '排序',edit:'sort',width:'10%'},
            {field: 'recommend', title: '推荐',templet:"#checkboxrecommend",width:'10%'},
            {field: 'add_time', title: '申请时间',width:'10%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'14%'},
        ];
    });
    //导出
    layList.search('export',function(where){
        var q={},where=where || {};
        q.store_name=where.store_name || '';
        q.is_show=where.is_show || '';
        location.href=layList.U({c:'merchant.merchant_list',a:'export',q:q});
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
            case 'sort':
                action.set_product('sort',id,value);
                break;
            case 'ficti':
                action.set_product('ficti',id,value);
                break;
        }
    });
    //上下加产品
    layList.switch('recommend',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({c:'merchant.merchant_list',a:'set_recommend',p:{recommend:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'merchant.merchant_list',a:'set_recommend',p:{recommend:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({c:'merchant.merchant_list',a:'delete',q:{id:data.id}});
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
                },code)
                break;
            case 'agree':
                var url=layList.U({c:'merchant.merchant_list',a:'agree',q:{id:data.id}});
                var code = {title:"操作提示",text:"确定该店铺通过审核吗？",type:'info',confirm:'是的,通过'};
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
//                            obj.del();
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '操作失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },code)
                break;
            case 'noagree':
                var url=layList.U({c:'merchant.merchant_list',a:'noagree',q:{id:data.id}});
                var code = {title:"操作提示",text:"确定拒绝该店铺的审核吗？",type:'info',confirm:'是的,拒绝'};
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
//                            obj.del();
                            parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},2000);
                        }else
                            return Promise.reject(res.data.msg || '操作失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },code)
                break;
            case 'open_image':
                $eb.openImage(data.image);
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
            layList.baseGet(layList.Url({c:'merchant.merchant_list',a:'set_product',q:{field:field,id:id,value:value}}),function (res) {
                layList.msg(res.msg);
            });
        },
        show:function(){
            var ids=layList.getCheckData().getIds('id');
            if(ids.length){
                layList.basePost(layList.Url({c:'merchant.merchant_list',a:'product_show'}),{ids:ids},function (res) {
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
