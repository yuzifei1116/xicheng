(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/user/user_integral"],{"2b9b":function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=function(){return n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},i={components:{uniLoadMore:o},data:function(){return{cateMaskState:0,headerPosition:"fixed",headerTop:"0px",loadingType:"more",filterIndex:0,cateId:0,priceOrder:0,cateList:[],goodsList:[],sid:0,salesOrder:"",source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:20,status:"more",keyword:"",type:0,userInfo:[]}},onLoad:function(t){this.type=t.type,console.log("页面",t),getApp().user_api(!0),this.calcSize(),this.getMyUserInfo(),this.getProductList()},onShow:function(){},onPageScroll:function(t){},onPullDownRefresh:function(){console.log("refresh"),setTimeout(function(){t.stopPullDownRefresh()},1e3)},onReachBottom:function(){},methods:{getMyUserInfo:function(){var t=this;console.log("排序",t.filterIndex);var e="/gzh/user_api/get_my_user_info",n={isIntegral:1};getApp().http(e,n,function(e){t.userInfo=e})},getProductList:function(e){var n=this;console.log("排序",n.filterIndex);var o="/gzh/user_api/user_integral_list",i={page:n.page,limit:n.limit};getApp().http(o,i,function(e){console.log("详情",e),t.hideLoading();var o=e;o.length>0?(console.log("总条数",o.length),n.goodsList=n.goodsList.concat(o),n.status=o.length>=n.limit?"more":"","more"==n.status&&(n.page+=1)):n.status=""})},tabClick:function(e){var n=this;console.log("点击",e),n.filterIndex=e,0==n.filterIndex?(t.showLoading({title:"正在加载"}),console.log("优惠券"),n.page=1,n.goodsList=[],n.type=0,n.getProductList()):1==n.filterIndex&&(n.page=1,n.goodsList=[],n.type=1,console.log("我的"))},navint:function(){t.switchTab({url:"/pages/index/index"})},scroAddr:function(){var t=this;t.status&&t.getProductList()},calcSize:function(){var e=this;t.getSystemInfo({success:function(n){t.createSelectorQuery().select(".content");e.scrollHeight=n.windowHeight-230}})}}};e.default=i}).call(this,n("543d")["default"])},4953:function(t,e,n){"use strict";n.r(e);var o=n("2b9b"),i=n.n(o);for(var r in o)"default"!==r&&function(t){n.d(e,t,function(){return o[t]})}(r);e["default"]=i.a},"8dc0":function(t,e,n){},c38c:function(t,e,n){"use strict";var o={"uni-load-more":()=>n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},i=function(){var t=this,e=t.$createElement;t._self._c},r=[];n.d(e,"b",function(){return i}),n.d(e,"c",function(){return r}),n.d(e,"a",function(){return o})},dfcb:function(t,e,n){"use strict";(function(t){n("3c4b"),n("921b");o(n("66fd"));var e=o(n("f6de"));function o(t){return t&&t.__esModule?t:{default:t}}t(e.default)}).call(this,n("543d")["createPage"])},f289:function(t,e,n){"use strict";var o=n("8dc0"),i=n.n(o);i.a},f6de:function(t,e,n){"use strict";n.r(e);var o=n("c38c"),i=n("4953");for(var r in i)"default"!==r&&function(t){n.d(e,t,function(){return i[t]})}(r);n("f289");var c,s=n("f0c5"),u=Object(s["a"])(i["default"],o["b"],o["c"],!1,null,null,null,!1,o["a"],c);e["default"]=u.exports}},[["dfcb","common/runtime","common/vendor"]]]);