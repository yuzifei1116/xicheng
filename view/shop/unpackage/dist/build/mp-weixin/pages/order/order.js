(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/order/order"],{"45d9":function(t,e,n){"use strict";n.r(e);var r=n("4b0f"),o=n("c881");for(var i in o)"default"!==i&&function(t){n.d(e,t,function(){return o[t]})}(i);n("e3dc");var a,s=n("f0c5"),d=Object(s["a"])(o["default"],r["b"],r["c"],!1,null,null,null,!1,r["a"],a);e["default"]=d.exports},"4b0f":function(t,e,n){"use strict";var r={"uni-load-more":()=>n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},o=function(){var t=this,e=t.$createElement,n=(t._self._c,t.__map(t.orderList,function(e,n){var r=t.__map(e.cartInfo,function(e,n){var r=t.indexUrl(e.productInfo.image);return{$orig:t.__get_orig(e),m0:r}});return{$orig:t.__get_orig(e),l0:r}}));t.$mp.data=Object.assign({},{$root:{l1:n}})},i=[];n.d(e,"b",function(){return o}),n.d(e,"c",function(){return i}),n.d(e,"a",function(){return r})},"50a9":function(t,e,n){},"5dab":function(t,e,n){"use strict";(function(t){n("3c4b"),n("921b");r(n("66fd"));var e=r(n("45d9"));function r(t){return t&&t.__esModule?t:{default:t}}t(e.default)}).call(this,n("543d")["createPage"])},c881:function(t,e,n){"use strict";n.r(e);var r=n("df4b"),o=n.n(r);for(var i in r)"default"!==i&&function(t){n.d(e,t,function(){return r[t]})}(i);e["default"]=o.a},df4b:function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;r(n("0740"));function r(t){return t&&t.__esModule?t:{default:t}}var o=function(){return n.e("components/empty").then(n.bind(null,"1df2"))},i=function(){return n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},a={components:{uniLoadMore:i,empty:o},data:function(){return{tabCurrentIndex:0,navList:[{state:0,text:"待付款",loadingType:"more",orderList:[]},{state:1,text:"待发货",loadingType:"more",orderList:[]},{state:2,text:"待收货",loadingType:"more",orderList:[]},{state:3,text:"待评价",loadingType:"more",orderList:[]},{state:4,text:"已完成",loadingType:"more",orderList:[]}],orderList:[],source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:20,status:"more"}},onLoad:function(t){this.tabCurrentIndex=t.state,this.page=1,this.limit=20,this.orderList=[],this.getUserOrderList(),console.log(t,"sdfd",this.tabCurrentIndex),this.calcSize()},onShow:function(){},methods:{getUserOrderList:function(e){var n=this;console.log(e,"哈哈哈哈哈哈哈",this.tabCurrentIndex);var r="/gzh/user_api/get_user_order_list",o={type:e||this.tabCurrentIndex,page:n.page,limit:n.limit};getApp().http(r,o,function(e){t.hideLoading();var r=e;r.length>0?(console.log(r.length),n.orderList=n.orderList.concat(r),n.status=r.length>=n.limit?"more":"","more"==n.status&&(n.page+=1)):n.status="",console.log("水电费水电费",n.orderLis)})},tabClick:function(e,n){var r=this;r.tabCurrentIndex=e;var o=n.state;t.showLoading({}),r.orderList=[],r.page=1,r.getUserOrderList(o),console.log("点击",this.tabCurrentIndex,o)},orderdet:function(e){console.log("详情",e),t.navigateTo({url:"/pages/order/orderDetail?uni="+e.order_id})},orderzf:function(e){console.log("支付",e),t.navigateTo({url:"/pages/order/orderDetail?uni="+e.order_id})},ordert:function(e){console.log("支付",e),t.navigateTo({url:"/pages/order/orderT?uni="+e.order_id})},cancelOrder:function(e){t.showLoading({});var n=this,r="/gzh/auth_api/cancel_order",o={order_id:e.order_id};getApp().http(r,o,function(e){t.hideLoading(),200==e.code?(n.getUserOrderList("0"),getApp().showTip(e.msg,"success")):getApp().showTip(e.msg,"none")},!0)},orderEval:function(t){},indexUrl:function(t){var e=t.indexOf("http");return-1==e?this.webUrl+"/"+t:t},scroAddr:function(){var t=this;t.status&&t.getUserOrderList()},calcSize:function(){var e=this;t.getSystemInfo({success:function(n){var r=t.createSelectorQuery().select(".content");e.scrollHeight=n.windowHeight-40,console.log(r),console.log("啊啊啊啊",n)}})},changeTab:function(t){this.tabCurrentIndex=t.target.current,this.loadData("tabChange")},deleteOrder:function(e){var n=this;t.showLoading({title:"请稍后"}),setTimeout(function(){n.navList[n.tabCurrentIndex].orderList.splice(e,1),t.hideLoading()},600)}}};e.default=a}).call(this,n("543d")["default"])},e3dc:function(t,e,n){"use strict";var r=n("50a9"),o=n.n(r);o.a}},[["5dab","common/runtime","common/vendor"]]]);