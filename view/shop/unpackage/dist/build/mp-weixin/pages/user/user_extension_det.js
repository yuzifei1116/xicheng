(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/user/user_extension_det"],{7450:function(t,e,n){"use strict";n.r(e);var r=n("b2ee"),o=n.n(r);for(var i in r)"default"!==i&&function(t){n.d(e,t,function(){return r[t]})}(i);e["default"]=o.a},9562:function(t,e,n){"use strict";var r={"uni-load-more":()=>n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},o=function(){var t=this,e=t.$createElement,n=(t._self._c,t.__map(t.orderList,function(e,n){var r=t.__map(e.cartInfo,function(e,n){var r=t.indexUrl(e.productInfo.image);return{$orig:t.__get_orig(e),m0:r}});return{$orig:t.__get_orig(e),l0:r}}));t.$mp.data=Object.assign({},{$root:{l1:n}})},i=[];n.d(e,"b",function(){return o}),n.d(e,"c",function(){return i}),n.d(e,"a",function(){return r})},a860:function(t,e,n){},b2ee:function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;r(n("0740"));function r(t){return t&&t.__esModule?t:{default:t}}var o=function(){return n.e("components/empty").then(n.bind(null,"1df2"))},i=function(){return n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},u={components:{uniLoadMore:i,empty:o},data:function(){return{tabCurrentIndex:0,navList:[],orderList:[],source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:20,status:"more",uid:0}},onLoad:function(t){this.uid=t.uid,this.calcSize(),this.page=1,this.limit=20,this.orderList=[],this.getUserOrderList()},onShow:function(){},methods:{getUserOrderList:function(){var t=this,e="/gzh/user_api/user_spread_order",n={uid:t.uid,page:t.page,limit:t.limit};getApp().http(e,n,function(e){var n=e;n.length>0?(t.status=n.length<=t.limit?"more":"","more"==t.status&&(t.page+=1,console.log(t.page),console.log("=================444444444444")),t.orderList=t.orderList.concat(n),console.log("都发给对方",t.orderList)):t.status="",console.log("水电费水电费",t.orderList,n)})},tabClick:function(t,e){var n=this;n.tabCurrentIndex=t;var r=e.state;n.orderList=[],n.page=1,n.getUserOrderList(r),console.log("点击",this.tabCurrentIndex,r)},indexUrl:function(t){var e=t.indexOf("http");return-1==e?this.webUrl+"/"+t:t},scroAddr:function(){var t=this;t.status&&t.getUserOrderList()},calcSize:function(){var e=this;t.getSystemInfo({success:function(n){var r=t.createSelectorQuery().select(".content");e.scrollHeight=n.windowHeight,console.log(r),console.log("啊啊啊啊",n)}})},orderdet:function(e){t.navigateTo({url:"/pages/integral/intOrderDetail?id="+e.id})},deleteOrder:function(e){var n=this;t.showLoading({title:"请稍后"}),setTimeout(function(){n.navList[n.tabCurrentIndex].orderList.splice(e,1),t.hideLoading()},600)}}};e.default=u}).call(this,n("543d")["default"])},c8eb:function(t,e,n){"use strict";var r=n("a860"),o=n.n(r);o.a},dfad:function(t,e,n){"use strict";n.r(e);var r=n("9562"),o=n("7450");for(var i in o)"default"!==i&&function(t){n.d(e,t,function(){return o[t]})}(i);n("c8eb");var u,s=n("f0c5"),a=Object(s["a"])(o["default"],r["b"],r["c"],!1,null,null,null,!1,r["a"],u);e["default"]=a.exports},ed7b:function(t,e,n){"use strict";(function(t){n("3c4b"),n("921b");r(n("66fd"));var e=r(n("dfad"));function r(t){return t&&t.__esModule?t:{default:t}}t(e.default)}).call(this,n("543d")["createPage"])}},[["ed7b","common/runtime","common/vendor"]]]);