(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/user/user_extension"],{"2dcb":function(t,e,n){"use strict";var o=n("46c1"),i=n.n(o);i.a},"2e32":function(t,e,n){"use strict";n.r(e);var o=n("3cf3"),i=n.n(o);for(var r in o)"default"!==r&&function(t){n.d(e,t,function(){return o[t]})}(r);e["default"]=i.a},"3cf3":function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=function(){return n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},i={components:{uniLoadMore:o},data:function(){return{cateMaskState:0,headerPosition:"fixed",headerTop:"0px",loadingType:"more",filterIndex:0,cateId:0,priceOrder:0,cateList:[],goodsList:[],uid:0,salesOrder:"",source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:20,status:"more",keyword:"",userInfo:[]}},onLoad:function(t){console.log("页面",t),this.uid=t.uid,this.calcSize(),getApp().user_api(!0),this.my(),this.getProductList()},onShow:function(){},onPageScroll:function(t){t.scrollTop>=0?this.headerPosition="fixed":this.headerPosition="absolute"},onPullDownRefresh:function(){},onReachBottom:function(){},methods:{getProductList:function(){var e=this,n="/gzh/user_api/user_spread_list",o=e.uid?e.uid:"",i={uid:o,page:e.page,limit:e.limit};getApp().http(n,i,function(n){console.log("详情",n),t.hideLoading();var o=n.list;o.length>0?(console.log("总条数",o.length),e.goodsList=e.goodsList.concat(o),e.status=o.length>=e.limit?"more":"","more"==e.status&&(e.page+=1)):e.status=""})},my:function(){var t=this,e="/gzh/user_api/my";getApp().http(e,{},function(e){console.log("个人信息",e),t.userInfo=e})},extenUser:function(e){console.log("发鬼地方个",e),t.navigateTo({url:"/pages/user/user_extension?uid="+e.uid})},orderdet:function(e){t.navigateTo({url:"/pages/user/user_extension_det?uid="+e.uid})},intClickord:function(){t.navigateTo({url:"/pages/user/user_extension_list"})},indexUrl:function(t){var e=t.indexOf("http");return-1==e?this.webUrl+"/"+t:t},scroAddr:function(){var t=this;t.status&&t.getProductList()},calcSize:function(){var e=this;t.getSystemInfo({success:function(n){t.createSelectorQuery().select(".content");e.uid?e.scrollHeight=n.windowHeight:e.scrollHeight=n.windowHeight-44}})},navToDetailPage:function(e){console.log("详情",e);var n=e.id;t.navigateTo({url:"/pages/integral/index?id=".concat(n)})},stopPrevent:function(){}}};e.default=i}).call(this,n("543d")["default"])},"46c1":function(t,e,n){},6409:function(t,e,n){"use strict";(function(t){n("3c4b"),n("921b");o(n("66fd"));var e=o(n("9450"));function o(t){return t&&t.__esModule?t:{default:t}}t(e.default)}).call(this,n("543d")["createPage"])},9450:function(t,e,n){"use strict";n.r(e);var o=n("f907"),i=n("2e32");for(var r in i)"default"!==r&&function(t){n.d(e,t,function(){return i[t]})}(r);n("2dcb");var u,s=n("f0c5"),c=Object(s["a"])(i["default"],o["b"],o["c"],!1,null,null,null,!1,o["a"],u);e["default"]=c.exports},f907:function(t,e,n){"use strict";var o={"uni-load-more":()=>n.e("components/uni-load-more/uni-load-more").then(n.bind(null,"b4fc"))},i=function(){var t=this,e=t.$createElement,n=(t._self._c,t.__map(t.goodsList,function(e,n){var o=t.indexUrl(e.avatar);return{$orig:t.__get_orig(e),m0:o}}));t.$mp.data=Object.assign({},{$root:{l0:n}})},r=[];n.d(e,"b",function(){return i}),n.d(e,"c",function(){return r}),n.d(e,"a",function(){return o})}},[["6409","common/runtime","common/vendor"]]]);