(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-prize-draw"],{1373:function(t,i,e){"use strict";e("c975"),Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var a={data:function(){return{list:[],width:0,animationData:{},btnDisabled:"",act_id:0,noticeInfo:[],checkClassNum:1,allhidden:!1,draw:[],time:3e3}},onLoad:function(t){console.log("说得对覆盖",t),getApp().user_api(!0),this.act_id=t.id,this.activityDetail()},methods:{activityDetail:function(){var t=this,i="/gzh/activity_api/activity_detail",e={act_id:t.act_id};getApp().http(i,e,(function(i){console.log("个人信息",i),t.noticeInfo=i,t.list=i.prize,t.width=360/t.list.length}))},animation:function(t,i){var e=this.list,a=4;this.runDeg=this.runDeg||0,this.runDeg=this.runDeg+(360-this.runDeg%360)+(360*a-t*(360/e.length))+1;var n=uni.createAnimation({duration:i,timingFunction:"ease"});n.rotate(this.runDeg).step(),this.animationData=n.export()},playReward:function(){var t=this,i=t.noticeInfo.start_time,e=new Date(i),a=Date.parse(e),n=a/1e3,o=t.noticeInfo.end_time,r=new Date(o),s=Date.parse(r),c=s/1e3,d=new Date,l=Date.parse(d),u=l/1e3;if(u<n)getApp().showTip("活动未开始","none");else if(u>c)getApp().showTip("活动已结束","none");else{if(t.noticeInfo.prize_num<=0)return this.btnDisabled="disabled",void getApp().showTip("抽奖次数已用完","none");var f=t.list,v="/gzh/activity_api/prize_draw",p={act_id:t.act_id};getApp().http(v,p,(function(i){t.draw=i,t.noticeInfo.prize_num=t.noticeInfo.prize_num-1;var e=t.inArray(i.prize_id,f),a=e,n=t.time;t.animation(a,n),setTimeout((function(){t.allClass()}),t.time)}))}},inArray:function(t,i){for(var e in i)if(i[e].id==t)return e;return!1},allClass:function(){var t=this;t.allhidden=!0},hideAllClass:function(){console.log("隐藏隐藏隐藏隐藏");var t=this;console.log(this),t.allhidden=!1},indexUrl:function(t){var i=t.indexOf("http");return-1==i?this.webUrl+"/"+t:t}}};i.default=a},1947:function(t,i,e){"use strict";e.r(i);var a=e("1373"),n=e.n(a);for(var o in a)"default"!==o&&function(t){e.d(i,t,(function(){return a[t]}))}(o);i["default"]=n.a},"1de5":function(t,i,e){"use strict";t.exports=function(t,i){return i||(i={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),i.hash&&(t+=i.hash),/["'() \t\n]/.test(t)||i.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"34a7":function(t,i,e){t.exports=e.p+"static/img/caidai.8005f73c.png"},3838:function(t,i,e){"use strict";e.r(i);var a=e("e63b"),n=e("1947");for(var o in n)"default"!==o&&function(t){e.d(i,t,(function(){return n[t]}))}(o);e("8acd");var r,s=e("f0c5"),c=Object(s["a"])(n["default"],a["b"],a["c"],!1,null,"706c226e",null,!1,a["a"],r);i["default"]=c.exports},6475:function(t,i,e){t.exports=e.p+"static/img/bg.0d93a100.png"},"6d12":function(t,i,e){var a=e("24fb"),n=e("1de5"),o=e("c808");i=a(!1);var r=n(o);i.push([t.i,".icon-awrad[data-v-706c226e]{font-size:%?50?%!important}.conbox[data-v-706c226e]{width:%?750?%;height:100vh;overflow-x:hidden;overflow-y:scroll}.container[data-v-706c226e],\r\n    uni-image.cont[data-v-706c226e]{width:%?750?%;min-height:100vh;height:auto;position:relative}uni-image.cont[data-v-706c226e]{height:100%;position:absolute;z-index:0}uni-image.caidai[data-v-706c226e]{position:absolute;top:0;left:0;width:%?750?%;height:%?1024?%}.header-title>uni-view[data-v-706c226e]{padding:%?8?% %?16?%;border:1px solid #d89720;color:#d89720;font-size:%?28?%;border-radius:%?26?%}.cl-num[data-v-706c226e]{padding:%?10?% %?30?%}.cli-num[data-v-706c226e]{color:#b87e3d;font-size:%?28?%;border:1px solid #b87e3d;padding:%?7?% %?25?%;border-radius:%?50?%;z-index:9}.red[data-v-706c226e]{color:red}\r\n\r\n    /* 转盘 */.canvas-container[data-v-706c226e]{margin:0 auto;position:relative;width:%?600?%;height:%?600?%;background:url("+r+') no-repeat;background-size:cover;border-radius:50%}.canvas-content[data-v-706c226e]{position:absolute;left:0;top:0;z-index:1;display:block;width:%?600?%;height:%?600?%;border-radius:inherit\r\n        /* background-clip: padding-box; */\r\n        /* background-color: #ffcb3f; */}.canvas-list[data-v-706c226e]{position:absolute;left:0;top:0;width:inherit;height:inherit;z-index:9999}.canvas-item[data-v-706c226e]{position:absolute;left:0;top:0;width:100%;height:100%;color:#e4370e\r\n        /* text-shadow: 0 1upx 1upx rgba(255, 255, 255, 0.6); */}.canvas-item-text[data-v-706c226e]{position:relative;display:block;padding-top:%?46?%;margin:0 auto;text-align:center;-webkit-transform-origin:50% %?300?%;transform-origin:50% %?300?%;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-align:center;-webkit-align-items:center;align-items:center;color:#fb778b}.canvas-item-text uni-text[data-v-706c226e]{font-size:%?30?%}.b[data-v-706c226e]{font-size:%?32?%\r\n\t\t/* border:1px solid red; */}.icon-img[data-v-706c226e]{width:%?60?%;height:%?60?%;margin-top:%?40?%}\r\n\r\n    /* 分隔线 */.canvas-line[data-v-706c226e]{position:absolute;left:0;top:0;width:inherit;height:inherit;z-index:99}.canvas-litem[data-v-706c226e]{position:absolute;left:%?300?%;top:0;width:%?3?%;height:%?300?%;background-color:rgba(228,55,14,.4);overflow:hidden;-webkit-transform-origin:50% %?300?%;transform-origin:50% %?300?%}\r\n\r\n    /**\r\n* 抽奖按钮\r\n*/.canvas-btn[data-v-706c226e]{position:absolute;left:%?260?%;top:%?260?%;z-index:10;width:%?80?%;height:%?80?%;border-radius:50%;color:#f4e9cc;background-color:#e44025;line-height:%?80?%;text-align:center;font-size:%?26?%;text-shadow:0 -1px 1px rgba(0,0,0,.6);box-shadow:0 3px 5px rgba(0,0,0,.6);text-decoration:none}.canvas-btn[data-v-706c226e]::after{position:absolute;display:block;content:" ";left:%?12?%;top:%?-44?%;width:0;height:0;overflow:hidden;border-width:%?30?%;border-style:solid;border-color:transparent;border-bottom-color:#e44025}.canvas-btn.disabled[data-v-706c226e]{pointer-events:none;background:#b07a7b;color:#ccc}.canvas-btn.disabled[data-v-706c226e]::after{border-bottom-color:#b07a7b}.typecheckbox uni-view[data-v-706c226e]{border:1px solid #ff3637;background:transparent;color:#ff3637;display:-webkit-box;display:-webkit-flex;display:flex;height:%?60?%;width:%?140?%;border-radius:%?50?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;display:flex;margin-left:%?20?%}.guize[data-v-706c226e]{width:%?560?%;min-height:%?300?%;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;position:relative;z-index:3;background-image:-webkit-linear-gradient(top,#f48549,#f2642e);background-image:linear-gradient(-180deg,#f48549,#f2642e);border:%?18?% solid #e4431a;border-radius:16px;margin:0 auto;margin-top:%?-104?%;padding:%?48?%;\r\n        /* box-sizing: border-box; */color:#fff}.guize .title[data-v-706c226e]{text-align:center;margin-bottom:%?28?%}.guize .g_item[data-v-706c226e]{font-family:PingFang-SC-Medium;font-size:%?24?%;color:#fff;letter-spacing:.5px;\r\n        /* text-align: justify; */line-height:20px}.myrewards .title[data-v-706c226e]{font-family:PingFang-SC-Bold;font-size:16px;color:#e4431a;letter-spacing:.57px;display:-webkit-box;display:-webkit-flex;display:flex;padding-top:%?36?%;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}\r\n\r\n    /* 弹出框 */.modal[data-v-706c226e]{width:100%;height:100%;position:fixed;z-index:99}.modal-box[data-v-706c226e]{position:fixed;width:100%;height:100%;top:0;background:rgba(0,0,0,.4);overflow:hidden;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;z-index:9999}.modal-body[data-v-706c226e]{position:fixed;left:15%;top:38%;background-color:#fff;width:%?540?%;border-radius:%?10?%;padding:%?20?%;z-index:9999\r\n\t\t/* height:100%; */}.mod-tit[data-v-706c226e]{text-align:center;margin-bottom:%?25?%;font-size:%?35?%}.modi[data-v-706c226e]{text-align:center}.mod-img[data-v-706c226e]{width:%?200?%;height:%?200?%}.mod-btn[data-v-706c226e]{background:#f44747;color:#fff;border-radius:%?50?%;text-align:center;padding:%?10?% 0;margin-top:%?25?%;margin-bottom:%?20?%}.modde[data-v-706c226e]{text-align:center;font-size:%?23?%;color:#989898}.modal-body[data-v-706c226e]{-webkit-animation:fadeInDown-data-v-706c226e .3s;animation:fadeInDown-data-v-706c226e .3s}@-webkit-keyframes fadeInDown-data-v-706c226e{0%{-webkit-transform:translate3d(0,-20%,0);-webkit-transform:translate3d(0,-20%,0);transform:translate3d(0,-20%,0);transform:translate3d(0,-20%,0);opacity:0}100%{-webkit-transform:none;transform:none;opacity:1}}@keyframes fadeInDown-data-v-706c226e{0%{-webkit-transform:translate3d(0,-20%,0);-webkit-transform:translate3d(0,-20%,0);transform:translate3d(0,-20%,0);transform:translate3d(0,-20%,0);opacity:0}100%{-webkit-transform:none;transform:none;opacity:1}}',""]),t.exports=i},"8acd":function(t,i,e){"use strict";var a=e("acf1"),n=e.n(a);n.a},acf1:function(t,i,e){var a=e("6d12");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=e("4f06").default;n("cd85051c",a,!0,{sourceMap:!1,shadowMode:!1})},c808:function(t,i,e){t.exports=e.p+"static/img/circle.2fd04c3a.png"},e63b:function(t,i,e){"use strict";var a,n=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("v-uni-view",{staticClass:"conbox"},[a("v-uni-view",{staticClass:"container"},[a("v-uni-image",{staticClass:"cont",attrs:{src:e("6475"),mode:""}}),a("v-uni-image",{staticClass:"caidai",attrs:{src:e("34a7"),mode:""}}),a("v-uni-view",{staticClass:"main",staticStyle:{"padding-top":"50upx"}},[a("v-uni-view",{staticClass:"cl-num aces-space-between"},[a("v-uni-view",{staticClass:"cli-num"},[t._v("次数："),a("v-uni-text",{staticClass:"red"},[t._v(t._s(t.noticeInfo.prize_num))])],1)],1),a("v-uni-view",{staticClass:"canvas-container"},[a("v-uni-view",{staticClass:"canvas-content",attrs:{animation:t.animationData,id:"zhuanpano"}},[a("v-uni-view",{staticClass:"canvas-line"},t._l(t.list,(function(i,e){return a("v-uni-view",{key:e,staticClass:"canvas-litem",style:{transform:"rotate("+(e*t.width+t.width/2)+"deg)"}})})),1),a("v-uni-view",{staticClass:"canvas-list"},t._l(t.list,(function(i,e){return a("v-uni-view",{key:e,staticClass:"canvas-item",style:{transform:"rotate("+e*t.width+"deg)",zIndex:e}},[a("v-uni-view",{staticClass:"canvas-item-text",style:"transform:rotate("+e+")"},[a("v-uni-text",{staticClass:"b"},[t._v(t._s(i.prize_name))]),a("v-uni-image",{staticClass:"icon-img",attrs:{src:t.indexUrl(i.image)}})],1)],1)})),1)],1),a("v-uni-view",{staticClass:"canvas-btn",class:t.btnDisabled,on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.playReward.apply(void 0,arguments)}}},[t._v("开始")])],1)],1),a("v-uni-view",{staticClass:"guize",staticStyle:{"margin-top":"100upx"}},[a("v-uni-view",{staticClass:"title"},[t._v(t._s(t.noticeInfo.title))]),a("v-uni-view",{staticClass:"g_item"},[t._v("活动开始时间："+t._s(t.noticeInfo.start_time))]),a("v-uni-view",{staticClass:"g_item"},[t._v("活动结束时间："+t._s(t.noticeInfo.end_time))]),t.noticeInfo.use_score>0?a("v-uni-view",{staticClass:"g_item"},[t._v("每次抽奖消耗"+t._s(t.noticeInfo.use_score)+"积分")]):t._e(),a("v-uni-view",{staticClass:"g_item"},[t._v(t._s(t.noticeInfo.description))])],1)],1),t.allhidden?a("v-uni-view",{staticClass:"modal"},[a("v-uni-view",{staticClass:"modal-box",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.hideAllClass.apply(void 0,arguments)}}}),a("v-uni-view",{staticClass:"modal-body "},[a("v-uni-view",{staticClass:"mod-tit"},[t._v("恭喜您抽中了"+t._s(t.draw.prize_name))]),a("v-uni-view",{staticClass:"modi"},[a("v-uni-image",{staticClass:"mod-img",attrs:{src:t.indexUrl(t.draw.image)}})],1),a("v-uni-view",{staticClass:"modde"},[t._v("*请到个人信息页面补全信息")]),a("v-uni-view",{staticClass:"mod-btn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.hideAllClass.apply(void 0,arguments)}}},[t._v("确定")])],1)],1):t._e()],1)},o=[];e.d(i,"b",(function(){return n})),e.d(i,"c",(function(){return o})),e.d(i,"a",(function(){return a}))}}]);