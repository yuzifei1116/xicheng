(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-user-user_extension_det"],{"016a":function(t,e,i){var a=i("d54c");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var o=i("4f06").default;o("7f97b7bc",a,!0,{sourceMap:!1,shadowMode:!1})},"1eca":function(t,e,i){"use strict";var a=i("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=a(i("1df2")),n=(a(i("0740")),a(i("b4fc"))),d={components:{uniLoadMore:n.default,empty:o.default},data:function(){return{tabCurrentIndex:0,navList:[],orderList:[],source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:20,status:"more",uid:0}},onLoad:function(t){this.uid=t.uid,this.calcSize(),this.page=1,this.limit=20,this.orderList=[],this.getUserOrderList()},onShow:function(){},methods:{getUserOrderList:function(){var t=this,e="/gzh/user_api/user_spread_order",i={uid:t.uid,page:t.page,limit:t.limit};getApp().http(e,i,function(e){var i=e;i.length>0?(t.status=i.length<=t.limit?"more":"","more"==t.status&&(t.page+=1,console.log(t.page),console.log("=================444444444444")),t.orderList=t.orderList.concat(i),console.log("都发给对方",t.orderList)):t.status="",console.log("水电费水电费",t.orderList,i)})},tabClick:function(t,e){var i=this;i.tabCurrentIndex=t;var a=e.state;i.orderList=[],i.page=1,i.getUserOrderList(a),console.log("点击",this.tabCurrentIndex,a)},indexUrl:function(t){var e=t.indexOf("http");return-1==e?this.webUrl+"/"+t:t},scroAddr:function(){var t=this;t.status&&t.getUserOrderList()},calcSize:function(){var t=this;uni.getSystemInfo({success:function(e){var i=uni.createSelectorQuery().select(".content");t.scrollHeight=e.windowHeight,console.log(i),console.log("啊啊啊啊",e)}})},orderdet:function(t){uni.navigateTo({url:"/pages/integral/intOrderDetail?id="+t.id})},deleteOrder:function(t){var e=this;uni.showLoading({title:"请稍后"}),setTimeout(function(){e.navList[e.tabCurrentIndex].orderList.splice(t,1),uni.hideLoading()},600)}}};e.default=d},7450:function(t,e,i){"use strict";i.r(e);var a=i("1eca"),o=i.n(a);for(var n in a)"default"!==n&&function(t){i.d(e,t,function(){return a[t]})}(n);e["default"]=o.a},c335:function(t,e,i){"use strict";var a=i("016a"),o=i.n(a);o.a},c4a9:function(t,e,i){"use strict";var a={"uni-load-more":i("b4fc").default},o=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"content"},[i("v-uni-scroll-view",{staticClass:"list-scroll-content",style:{height:t.scrollHeight+"px"},attrs:{"scroll-y":!0,"scroll-with-animation":!0},on:{scrolltolower:function(e){arguments[0]=e=t.$handleEvent(e),t.scroAddr.apply(void 0,arguments)}}},[t._l(t.orderList,function(e,a){return i("v-uni-view",{key:a,staticClass:"order-item"},[i("v-uni-view",{staticClass:"i-top b-b"},[i("v-uni-text",{staticClass:"time"},[t._v(t._s(e._add_time))]),i("v-uni-text",{staticClass:"state",style:{color:e.stateTipColor}},[t._v(t._s(e._status._title))]),9===e.state?i("v-uni-text",{staticClass:"del-btn yticon icon-iconfontshanchu1",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.deleteOrder(a)}}}):t._e()],1),t._l(e.cartInfo,function(a,o){return i("v-uni-view",{key:o,staticClass:"goods-box-single",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.orderdet(e)}}},[i("v-uni-image",{staticClass:"goods-img",attrs:{src:t.indexUrl(a.productInfo.image),mode:"aspectFill"}}),i("v-uni-view",{staticClass:"right"},[i("v-uni-text",{staticClass:"title clamp"},[t._v(t._s(a.productInfo.store_name))]),i("v-uni-text",{staticClass:"attr-box"},[t._v(t._s(a.productInfo.attrInfo?a.productInfo.attrInfo.suk:"")+"  x "+t._s(a.cart_num))]),i("v-uni-text",{staticClass:"price"},[t._v(t._s(a.truePrice))])],1)],1)}),i("v-uni-view",{staticClass:"price-box"},[t._v("共"),i("v-uni-text",{staticClass:"num"},[t._v(t._s(e.cartInfo.length))]),t._v("件商品 总金额"),i("v-uni-text",{staticClass:"price"},[t._v(t._s(e.total_price))])],1)],2)}),i("uni-load-more",{attrs:{status:t.status,"icon-size":16,"content-text":t.contentText}})],2)],1)},n=[];i.d(e,"b",function(){return o}),i.d(e,"c",function(){return n}),i.d(e,"a",function(){return a})},d54c:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,'@charset "UTF-8";\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */uni-page-body[data-v-de5184b4], .content[data-v-de5184b4]{background:#f8f8f8;height:100%}.swiper-box[data-v-de5184b4]{height:calc(100% - 40px)}.list-scroll-content[data-v-de5184b4]{height:100%}.navbar[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;height:40px;padding:0 5px;background:#fff;box-shadow:0 1px 5px rgba(0,0,0,.06);position:relative;z-index:10}.navbar .nav-item[data-v-de5184b4]{-webkit-box-flex:1;-webkit-flex:1;flex:1;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:100%;font-size:15px;color:#303133;position:relative}.navbar .nav-item.current[data-v-de5184b4]{color:#fa436a}.navbar .nav-item.current[data-v-de5184b4]:after{content:"";position:absolute;left:50%;bottom:0;-webkit-transform:translateX(-50%);transform:translateX(-50%);width:44px;height:0;border-bottom:2px solid #fa436a}.uni-swiper-item[data-v-de5184b4]{height:auto}.order-item[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;padding-left:%?30?%;background:#fff;margin-top:%?16?%\n  /* 多条商品 */\n  /* 单条商品 */}.order-item .i-top[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:%?80?%;padding-right:%?30?%;font-size:%?28?%;color:#303133;position:relative}.order-item .i-top .time[data-v-de5184b4]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.order-item .i-top .state[data-v-de5184b4]{color:#fa436a}.order-item .i-top .del-btn[data-v-de5184b4]{padding:%?10?% 0 %?10?% %?36?%;font-size:%?32?%;color:#909399;position:relative}.order-item .i-top .del-btn[data-v-de5184b4]:after{content:"";width:0;height:%?30?%;border-left:1px solid #dcdfe6;position:absolute;left:%?20?%;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.order-item .goods-box[data-v-de5184b4]{height:%?160?%;padding:%?20?% 0;white-space:nowrap}.order-item .goods-box .goods-item[data-v-de5184b4]{width:%?120?%;height:%?120?%;display:inline-block;margin-right:%?24?%}.order-item .goods-box .goods-img[data-v-de5184b4]{display:block;width:100%;height:100%}.order-item .goods-box-single[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;padding:%?20?% 0}.order-item .goods-box-single .goods-img[data-v-de5184b4]{display:block;width:%?120?%;height:%?120?%}.order-item .goods-box-single .right[data-v-de5184b4]{-webkit-box-flex:1;-webkit-flex:1;flex:1;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;padding:0 %?30?% 0 %?24?%;overflow:hidden}.order-item .goods-box-single .right .title[data-v-de5184b4]{font-size:%?30?%;color:#303133;line-height:1;margin-top:%?10?%;margin-bottom:%?10?%}.order-item .goods-box-single .right .attr-box[data-v-de5184b4]{font-size:%?26?%;color:#909399;padding:%?10?% %?12?%}.order-item .goods-box-single .right .price[data-v-de5184b4]{font-size:%?30?%;color:#fa436a}.order-item .goods-box-single .right .price[data-v-de5184b4]:before{content:"";font-size:%?24?%}.order-item .price-box[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:end;-webkit-justify-content:flex-end;justify-content:flex-end;-webkit-box-align:baseline;-webkit-align-items:baseline;align-items:baseline;padding:%?20?% %?30?%;font-size:%?26?%;color:#909399}.order-item .price-box .num[data-v-de5184b4]{margin:0 %?8?%;color:#303133}.order-item .price-box .price[data-v-de5184b4]{font-size:%?32?%;color:#303133}.order-item .price-box .price[data-v-de5184b4]:before{content:"";font-size:%?24?%;margin:0 %?2?% 0 %?8?%}.order-item .action-box[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:end;-webkit-justify-content:flex-end;justify-content:flex-end;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:%?100?%;position:relative;padding-right:%?30?%}.order-item .action-btn[data-v-de5184b4]{width:%?160?%;height:%?60?%;margin:0;margin-left:%?24?%;padding:0;text-align:center;line-height:%?60?%;font-size:%?26?%;color:#303133;background:#fff;border-radius:100px}.order-item .action-btn[data-v-de5184b4]:after{border-radius:100px}.order-item .action-btn.recom[data-v-de5184b4]{background:#fff9f9;color:#fa436a}.order-item .action-btn.recom[data-v-de5184b4]:after{border-color:#f7bcc8}\n/* load-more */.uni-load-more[data-v-de5184b4]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;height:%?80?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.uni-load-more__text[data-v-de5184b4]{font-size:%?28?%;color:#999}.uni-load-more__img[data-v-de5184b4]{height:24px;width:24px;margin-right:10px}.uni-load-more__img > uni-view[data-v-de5184b4]{position:absolute}.uni-load-more__img > uni-view uni-view[data-v-de5184b4]{width:6px;height:2px;border-top-left-radius:1px;border-bottom-left-radius:1px;background:#999;position:absolute;opacity:.2;-webkit-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-de5184b4 1.56s ease infinite;animation:load-data-v-de5184b4 1.56s ease infinite}.uni-load-more__img > uni-view uni-view[data-v-de5184b4]:nth-child(1){-webkit-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.uni-load-more__img > uni-view uni-view[data-v-de5184b4]:nth-child(2){-webkit-transform:rotate(180deg);transform:rotate(180deg);top:11px;right:0}.uni-load-more__img > uni-view uni-view[data-v-de5184b4]:nth-child(3){-webkit-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.uni-load-more__img > uni-view uni-view[data-v-de5184b4]:nth-child(4){top:11px;left:0}.load1[data-v-de5184b4],\n.load2[data-v-de5184b4],\n.load3[data-v-de5184b4]{height:24px;width:24px}.load2[data-v-de5184b4]{-webkit-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-de5184b4]{-webkit-transform:rotate(60deg);transform:rotate(60deg)}.load1 uni-view[data-v-de5184b4]:nth-child(1){-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-de5184b4]:nth-child(1){-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-de5184b4]:nth-child(1){-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-de5184b4]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-de5184b4]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-de5184b4]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-de5184b4]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-de5184b4]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-de5184b4]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-de5184b4]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-de5184b4]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-de5184b4]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-de5184b4{0%{opacity:1}100%{opacity:.2}}body.?%PAGE?%[data-v-de5184b4]{background:#f8f8f8}',""])},dfad:function(t,e,i){"use strict";i.r(e);var a=i("c4a9"),o=i("7450");for(var n in o)"default"!==n&&function(t){i.d(e,t,function(){return o[t]})}(n);i("c335");var d,r=i("f0c5"),s=Object(r["a"])(o["default"],a["b"],a["c"],!1,null,"de5184b4",null,!1,a["a"],d);e["default"]=s.exports}}]);