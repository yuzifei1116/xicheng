(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-user-user_bill"],{"0a20":function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".uni-load-more[data-v-a38d3bf8]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;height:%?80?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.uni-load-more__text[data-v-a38d3bf8]{font-size:%?28?%;color:#999}.uni-load-more__img[data-v-a38d3bf8]{height:24px;width:24px;margin-right:10px}.uni-load-more__img>uni-view[data-v-a38d3bf8]{position:absolute}.uni-load-more__img>uni-view uni-view[data-v-a38d3bf8]{width:6px;height:2px;border-top-left-radius:1px;border-bottom-left-radius:1px;background:#999;position:absolute;opacity:.2;-webkit-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-a38d3bf8 1.56s ease infinite;animation:load-data-v-a38d3bf8 1.56s ease infinite}.uni-load-more__img>uni-view uni-view[data-v-a38d3bf8]:nth-child(1){-webkit-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.uni-load-more__img>uni-view uni-view[data-v-a38d3bf8]:nth-child(2){-webkit-transform:rotate(180deg);transform:rotate(180deg);top:11px;right:0}.uni-load-more__img>uni-view uni-view[data-v-a38d3bf8]:nth-child(3){-webkit-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.uni-load-more__img>uni-view uni-view[data-v-a38d3bf8]:nth-child(4){top:11px;left:0}.load1[data-v-a38d3bf8],\n.load2[data-v-a38d3bf8],\n.load3[data-v-a38d3bf8]{height:24px;width:24px}.load2[data-v-a38d3bf8]{-webkit-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-a38d3bf8]{-webkit-transform:rotate(60deg);transform:rotate(60deg)}.load1 uni-view[data-v-a38d3bf8]:nth-child(1){-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-a38d3bf8]:nth-child(1){-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-a38d3bf8]:nth-child(1){-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-a38d3bf8]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-a38d3bf8]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-a38d3bf8]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-a38d3bf8]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-a38d3bf8]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-a38d3bf8]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-a38d3bf8]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-a38d3bf8]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-a38d3bf8]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-a38d3bf8{0%{opacity:1}100%{opacity:.2}}",""])},"0ba1":function(t,e,a){var i=a("97b5");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("4f06").default;n("30da043a",i,!0,{sourceMap:!1,shadowMode:!1})},"1dc7":function(t,e,a){"use strict";var i,n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"uni-load-more"},[a("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:"loading"===t.status&&t.showIcon,expression:"status === 'loading' && showIcon"}],staticClass:"uni-load-more__img"},[a("v-uni-view",{staticClass:"load1"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1),a("v-uni-view",{staticClass:"load2"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1),a("v-uni-view",{staticClass:"load3"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1)],1),a("v-uni-text",{staticClass:"uni-load-more__text",style:{color:t.color}},[t._v(t._s("more"===t.status?t.contentText.contentdown:"loading"===t.status?t.contentText.contentrefresh:t.contentText.contentnomore))])],1)},o=[];a.d(e,"b",function(){return n}),a.d(e,"c",function(){return o}),a.d(e,"a",function(){return i})},"36d5":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={name:"uni-load-more",props:{status:{type:String,default:"more"},showIcon:{type:Boolean,default:!0},color:{type:String,default:"#777777"},contentText:{type:Object,default:function(){return{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}}},data:function(){return{}}};e.default=i},"45a0":function(t,e,a){"use strict";var i,n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"content"},[a("v-uni-view",{staticClass:"navbar"},[a("v-uni-view",{staticClass:"nav-item",class:{current:0==t.filterIndex},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.tabClick(0)}}},[t._v("全部")]),a("v-uni-view",{staticClass:"nav-item",class:{current:1==t.filterIndex},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.tabClick(1)}}},[t._v("消费")]),a("v-uni-view",{staticClass:"nav-item",class:{current:2==t.filterIndex},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.tabClick(2)}}},[t._v("充值")])],1),a("v-uni-scroll-view",{staticClass:"right-aside",style:{height:t.scrollHeight+"px"},attrs:{"scroll-with-animation":!0,"scroll-y":!0},on:{scrolltolower:function(e){arguments[0]=e=t.$handleEvent(e),t.scroAddr.apply(void 0,arguments)}}},t._l(t.goodsList,function(e,i){return a("v-uni-view",{key:i},[a("v-uni-view",{staticClass:"good-list-time"},[t._v(t._s(e.money))]),a("v-uni-view",{staticClass:"goods-list "},t._l(e.list,function(e,i){return a("v-uni-view",{key:i,staticClass:"goods-item "},[a("v-uni-view",{staticClass:"aces-space-between"},[a("v-uni-view",{staticClass:"text"},[a("v-uni-view",{staticClass:"condition line1"},[t._v(t._s(e.title))]),a("v-uni-view",{staticClass:"data acea-row "},[a("v-uni-view",{staticClass:"coutime"},[t._v(t._s(e.add_time))])],1)],1),e.pm?a("v-uni-view",[a("v-uni-view",{staticClass:"money1"},[t._v("+"),a("v-uni-text",{staticClass:"num"},[t._v(t._s(e.number))])],1)],1):a("v-uni-view",[a("v-uni-view",{staticClass:"money"},[t._v("-"),a("v-uni-text",{staticClass:"num"},[t._v(t._s(e.number))])],1)],1)],1)],1)}),1)],1)}),1)],1)},o=[];a.d(e,"b",function(){return n}),a.d(e,"c",function(){return o}),a.d(e,"a",function(){return i})},"59a4":function(t,e,a){"use strict";a.r(e);var i=a("b959"),n=a.n(i);for(var o in i)"default"!==o&&function(t){a.d(e,t,function(){return i[t]})}(o);e["default"]=n.a},"5d13":function(t,e,a){var i=a("0a20");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("4f06").default;n("c9e257c8",i,!0,{sourceMap:!1,shadowMode:!1})},6748:function(t,e,a){"use strict";a.r(e);var i=a("45a0"),n=a("59a4");for(var o in n)"default"!==o&&function(t){a.d(e,t,function(){return n[t]})}(o);a("90d9");var r,d=a("f0c5"),s=Object(d["a"])(n["default"],i["b"],i["c"],!1,null,"a0e25410",null,!1,i["a"],r);e["default"]=s.exports},"90d9":function(t,e,a){"use strict";var i=a("0ba1"),n=a.n(i);n.a},"97b5":function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,'@charset "UTF-8";\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */\n/* 商品列表 */.goods-list[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;padding:%?10?% %?30?%;background:#fff}.goods-item[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;width:100%;border-bottom:1px solid #f7f7f7;\r\n \t/* padding-bottom: 40upx; */padding:%?15?% %?0?%\r\n \t/* &:nth-child(2n+1){\r\n \t\tmargin-right: 4%;\r\n \t} */}.image-wra[data-v-a0e25410]{border-radius:3px;overflow:hidden;\r\n \t/* border:1px solid red; */padding-right:%?10?%}.couimg[data-v-a0e25410]{width:%?150?%;height:%?150?%;opacity:1\r\n \t/* border:1px solid red; */}.money[data-v-a0e25410]{\r\n\t/* border:1px solid red; */color:red;font-size:%?35?%}.money1[data-v-a0e25410]{color:#16ac57;font-size:%?35?%}.coutime[data-v-a0e25410]{font-size:%?20?%;color:#999}.good-list-time[data-v-a0e25410]{padding:%?20?%;background:#f5f5f5;width:100%;font-size:%?28?%;color:#666}.text[data-v-a0e25410]{\r\n\t/* border:1px solid red; */\r\n\t/* width:480upx; */padding-left:%?5?%}.line1[data-v-a0e25410]{font-size:%?30?%;margin-bottom:%?6?%}.coulq[data-v-a0e25410]{width:%?150?%;\r\n\t/* border:1px solid red; */text-align:center;margin-top:%?60?%}.couliq[data-v-a0e25410]{\r\n\t/* border:1px solid red; */background-color:#eb0f2b;color:#fff;font-size:%?30?%;padding:%?10?% %?0?%;width:%?130?%;margin-left:%?20?%;border-radius:%?50?%}.couliqs[data-v-a0e25410]{\r\n \t/* border:1px solid red; */background-color:#c3c3c3;color:#fff;font-size:%?30?%;padding:%?10?% %?0?%;width:%?130?%;margin-left:%?20?%;border-radius:%?50?%}.sx[data-v-a0e25410]{color:#797979;text-align:center;margin-top:%?50?%;font-size:%?35?%}uni-page-body[data-v-a0e25410], .content[data-v-a0e25410]{background:#f8f8f8}.content[data-v-a0e25410]{padding-top:%?82?%}.navbar[data-v-a0e25410]{position:fixed;left:0;top:var(--window-top);display:-webkit-box;display:-webkit-flex;display:flex;width:100%;height:%?80?%;background:#fff;box-shadow:0 %?2?% %?10?% rgba(0,0,0,.06);z-index:10}.navbar .nav-item[data-v-a0e25410]{-webkit-box-flex:1;-webkit-flex:1;flex:1;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:100%;font-size:%?30?%;color:#303133;position:relative}.navbar .nav-item.current[data-v-a0e25410]{color:#fa436a}.navbar .nav-item.current[data-v-a0e25410]:after{content:"";position:absolute;left:50%;bottom:0;-webkit-transform:translateX(-50%);transform:translateX(-50%);width:%?120?%;height:0;border-bottom:%?4?% solid #fa436a}.navbar .p-box[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column}.navbar .p-box .yticon[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;width:%?30?%;height:%?14?%;line-height:1;margin-left:%?4?%;font-size:%?26?%;color:#888}.navbar .p-box .yticon.active[data-v-a0e25410]{color:#fa436a}.navbar .p-box .xia[data-v-a0e25410]{-webkit-transform:scaleY(-1);transform:scaleY(-1)}.navbar .cate-item[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:100%;width:%?80?%;position:relative;font-size:%?44?%}.navbar .cate-item[data-v-a0e25410]:after{content:"";position:absolute;left:0;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);border-left:1px solid #ddd;width:0;height:%?36?%}\n/* 分类 */.cate-mask[data-v-a0e25410]{position:fixed;left:0;top:var(--window-top);bottom:0;width:100%;background:transparent;z-index:95;-webkit-transition:.3s;transition:.3s}.cate-mask .cate-content[data-v-a0e25410]{width:%?630?%;height:100%;background:#fff;float:right;-webkit-transform:translateX(100%);transform:translateX(100%);-webkit-transition:.3s;transition:.3s}.cate-mask.none[data-v-a0e25410]{display:none}.cate-mask.show[data-v-a0e25410]{background:rgba(0,0,0,.4)}.cate-mask.show .cate-content[data-v-a0e25410]{-webkit-transform:translateX(0);transform:translateX(0)}.cate-list[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;height:100%}.cate-list .cate-item[data-v-a0e25410]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:%?90?%;padding-left:%?30?%;font-size:%?28?%;color:#555;position:relative}.cate-list .two[data-v-a0e25410]{height:%?64?%;color:#303133;font-size:%?30?%;background:#f8f8f8}.cate-list .active[data-v-a0e25410]{color:#fa436a}body.?%PAGE?%[data-v-a0e25410]{background:#f8f8f8}',""])},b4fc:function(t,e,a){"use strict";a.r(e);var i=a("1dc7"),n=a("d3e9");for(var o in n)"default"!==o&&function(t){a.d(e,t,function(){return n[t]})}(o);a("ceb4");var r,d=a("f0c5"),s=Object(d["a"])(n["default"],i["b"],i["c"],!1,null,"a38d3bf8",null,!1,i["a"],r);e["default"]=s.exports},b959:function(t,e,a){"use strict";var i=a("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=i(a("b4fc")),o={components:{uniLoadMore:n.default},data:function(){return{cateMaskState:0,headerPosition:"fixed",headerTop:"0px",loadingType:"more",filterIndex:0,cateId:0,priceOrder:0,cateList:[],goodsList:[],sid:0,salesOrder:"",source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:5,status:"more",keyword:"",type:0}},onLoad:function(t){this.type=t.type,console.log("页面",t),getApp().user_api(!0),this.calcSize(),this.getProductList()},onShow:function(){},onPageScroll:function(t){t.scrollTop>=0?this.headerPosition="fixed":this.headerPosition="absolute"},onPullDownRefresh:function(){console.log("refresh"),setTimeout(function(){uni.stopPullDownRefresh()},1e3)},onReachBottom:function(){},methods:{getProductList:function(t){var e=this;e.filterIndex=e.type,console.log("排序",e.filterIndex);var a="/gzh/user_api/get_user_bill_list",i={type:e.type};getApp().http(a,i,function(t){console.log("详情",t),uni.hideLoading(),e.goodsList=t})},tabClick:function(t){var e=this;console.log("点击",t),e.filterIndex=t,this.priceOrder=2===t?1===this.priceOrder?2:1:0,uni.showLoading({title:"正在加载"}),0==e.filterIndex?(console.log("优惠券"),e.page=1,e.goodsList=[],e.type=0,e.getProductList()):1==e.filterIndex?(e.page=1,e.goodsList=[],e.type=1,e.getProductList(),console.log("我的")):2==e.filterIndex&&(e.page=1,e.goodsList=[],e.type=2,e.getProductList(),console.log("我的"))},userCoupon:function(t){var e=t.id,a="/gzh/coupons_api/user_get_coupon",i={couponId:e};getApp().http(a,i,function(t){console.log("领取",t),200==t.code?getApp().showTip(t.msg,"success"):getApp().showTip(t.msg,"none")},!0)},scroAddr:function(){var t=this;t.status},calcSize:function(){var t=this;uni.getSystemInfo({success:function(e){uni.createSelectorQuery().select(".content");t.scrollHeight=e.windowHeight-38}})}}};e.default=o},ceb4:function(t,e,a){"use strict";var i=a("5d13"),n=a.n(i);n.a},d3e9:function(t,e,a){"use strict";a.r(e);var i=a("36d5"),n=a.n(i);for(var o in i)"default"!==o&&function(t){a.d(e,t,function(){return i[t]})}(o);e["default"]=n.a}}]);