(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-user-user_sgin"],{"0446":function(t,e,a){"use strict";a.r(e);var n=a("4bf6"),i=a.n(n);for(var r in n)"default"!==r&&function(t){a.d(e,t,(function(){return n[t]}))}(r);e["default"]=i.a},"1b9d":function(t,e,a){"use strict";var n=a("40a9"),i=a.n(n);i.a},3843:function(t,e,a){"use strict";var n,i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"all"},[a("v-uni-view",{staticClass:"bar"},[a("v-uni-view",{staticClass:"previous",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.handleCalendar(0)}}},["ch"==t.langType?a("v-uni-button",{staticClass:"barbtn"},[t._v("上一月")]):a("v-uni-button",{staticClass:"barbtn"},[t._v("Last")])],1),a("v-uni-view",{staticClass:"date"},[t._v(t._s(t.cur_year||"--")+" 年 "+t._s(t.cur_month||"--")+" 月")]),a("v-uni-view",{staticClass:"next",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.handleCalendar(1)}}},["ch"==t.langType?a("v-uni-button",{staticClass:"barbtn"},[t._v("下一月")]):a("v-uni-button",{staticClass:"barbtn"},[t._v("Next")])],1)],1),a("v-uni-view",{staticClass:"myDateTable"},["ch"==t.langType?a("v-uni-view",{staticClass:"week"},t._l(t.weeks_ch,(function(e,n){return a("v-uni-view",{key:n},[t._v(t._s(e))])})),1):a("v-uni-view",{staticClass:"week"},t._l(t.weeks_en,(function(e,n){return a("v-uni-view",{key:n},[t._v(t._s(e))])})),1),t._l(t.days,(function(e,n){return a("v-uni-view",{key:n,staticClass:"dateCell"},[void 0==e.date||null==e.date?a("v-uni-view",{staticClass:"cell"},[a("v-uni-text",{attrs:{decode:!0}},[t._v("")])],1):a("v-uni-view",[1==e.isSign?a("v-uni-view",{staticClass:"cell greenColor bgWhite  "},[a("v-uni-text",[t._v(t._s(e.date))])],1):t.cur_year<t.toYear||t.cur_year==t.toYear&&t.cur_month<t.toMonth||t.cur_year==t.toYear&&t.cur_month==t.toMonth&&e.date<t.today?a("v-uni-view",{staticClass:"cell redColor bgGray"},[a("v-uni-text",[t._v(t._s(e.date))])],1):e.date==t.today&&t.cur_month==t.toMonth&&t.cur_year==t.toYear?a("v-uni-view",{staticClass:"cell whiteColor bgBlue",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.clickSignUp(e.date,1)}}},[a("v-uni-text",[t._v("签到")])],1):a("v-uni-view",{staticClass:"whiteColor cell"},[a("v-uni-text",[t._v(t._s(e.date))])],1)],1)],1)}))],2),a("v-uni-view",{staticClass:"TipArea aces-space-between"},[a("v-uni-view",{staticClass:"leij"},[t._v("累计打卡"),a("v-uni-text",{staticClass:"leijnum"},[t._v(t._s(t.data.allday))]),t._v("天")],1),a("v-uni-view",{staticClass:"leij"},[t._v("本月打卡"),a("v-uni-text",{staticClass:"leijnum"},[t._v(t._s(t.data.monthday))]),t._v("天")],1)],1)],1)},r=[];a.d(e,"b",(function(){return i})),a.d(e,"c",(function(){return r})),a.d(e,"a",(function(){return n}))},"40a9":function(t,e,a){var n=a("eaee");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=a("4f06").default;i("46471d2e",n,!0,{sourceMap:!1,shadowMode:!1})},"4bf6":function(t,e,a){"use strict";a("a9e3"),a("e25e"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={data:function(){return{days:[],SignUp:[],cur_year:0,cur_month:0,today:parseInt((new Date).getDate()),toMonth:parseInt((new Date).getMonth()+1),toYear:parseInt((new Date).getFullYear()),weeks_ch:["日","一","二","三","四","五","六"],weeks_en:["Sun","Mon","Tues","Wed","Thur","Fri","Sat"],data:[]}},props:{sendYear:{type:Number,default:(new Date).getFullYear()},sendMonth:{type:Number,default:(new Date).getMonth()+1},dataSource:{type:Array,default:function(){return[]}},langType:{type:String,default:"ch"}},created:function(){this.cur_year=this.sendYear,this.cur_month=this.sendMonth,this.SignUp=this.dataSource,this.calculateEmptyGrids(this.cur_year,this.cur_month),this.calculateDays(this.cur_year,this.cur_month),this.onJudgeSign()},watch:{dataSource:"onResChange"},methods:{getThisMonthDays:function(t,e){return new Date(t,e,0).getDate()},getFirstDayOfWeek:function(t,e){return new Date(Date.UTC(t,e-1,1)).getDay()},calculateEmptyGrids:function(t,e){this.days=[];var a=this.getFirstDayOfWeek(t,e);if(a>0)for(var n=0;n<a;n++){var i={date:null,isSign:!1};this.days.push(i)}},calculateDays:function(t,e){for(var a=this.getThisMonthDays(t,e),n=1;n<=a;n++){var i={date:n,isSign:!1};this.days.push(i)}},onResChange:function(t,e){this.SignUp=t,this.onJudgeSign()},onJudgeSign:function(t){var e=this,a="/gzh/user_api/get_sign_list",n=t||e.cur_year+"-"+e.cur_month,i={date:n};getApp().http(a,i,(function(t){e.data=t;var a=t.list,n=e.days;console.log("第三个地方官",a,n);for(var i=0;i<a.length;i++){var r=a[i].add_time;r=parseInt(r);for(var s=0;s<n.length;s++)r==n[s].date&&(n[s].isSign=!0)}e.days=n}))},handleCalendar:function(t){var e,a=parseInt(this.cur_year),n=parseInt(this.cur_month),i=a;0===t?(e=n-1,e<1&&(i=a-1,e=12)):(e=n+1,e>12&&(i=a+1,e=1));var r=this.cur_year+"-"+e;this.onJudgeSign(r),this.calculateEmptyGrids(i,e),this.calculateDays(i,e),this.cur_year=i,this.cur_month=e,this.SignUp=[],this.$emit("dateChange",this.cur_year+"-"+this.cur_month)},clickSignUp:function(t,e){var a=this,n=a.cur_year+"-"+a.cur_month,i="/gzh/user_api/user_sign";getApp().http(i,{},(function(t){console.log("个人信息",t),200==t.code?uni.showToast({title:t.msg,icon:"success",duration:2e3}):uni.showToast({title:t.msg,icon:"none",duration:2e3})}),!0),this.$forceUpdate(),this.$emit("clickChange",this.cur_year+"-"+this.cur_month+"-"+t),this.onJudgeSign(n)}}};e.default=n},"8cf9":function(t,e,a){"use strict";a.r(e);var n=a("3843"),i=a("0446");for(var r in i)"default"!==r&&function(t){a.d(e,t,(function(){return i[t]}))}(r);a("1b9d");var s,o=a("f0c5"),d=Object(o["a"])(i["default"],n["b"],n["c"],!1,null,"5285b6d1",null,!1,n["a"],s);e["default"]=d.exports},eaee:function(t,e,a){var n=a("24fb");e=n(!1),e.push([t.i,".all[data-v-5285b6d1]{margin-top:%?20?%}.all .bar[data-v-5285b6d1]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;margin:%?30?% %?20?%;padding:%?10?%}.bar .barbtn[data-v-5285b6d1]{height:%?60?%;line-height:%?60?%;font-size:%?24?%}.all .week[data-v-5285b6d1]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;\r\n\t/* padding: 20upx;\r\n\tpadding-left: 40upx;\r\n\tpadding-right: 40upx; */\r\n\t/* border:1px solid red; */\r\n\t/* background-color: #fff; */padding:%?20?% %?36?% %?20?% %?20?%;border-radius:%?20?%;color:#fff;font-size:%?30?%}.myDateTable[data-v-5285b6d1]{margin:%?20?%;\r\n\t/* padding: 20upx 0upx 20upx 18upx; */border-radius:%?20?%;background:-webkit-linear-gradient(#74aada,#94db98);background:linear-gradient(#74aada,#94db98)}.myDateTable .dateCell[data-v-5285b6d1]{\r\n\t/* width: 11vw; */\r\n\t/* width: 22upx; */width:%?65?%;\r\n\t/* padding: 1vw; */display:inline-block;text-align:center;font-size:16px;margin:%?19?%}.dateCell .cell[data-v-5285b6d1]{display:-webkit-box;display:-webkit-flex;display:flex;border-radius:50%;height:%?65?%;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.whiteColor[data-v-5285b6d1]{color:#fff}.greenColor[data-v-5285b6d1]{color:#01b90b;font-weight:700}.bgWhite[data-v-5285b6d1]{background-color:#fff}.bgGray[data-v-5285b6d1]{background-color:hsla(0,0%,100%,.42)}.bgBlue[data-v-5285b6d1]{font-size:14px;background-color:#4b95e6}.redColor[data-v-5285b6d1]{color:red}.TipArea[data-v-5285b6d1]{word-break:break-all;word-wrap:break-word;font-size:14px;padding:20px;margin:%?0?% %?20?%;border-radius:%?20?%;background:-webkit-linear-gradient(#74aada,#94db98);background:linear-gradient(#74aada,#94db98)}.impTip[data-v-5285b6d1]{display:inline-block;color:red}\r\n\r\n/* 总打卡 */.leij[data-v-5285b6d1]{color:#e3f7f2}.leijnum[data-v-5285b6d1]{color:#fff;font-size:%?50?%;font-weight:700}",""]),t.exports=e}}]);