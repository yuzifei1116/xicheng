(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-address-address"],{"03e6":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'.uni-list-item[data-v-747afcd4]{font-size:%?32?%;position:relative;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between\n\t\t/* padding-left: 30rpx; */\n\t\t/* padding: 5rpx 0; */}.uni-list-item--disabled[data-v-747afcd4]{opacity:.3}.uni-list-item--hover[data-v-747afcd4]{background-color:#f1f1f1}.uni-list-item__container[data-v-747afcd4]{position:relative;\ndisplay:-webkit-box;display:-webkit-flex;display:flex;\n-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;\n\t\t/* padding: 24rpx 30rpx; */padding-left:0;-webkit-box-flex:1;-webkit-flex:1;flex:1;position:relative;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-box-align:center;-webkit-align-items:center;align-items:center\n}.uni-list-item--first[data-v-747afcd4]{\n\t\t/* border-top-width: 0px; */}\n.uni-list-item__container[data-v-747afcd4]:after{position:absolute;top:0;right:0;left:0;height:1px;content:""\n\t\t/* -webkit-transform: scaleY(.5);\n\t\ttransform: scaleY(.5); */\n\t\t/* background-color: #e5e5e5; */}.uni-list-item--first[data-v-747afcd4]:after{height:0}\n.uni-list-item__content[data-v-747afcd4]{\ndisplay:-webkit-box;display:-webkit-flex;display:flex;\n-webkit-box-flex:1;-webkit-flex:1;flex:1;overflow:hidden;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;color:#3b4144}.uni-list-item__content-title[data-v-747afcd4]{font-size:%?28?%;color:#3b4144;overflow:hidden}.uni-list-item__content-note[data-v-747afcd4]{margin-top:%?6?%;color:#999;font-size:%?24?%;overflow:hidden}.uni-list-item__extra[data-v-747afcd4]{\n\t\t/* width: 25%;\n */\ndisplay:-webkit-box;display:-webkit-flex;display:flex;\n-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-box-pack:end;-webkit-justify-content:flex-end;justify-content:flex-end;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.uni-list-item__icon[data-v-747afcd4]{margin-right:%?15?%;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center\n\t\t/* border:1px solid red; */}.uni-list-item__icon-img[data-v-747afcd4]{height:%?52?%;width:%?52?%}.uni-list-item__extra-text[data-v-747afcd4]{color:#999;font-size:%?24?%}',""]),t.exports=e},"062e":function(t,e,i){"use strict";var n,a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return t.text?i("v-uni-text",{staticClass:"uni-badge",class:t.inverted?"uni-badge--"+t.type+" uni-badge--"+t.size+" uni-badge--"+t.type+"-inverted":"uni-badge--"+t.type+" uni-badge--"+t.size,style:t.badgeStyle,on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onClick()}}},[t._v(t._s(t.text))]):t._e()},o=[];i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}))},"16c6":function(t,e,i){"use strict";var n=i("ee27");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("85c6")),o=n(i("66b7")),r={name:"UniListItem",components:{uniIcons:a.default,uniBadge:o.default},props:{title:{type:String,default:""},note:{type:String,default:""},disabled:{type:[Boolean,String],default:!1},showArrow:{type:[Boolean,String],default:!0},showBadge:{type:[Boolean,String],default:!1},showSwitch:{type:[Boolean,String],default:!1},switchChecked:{type:[Boolean,String],default:!1},badgeText:{type:String,default:""},badgeType:{type:String,default:"success"},rightText:{type:String,default:""},thumb:{type:String,default:""},showExtraIcon:{type:[Boolean,String],default:!1},extraIcon:{type:Object,default:function(){return{type:"contact",color:"#000000",size:20}}},mid:{type:String,default:""}},inject:["list"],data:function(){return{isFirstChild:!1}},mounted:function(){this.list.firstChildAppend||(this.list.firstChildAppend=!0,this.isFirstChild=!0)},methods:{onClick:function(t){console.log("点击了",t),this.$emit("myclick",t)},onSwitchChange:function(t){this.$emit("switchChange",t.detail)}}};e.default=r},"205a":function(t,e,i){"use strict";i.r(e);var n=i("7876"),a=i("3090");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("acae");var r,s=i("f0c5"),c=Object(s["a"])(a["default"],n["b"],n["c"],!1,null,"3fae5c23",null,!1,n["a"],r);e["default"]=c.exports},"21c0":function(t,e,i){"use strict";i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={name:"UniBadge",props:{type:{type:String,default:"default"},inverted:{type:Boolean,default:!1},text:{type:[String,Number],default:""},size:{type:String,default:"normal"}},data:function(){return{badgeStyle:""}},watch:{text:function(){this.setStyle()}},mounted:function(){this.setStyle()},methods:{setStyle:function(){this.badgeStyle="width: ".concat(8*String(this.text).length+12,"px")},onClick:function(){this.$emit("click")}}};e.default=n},2804:function(t,e,i){"use strict";i.r(e);var n=i("bb1b"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},3090:function(t,e,i){"use strict";i.r(e);var n=i("ecad"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},"3e3f":function(t,e,i){"use strict";var n=i("c45f"),a=i.n(n);a.a},"3f7a":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,".uni-load-more[data-v-76b7c960]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;height:%?80?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.uni-load-more__text[data-v-76b7c960]{font-size:%?28?%;color:#999}.uni-load-more__img[data-v-76b7c960]{height:24px;width:24px;margin-right:10px}.uni-load-more__img>uni-view[data-v-76b7c960]{position:absolute}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]{width:6px;height:2px;border-top-left-radius:1px;border-bottom-left-radius:1px;background:#999;position:absolute;opacity:.2;-webkit-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-76b7c960 1.56s ease infinite;animation:load-data-v-76b7c960 1.56s ease infinite}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(1){-webkit-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(2){-webkit-transform:rotate(180deg);transform:rotate(180deg);top:11px;right:0}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(3){-webkit-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(4){top:11px;left:0}.load1[data-v-76b7c960],\n.load2[data-v-76b7c960],\n.load3[data-v-76b7c960]{height:24px;width:24px}.load2[data-v-76b7c960]{-webkit-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-76b7c960]{-webkit-transform:rotate(60deg);transform:rotate(60deg)}.load1 uni-view[data-v-76b7c960]:nth-child(1){-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-76b7c960]:nth-child(1){-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-76b7c960]:nth-child(1){-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-76b7c960]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-76b7c960]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-76b7c960]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-76b7c960]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-76b7c960]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-76b7c960]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-76b7c960]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-76b7c960]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-76b7c960]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-76b7c960{0%{opacity:1}100%{opacity:.2}}",""]),t.exports=e},4041:function(t,e,i){"use strict";i.r(e);var n=i("21c0"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},"57a9":function(t,e,i){"use strict";var n={uniIcons:i("85c6").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-list-item",class:t.disabled?"uni-list-item--disabled":"",attrs:{"hover-class":t.disabled||t.showSwitch?"":"uni-list-item--hover"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onClick(t.mid)}}},[i("v-uni-view",{staticClass:"uni-list-item__container",class:{"uni-list-item--first":t.isFirstChild}},[t.thumb?i("v-uni-view",{staticClass:"uni-list-item__icon"},[i("v-uni-image",{staticClass:"uni-list-item__icon-img",attrs:{src:t.thumb}})],1):t.showExtraIcon?i("v-uni-view",{staticClass:"uni-list-item__icon"},[i("uni-icons",{staticClass:"uni-icon-wrapper",attrs:{color:t.extraIcon.color,size:t.extraIcon.size,type:t.extraIcon.type}})],1):t._e(),i("v-uni-view",{staticClass:"uni-list-item__content"},[t._t("default"),i("v-uni-text",{staticClass:"uni-list-item__content-title"},[t._v(t._s(t.title))]),t.note?i("v-uni-text",{staticClass:"uni-list-item__content-note"},[t._v(t._s(t.note))]):t._e()],2)],1)],1)},o=[];i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}))},"58f6":function(t,e,i){"use strict";i.r(e);var n=i("a6bf"),a=i("2804");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("3e3f");var r,s=i("f0c5"),c=Object(s["a"])(a["default"],n["b"],n["c"],!1,null,"76b7c960",null,!1,n["a"],r);e["default"]=c.exports},"5e3f":function(t,e,i){"use strict";i.r(e);var n=i("16c6"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},"66b7":function(t,e,i){"use strict";i.r(e);var n=i("062e"),a=i("4041");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("f284");var r,s=i("f0c5"),c=Object(s["a"])(a["default"],n["b"],n["c"],!1,null,"342127b6",null,!1,n["a"],r);e["default"]=c.exports},7432:function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,".uni-badge[data-v-342127b6]{\ndisplay:-webkit-box;display:-webkit-flex;display:flex;\n-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;height:20px;line-height:20px;color:#333;border-radius:100px;background-color:#f1f1f1;background-color:initial;text-align:center;font-family:Helvetica Neue,Helvetica,sans-serif;font-size:12px;padding:0 6px}.uni-badge--inverted[data-v-342127b6]{padding:0 5px 0 0;color:#f1f1f1}.uni-badge--default[data-v-342127b6]{color:#333;background-color:#f1f1f1}.uni-badge--default-inverted[data-v-342127b6]{color:#999;background-color:initial}.uni-badge--primary[data-v-342127b6]{color:#fff;background-color:#007aff}.uni-badge--primary-inverted[data-v-342127b6]{color:#007aff;background-color:initial}.uni-badge--success[data-v-342127b6]{color:#fff;background-color:#4cd964}.uni-badge--success-inverted[data-v-342127b6]{color:#4cd964;background-color:initial}.uni-badge--warning[data-v-342127b6]{color:#fff;background-color:#f0ad4e}.uni-badge--warning-inverted[data-v-342127b6]{color:#f0ad4e;background-color:initial}.uni-badge--error[data-v-342127b6]{color:#fff;background-color:#dd524d}.uni-badge--error-inverted[data-v-342127b6]{color:#dd524d;background-color:initial}.uni-badge--small[data-v-342127b6]{-webkit-transform:scale(.8);transform:scale(.8);-webkit-transform-origin:center center;transform-origin:center center}",""]),t.exports=e},7876:function(t,e,i){"use strict";var n={uniLoadMore:i("58f6").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"content b-t"},[i("v-uni-scroll-view",{staticClass:"right-aside",style:{height:t.scrollHeight+"px"},attrs:{"scroll-with-animation":!0,"scroll-y":!0},on:{scrolltolower:function(e){arguments[0]=e=t.$handleEvent(e),t.scroAddr.apply(void 0,arguments)}}},[t._l(t.addressList,(function(e,n){return i("v-uni-view",{key:n,staticClass:"list b-b"},[i("v-uni-view",{staticClass:"wrapper",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.checkAddress(n)}}},[i("v-uni-view",{staticClass:"address-box"},[e.is_default?i("v-uni-text",{staticClass:"tag"},[t._v("默认")]):t._e(),i("v-uni-text",{staticClass:"address"},[t._v(t._s(e.province)+t._s(e.city)+t._s(e.district)+" "+t._s(e.detail))])],1),i("v-uni-view",{staticClass:"u-box"},[i("v-uni-text",{staticClass:"name"},[t._v(t._s(e.real_name))]),i("v-uni-text",{staticClass:"mobile"},[t._v(t._s(e.phone))])],1)],1),i("v-uni-view",{staticClass:"aces-space-between"},[i("v-uni-view",{staticClass:"yticon acea-row",on:{click:function(i){i.stopPropagation(),arguments[0]=i=t.$handleEvent(i),t.addAddress("edit",e.id)}}},[i("v-uni-text",{staticClass:"icon-bianji ed-icon"}),i("v-uni-text",{staticClass:"ed"},[t._v("编辑")])],1),i("v-uni-view",{staticClass:"iconfg"},[t._v("|")]),i("v-uni-view",{staticClass:"yticon acea-row",on:{click:function(i){i.stopPropagation(),arguments[0]=i=t.$handleEvent(i),t.delAddress(e.id)}}},[i("v-uni-text",{staticClass:"ed-icon icon-iconfontshanchu1"}),i("v-uni-text",{staticClass:"ed"},[t._v("删除")])],1)],1)],1)})),i("uni-load-more",{attrs:{status:t.status,"icon-size":16,"content-text":t.contentText}})],2),i("v-uni-button",{staticClass:"add-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.addAddress("add")}}},[t._v("新增地址")])],1)},o=[];i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}))},"826a":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\r\n/* 页面左右间距 */\r\n/* 文字尺寸 */\r\n/*文字颜色*/\r\n/* 边框颜色 */\r\n/* 图片加载中颜色 */\r\n/* 行为相关颜色 */.content[data-v-3fae5c23]{position:relative}.list[data-v-3fae5c23]{\r\n\t/* display: flex; */\r\n\t/* align-items: center; */padding:%?20?% %?30?% %?0?% %?20?%;background:#fff;position:relative\r\n\t/* border:1px solid red; */}.wrapper[data-v-3fae5c23]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-flex:1;-webkit-flex:1;flex:1;border-bottom:1px solid #f7f7f7;padding:%?0?% %?10?% %?15?% %?10?%}.address-box[data-v-3fae5c23]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.u-box .name[data-v-3fae5c23]{margin-right:%?30?%}.yticon[data-v-3fae5c23]{\r\n\t/* border:1px solid red; */\r\n\t/* width:50%; */\r\n\t/* font-size:3upx; */font-size:%?30?%;color:#909399}.ed[data-v-3fae5c23]{line-height:%?75?%;margin-left:%?10?%}.iconfg[data-v-3fae5c23]{font-size:%?28?%;color:#f5f5f5;line-height:%?75?%}uni-page-body[data-v-3fae5c23]{padding-bottom:%?120?%}.tag[data-v-3fae5c23]{font-size:%?24?%;color:#fa436a;margin-right:%?10?%;background:#fffafb;border:1px solid #ffb4c7;border-radius:%?4?%;padding:%?4?% %?10?%;line-height:1}.address[data-v-3fae5c23]{font-size:%?30?%;color:#303133}.u-box[data-v-3fae5c23]{font-size:%?28?%;color:#909399;margin-top:%?16?%}.ed-icon[data-v-3fae5c23]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:%?80?%;font-size:%?40?%;color:#909399;padding-left:%?30?%}.add-btn[data-v-3fae5c23]{position:fixed;left:%?30?%;right:%?30?%;bottom:%?16?%;z-index:95;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;width:%?690?%;height:%?80?%;font-size:%?32?%;color:#fff;background-color:#fa436a;border-radius:%?10?%;box-shadow:1px 2px 5px rgba(219,63,96,.4)}',""]),t.exports=e},a6bf:function(t,e,i){"use strict";var n,a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-load-more"},[i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:"loading"===t.status&&t.showIcon,expression:"status === 'loading' && showIcon"}],staticClass:"uni-load-more__img"},[i("v-uni-view",{staticClass:"load1"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1),i("v-uni-view",{staticClass:"load2"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1),i("v-uni-view",{staticClass:"load3"},[i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}}),i("v-uni-view",{style:{background:t.color}})],1)],1),i("v-uni-text",{staticClass:"uni-load-more__text",style:{color:t.color}},[t._v(t._s("more"===t.status?t.contentText.contentdown:"loading"===t.status?t.contentText.contentrefresh:t.contentText.contentnomore))])],1)},o=[];i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}))},acae:function(t,e,i){"use strict";var n=i("bcbf"),a=i.n(n);a.a},bb1b:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={name:"uni-load-more",props:{status:{type:String,default:"more"},showIcon:{type:Boolean,default:!0},color:{type:String,default:"#777777"},contentText:{type:Object,default:function(){return{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}}},data:function(){return{}}};e.default=n},bcbf:function(t,e,i){var n=i("826a");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("21833a33",n,!0,{sourceMap:!1,shadowMode:!1})},c384:function(t,e,i){var n=i("7432");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("91992418",n,!0,{sourceMap:!1,shadowMode:!1})},c45f:function(t,e,i){var n=i("3f7a");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("4cd2be94",n,!0,{sourceMap:!1,shadowMode:!1})},c65f:function(t,e,i){var n=i("03e6");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("4f84ab54",n,!0,{sourceMap:!1,shadowMode:!1})},d8d4:function(t,e,i){"use strict";i.r(e);var n=i("57a9"),a=i("5e3f");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("e776");var r,s=i("f0c5"),c=Object(s["a"])(a["default"],n["b"],n["c"],!1,null,"747afcd4",null,!1,n["a"],r);e["default"]=c.exports},e776:function(t,e,i){"use strict";var n=i("c65f"),a=i.n(n);a.a},ecad:function(t,e,i){"use strict";var n=i("ee27");i("99af"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("d8d4")),o=(n(i("58f6")),{components:{uniListItem:a.default},data:function(){return{source:0,addressList:[],scrollHeight:100,tabScrollTop:0,contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"我是有底线的"},page:1,limit:20,status:"more",cartId:0,id:0,num:0,adr:0}},onShow:function(){this.addressList=[],this.userAddressIist()},onLoad:function(t){console.log(t.source),this.source=t.source,this.cartId=t.cartId,this.id=t.id,this.num=t.num,this.adr=t.adr,getApp().user_api(!0),this.calcSize()},methods:{userAddressIist:function(){var t=this,e="/gzh/user_api/user_address_list",i={page:t.page,limit:t.limit};getApp().http(e,i,(function(e){console.log(e);var i=e;i.length>0?(console.log(i.length),t.addressList=t.addressList.concat(i),t.status=i.length>=t.limit?"more":"","more"==t.status&&(t.page+=1)):t.status=""}))},delAddress:function(t){var e=this;uni.showModal({title:"删除",content:"确定删除吗？",success:function(i){if(1==i.confirm){console.log("确定");var n="/gzh/user_api/remove_user_address",a={addressId:t};getApp().http(n,a,(function(t){console.log(t),console.log(t.code),console.log("------------00000000000000---------"),200==t.code?(e.addressList=[],e.userAddressIist(),getApp().showTip(t.msg,"success")):getApp().showTip(t.msg)}),!0)}}}),console.log(t),console.log("-----------------------------")},scroAddr:function(){var t=this;t.status&&t.userAddressIist()},calcSize:function(){var t=this;uni.getSystemInfo({success:function(e){var i=uni.createSelectorQuery().select(".content");t.scrollHeight=e.windowHeight-55,console.log(i),console.log("啊啊啊啊",e)}})},checkAddress:function(t){console.log("sdfsdfsdfsd",t),2==this.adr?uni.navigateTo({url:"/pages/integral/integralOrder?id="+this.id+"&num="+this.num+"&address="+t}):3==this.adr&&uni.navigateTo({url:"/pages/order/createOrder?cartId="+this.cartId+"&address="+t})},addAddress:function(t,e){uni.navigateTo({url:"/pages/address/addressManage?type=".concat(t,"&data=").concat(JSON.stringify(e))})},refreshList:function(t,e){this.addressList.unshift(t),console.log(t,e)}}});e.default=o},f284:function(t,e,i){"use strict";var n=i("c384"),a=i.n(n);a.a}}]);