(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-address-addressManage"],{"0e0b":function(t,e,a){"use strict";var s=a("ee27");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i=s(a("8143")),n={data:function(){return{addressData:{real_name:"",phone:"",province:"省",city:"市",district:"区",detail:""},is_default:0,cityPickerValueDefault:[0,0,0],pickerText:"省-市-区",picker:null,adrid:0,opType:""}},components:{simpleAddress:i.default},onLoad:function(t){console.log(t),console.log("optionoptionoptionoptionoptionoption");var e=this;e.opType=t.type,getApp().user_api(!0);var a="新增收货地址";"edit"===t.type&&(a="编辑收货地址",e.adrid=t.data,e.getUserAddress()),uni.setNavigationBarTitle({title:a})},methods:{openAddres:function(){this.$refs.simpleAddress.open()},onConfirm:function(t){console.log(t),this.pickerText=t.label,this.addressData.province=t.labelArr[0],this.addressData.city=t.labelArr[1],this.addressData.district=t.labelArr[2]},switchChange:function(t){this.addressData.is_default=1},getUserAddress:function(){var t=this,e="/gzh/user_api/get_user_address",a={addressId:t.adrid};console.log(t.addressData),console.log("============"),getApp().http(e,a,(function(e){console.log("修改",e),t.addressData=e}))},confirm:function(){var t=this,e=t.addressData;if(e.real_name)if(/(^1[3|4|5|7|8][0-9]{9}$)/.test(e.phone))if(e.province)if(e.detail){getApp().showTip(),console.log("地址",e);var a="/gzh/user_api/edit_user_address",s=e;getApp().http(a,s,(function(t){200==t.code?(console.log("sdfsdfsdfsdsdf"),getApp().showTip(t.msg,"success"),uni.navigateBack(1500)):getApp().showTip(t.msg)}),!0,"POST")}else this.$api.msg("请填写门牌号信息");else this.$api.msg("请输入地址");else this.$api.msg("请输入正确的手机号码");else this.$api.msg("请填写收货人姓名")},chooseLocation:function(){var t=this;uni.chooseLocation({success:function(e){t.addressData.addressName=e.name,t.addressData.address=e.name}})}}};e.default=n},"1f24":function(t,e,a){var s=a("24fb");e=s(!1),e.push([t.i,'@charset "UTF-8";\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */.btns[data-v-08ff1bbc]{font-size:%?30?%;width:100%}uni-page-body[data-v-08ff1bbc]{background:#f8f8f8;padding-top:%?16?%}.row[data-v-08ff1bbc]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;position:relative;padding:0 %?30?%;height:%?110?%;background:#fff}.row .tit[data-v-08ff1bbc]{-webkit-flex-shrink:0;flex-shrink:0;width:%?120?%;font-size:%?30?%;color:#303133}.row .input[data-v-08ff1bbc]{-webkit-box-flex:1;-webkit-flex:1;flex:1;font-size:%?30?%;color:#303133}.row .icon-shouhuodizhi[data-v-08ff1bbc]{font-size:%?36?%;color:#909399}.default-row[data-v-08ff1bbc]{margin-top:%?16?%}.default-row .tit[data-v-08ff1bbc]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.default-row uni-switch[data-v-08ff1bbc]{-webkit-transform:translateX(%?16?%) scale(.9);transform:translateX(%?16?%) scale(.9)}.add-btn[data-v-08ff1bbc]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;width:%?690?%;height:%?80?%;margin:%?60?% auto;font-size:%?32?%;color:#fff;background-color:#fa436a;border-radius:%?10?%;box-shadow:1px 2px 5px rgba(219,63,96,.4)}body.?%PAGE?%[data-v-08ff1bbc]{background:#f8f8f8}',""]),t.exports=e},7710:function(t,e,a){"use strict";var s={simpleAddress:a("8143").default},i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"content"},[a("v-uni-view",{staticClass:"row b-b"},[a("v-uni-text",{staticClass:"tit"},[t._v("姓名")]),a("v-uni-input",{staticClass:"input",attrs:{type:"text",placeholder:"收货人姓名","placeholder-class":"placeholder"},model:{value:t.addressData.real_name,callback:function(e){t.$set(t.addressData,"real_name",e)},expression:"addressData.real_name"}})],1),a("v-uni-view",{staticClass:"row b-b"},[a("v-uni-text",{staticClass:"tit"},[t._v("手机号")]),a("v-uni-input",{staticClass:"input",attrs:{type:"number",placeholder:"收货人手机号码","placeholder-class":"placeholder"},model:{value:t.addressData.phone,callback:function(e){t.$set(t.addressData,"phone",e)},expression:"addressData.phone"}})],1),a("v-uni-view",{staticClass:"row b-b"},[a("v-uni-text",{staticClass:"tit"},[t._v("地址")]),a("v-uni-text",{staticClass:"btns",attrs:{type:"primary"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.openAddres.apply(void 0,arguments)}}},[t._v(t._s(t.addressData.province)+"-"+t._s(t.addressData.city)+"-"+t._s(t.addressData.district))]),a("simple-address",{ref:"simpleAddress",attrs:{pickerValueDefault:t.cityPickerValueDefault,themeColor:"#007AFF"},on:{onConfirm:function(e){arguments[0]=e=t.$handleEvent(e),t.onConfirm.apply(void 0,arguments)}}}),a("v-uni-text",{staticClass:"yticon icon-shouhuodizhi"})],1),a("v-uni-view",{staticClass:"row b-b"},[a("v-uni-text",{staticClass:"tit"},[t._v("门牌号")]),a("v-uni-input",{staticClass:"input",attrs:{type:"text",placeholder:"楼号、门牌","placeholder-class":"placeholder"},model:{value:t.addressData.detail,callback:function(e){t.$set(t.addressData,"detail",e)},expression:"addressData.detail"}})],1),a("v-uni-view",{staticClass:"row default-row"},[a("v-uni-text",{staticClass:"tit"},[t._v("设为默认")]),a("v-uni-switch",{attrs:{checked:t.addressData.is_default,color:"#fa436a"},on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.switchChange.apply(void 0,arguments)}}})],1),a("v-uni-button",{staticClass:"add-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.confirm.apply(void 0,arguments)}}},[t._v("提交")])],1)},n=[];a.d(e,"b",(function(){return i})),a.d(e,"c",(function(){return n})),a.d(e,"a",(function(){return s}))},"8ad0":function(t,e,a){"use strict";var s=a("9569"),i=a.n(s);i.a},"90f7":function(t,e,a){"use strict";a.r(e);var s=a("0e0b"),i=a.n(s);for(var n in s)"default"!==n&&function(t){a.d(e,t,(function(){return s[t]}))}(n);e["default"]=i.a},9569:function(t,e,a){var s=a("1f24");"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var i=a("4f06").default;i("0dc559a9",s,!0,{sourceMap:!1,shadowMode:!1})},ed33:function(t,e,a){"use strict";a.r(e);var s=a("7710"),i=a("90f7");for(var n in i)"default"!==n&&function(t){a.d(e,t,(function(){return i[t]}))}(n);a("8ad0");var o,r=a("f0c5"),d=Object(r["a"])(i["default"],s["b"],s["c"],!1,null,"08ff1bbc",null,!1,s["a"],o);e["default"]=d.exports}}]);