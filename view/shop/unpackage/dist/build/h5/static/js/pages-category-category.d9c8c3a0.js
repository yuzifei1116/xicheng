(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-category-category"],{"0bf3":function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,'@charset "UTF-8";\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */.aa[data-v-463512c2]{\r\n\t/* background:red; */display:none}.item-img[data-v-463512c2]{width:100%;height:%?200?%;margin-top:%?10?%;margin-bottom:%?10?%}.right-aside[data-v-463512c2]{background:#fff}.content[data-v-463512c2]{border-top:%?1?% solid #fafafa}.s-list[data-v-463512c2]{\r\n\t/* border:1px solid red; */padding-right:%?15?%}.list-img[data-v-463512c2]{width:100%}uni-page-body[data-v-463512c2],\n.content[data-v-463512c2]{height:100%;background-color:#f8f8f8}.content[data-v-463512c2]{display:-webkit-box;display:-webkit-flex;display:flex}.left-aside[data-v-463512c2]{-webkit-flex-shrink:0;flex-shrink:0;width:%?200?%;height:100%;background-color:#fff}.f-item[data-v-463512c2]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;width:100%;height:%?100?%;font-size:%?28?%;color:#606266;position:relative}.f-item.active[data-v-463512c2]{color:#fa436a;background:#f8f8f8}.f-item.active[data-v-463512c2]:before{content:"";position:absolute;left:0;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);height:%?36?%;width:%?8?%;background-color:#fa436a;border-radius:0 4px 4px 0;opacity:.8}.right-aside[data-v-463512c2]{\n  /* border:1px solid red; */-webkit-box-flex:1;-webkit-flex:1;flex:1;overflow:hidden;padding-left:%?20?%}.s-item[data-v-463512c2]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:%?70?%;padding-top:%?8?%;font-size:%?28?%;\n  /* border:1px solid red; */color:#303133}.t-list[data-v-463512c2]{\n  /* display: flex;\n\tflex-wrap: wrap; */width:%?170?%;\n  /* width:200upx; */background:#fff;padding-top:%?12?%\n  /* border:1px solid red; */}.t-list[data-v-463512c2]:after{content:"";-webkit-box-flex:99;-webkit-flex:99;flex:99;height:0}.t-item[data-v-463512c2]{-webkit-flex-shrink:0;flex-shrink:0;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;width:%?176?%;font-size:%?26?%;color:#666;padding-bottom:%?20?%}.t-item uni-image[data-v-463512c2]{width:%?130?%;height:%?130?%;border-radius:%?65?%;margin-bottom:%?5?%}body.?%PAGE?%[data-v-463512c2]{background-color:#f8f8f8}',""])},"2b07":function(t,e,i){"use strict";i.r(e);var a=i("3512"),n=i("fdd0");for(var o in n)"default"!==o&&function(t){i.d(e,t,function(){return n[t]})}(o);i("a89f");var c,r=i("f0c5"),l=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"463512c2",null,!1,a["a"],c);e["default"]=l.exports},3512:function(t,e,i){"use strict";var a,n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"content"},[i("v-uni-scroll-view",{staticClass:"left-aside",attrs:{"scroll-y":!0}},t._l(t.getData,function(e){return i("v-uni-view",{key:e.id,staticClass:"f-item b-b",class:{active:e.id===t.currentId},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.tabtap(e)}}},[t._v(t._s(e.cate_name))])}),1),i("v-uni-scroll-view",{staticClass:"right-aside",style:{height:t.scrollHeight+"px"},attrs:{"scroll-with-animation":!0,"scroll-y":!0,"scroll-top":t.tabScrollTop},on:{scroll:function(e){arguments[0]=e=t.$handleEvent(e),t.asideScroll.apply(void 0,arguments)}}},[i("v-uni-view",{staticClass:"s-list acea-row"},[t.itemPic?i("v-uni-view",{staticClass:"list-img"},[i("v-uni-image",{staticClass:"item-img",attrs:{src:t.indexUrl(t.itemPic)}})],1):t._e(),t._l(t.slist,function(e){return i("v-uni-view",{key:e.id,staticClass:"t-list"},[i("v-uni-view",{staticClass:"t-item",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.navToList(e)}}},[i("v-uni-image",{attrs:{src:t.indexUrl(e.pic)}}),i("v-uni-text",[t._v(t._s(e.cate_name))])],1)],1)})],2)],1)],1)},o=[];i.d(e,"b",function(){return n}),i.d(e,"c",function(){return o}),i.d(e,"a",function(){return a})},"89a9":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={data:function(){return{sizeCalcState:!1,tabScrollTop:0,currentId:0,flist:[],slist:[],tlist:[],itemPic:"/public/uploads/attach/2020/01/06/5e130e7217a21.jpg",getData:[{}],scrollHeight:100}},onLoad:function(){this.getCategoryOneTwo(),getApp().user_api(!0)},onReady:function(){},methods:{getCategoryOneTwo:function(){this.calcSize();var t=this;t.slist=t.getData[0].child,t.currentId=t.getData[0].id,t.itemPic=t.getData[0].pic;var e="/gzh/store_api/get_category_one_two";getApp().http(e,{},function(e){t.getData=e,t.slist=e[0].child,t.currentId=e[0].id,t.itemPic=e[0].pic,console.log(t.currentId),console.log("==========")})},tabtap:function(t){this.currentId=t.id,this.slist=t.child,this.itemPic=t.pic,console.log("aaaaaaaa1",this.itemPic)},indexUrl:function(t){var e=t.indexOf("http");return console.log("图谱爱",e),-1==e?this.webUrl+t:t},asideScroll:function(t){var e=t.detail.scrollTop;this.slist.filter(function(t){return t.top<=e}).reverse()},calcSize:function(){var t=this;uni.getSystemInfo({success:function(e){var i=uni.createSelectorQuery().select(".content");t.scrollHeight=e.windowHeight,console.log(i),console.log("战速",e.windowHeight),console.log("啊啊啊啊",e)}})},navToList:function(t){console.log("跳转",t),uni.navigateTo({url:"/pages/product/list?sid="+t.id})}}};e.default=a},a89f:function(t,e,i){"use strict";var a=i("d2ee"),n=i.n(a);n.a},d2ee:function(t,e,i){var a=i("0bf3");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("aad60738",a,!0,{sourceMap:!1,shadowMode:!1})},fdd0:function(t,e,i){"use strict";i.r(e);var a=i("89a9"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,function(){return a[t]})}(o);e["default"]=n.a}}]);