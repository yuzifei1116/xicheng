(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/haibao/haibao"],{"01b3":function(t,e,n){"use strict";n.r(e);var a=n("115a"),i=n.n(a);for(var o in a)"default"!==o&&function(t){n.d(e,t,function(){return a[t]})}(o);e["default"]=i.a},"115a":function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=r(n("a34a")),i=r(n("b530")),o=n("c69a");function r(t){return t&&t.__esModule?t:{default:t}}function u(t,e,n,a,i,o,r){try{var u=t[o](r),s=u.value}catch(c){return void n(c)}u.done?e(s):Promise.resolve(s).then(a,i)}function s(t){return function(){var e=this,n=arguments;return new Promise(function(a,i){var o=t.apply(e,n);function r(t){u(o,a,i,r,s,"next",t)}function s(t){u(o,a,i,r,s,"throw",t)}r(void 0)})}}var c={data:function(){return{poster:{},qrShow:!1,canvasId:"default_PosterCanvasId",val:"",beijing:[]}},onLoad:function(){this.my()},methods:{my:function(){var t=this,e="/gzh/user_api/my";getApp().http(e,{},function(e){console.log("个人信息",e),t.val=t.webUrl+"/gzh/?spuid="+e.uid,t.haiB()})},haiB:function(){var e=this;console.log("沙发上对方",this.userInfo,this.webUrl),t.request({url:e.webUrl+"/gzh/auth_api/get_spread_poster",data:{},header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){e.beijing=t.data.data,console.log("获取 ",e.beijing),e.shareFc()}})},shareFc:function(){var t=s(a.default.mark(function t(){var e,n,r=this;return a.default.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return e=this,console.log(e.beijing[0],"背景"),t.prev=2,i.default.log("准备生成:"+new Date),t.next=6,(0,o.getSharePoster)({_this:this,type:"testShareType",formData:{},posterCanvasId:this.canvasId,delayTimeScale:20,backgroundImage:e.beijing[0].pic,drawArray:function(t){var n=t.bgObj;t.type,t.bgScale,n.width,n.width,n.height;return new Promise(function(t,a){t([{type:"qrcode",text:e.val,size:.2*n.width,dx:.05*n.width,dy:n.height-.25*n.width}])})},setCanvasWH:function(t){var e=t.bgObj;t.type,t.bgScale;r.poster=e}});case 6:n=t.sent,i.default.log("海报生成成功, 时间:"+new Date+"， 临时路径: "+n.poster.tempFilePath),this.poster.finalPath=n.poster.tempFilePath,this.qrShow=!0,t.next=17;break;case 12:t.prev=12,t.t0=t["catch"](2),i.default.hideLoading(),i.default.showToast(JSON.stringify(t.t0)),console.log(JSON.stringify(t.t0));case 17:case"end":return t.stop()}},t,this,[[2,12]])}));function e(){return t.apply(this,arguments)}return e}(),saveImage:function(){t.saveImageToPhotosAlbum({filePath:this.poster.finalPath,success:function(t){i.default.showToast("保存成功")}})},share:function(){i.default.showToast("分享了")},hideQr:function(){this.qrShow=!1}}};e.default=c}).call(this,n("543d")["default"])},"1c8f":function(t,e,n){"use strict";var a=n("495c"),i=n.n(a);i.a},"495c":function(t,e,n){},"54bc":function(t,e,n){"use strict";var a,i=function(){var t=this,e=t.$createElement;t._self._c},o=[];n.d(e,"b",function(){return i}),n.d(e,"c",function(){return o}),n.d(e,"a",function(){return a})},d7f1:function(t,e,n){"use strict";n.r(e);var a=n("54bc"),i=n("01b3");for(var o in i)"default"!==o&&function(t){n.d(e,t,function(){return i[t]})}(o);n("1c8f");var r,u=n("f0c5"),s=Object(u["a"])(i["default"],a["b"],a["c"],!1,null,null,null,!1,a["a"],r);e["default"]=s.exports},ebd7:function(t,e,n){"use strict";(function(t){n("3c4b"),n("921b");a(n("66fd"));var e=a(n("d7f1"));function a(t){return t&&t.__esModule?t:{default:t}}t(e.default)}).call(this,n("543d")["createPage"])}},[["ebd7","common/runtime","common/vendor"]]]);