(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/set/set"],{"1b13":function(t,n,e){"use strict";e.r(n);var u=e("2d3d"),o=e("6533");for(var c in o)"default"!==c&&function(t){e.d(n,t,function(){return o[t]})}(c);e("76e7");var r,a=e("f0c5"),i=Object(a["a"])(o["default"],u["b"],u["c"],!1,null,null,null,!1,u["a"],r);n["default"]=i.exports},"2d3d":function(t,n,e){"use strict";var u,o=function(){var t=this,n=t.$createElement;t._self._c},c=[];e.d(n,"b",function(){return o}),e.d(n,"c",function(){return c}),e.d(n,"a",function(){return u})},6533:function(t,n,e){"use strict";e.r(n);var u=e("ee83"),o=e.n(u);for(var c in u)"default"!==c&&function(t){e.d(n,t,function(){return u[t]})}(c);n["default"]=o.a},"76e7":function(t,n,e){"use strict";var u=e("fb3c"),o=e.n(u);o.a},8454:function(t,n,e){"use strict";(function(t){e("3c4b"),e("921b");u(e("66fd"));var n=u(e("1b13"));function u(t){return t&&t.__esModule?t:{default:t}}t(n.default)}).call(this,e("543d")["createPage"])},ee83:function(t,n,e){"use strict";(function(t){Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var u=e("2f62");function o(t){for(var n=1;n<arguments.length;n++){var e=null!=arguments[n]?arguments[n]:{},u=Object.keys(e);"function"===typeof Object.getOwnPropertySymbols&&(u=u.concat(Object.getOwnPropertySymbols(e).filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),u.forEach(function(n){c(t,n,e[n])})}return t}function c(t,n,e){return n in t?Object.defineProperty(t,n,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[n]=e,t}var r={data:function(){return{}},methods:o({},(0,u.mapMutations)(["logout"]),{navTo:function(t){this.$api.msg("跳转到".concat(t))},toLogout:function(){var n=this;t.showModal({content:"确定要退出登录么",success:function(e){e.confirm&&(n.logout(),setTimeout(function(){t.navigateBack()},200))}})},switchChange:function(t){var n=t.detail.value?"打开":"关闭";this.$api.msg("".concat(n,"消息推送"))}})};n.default=r}).call(this,e("543d")["default"])},fb3c:function(t,n,e){}},[["8454","common/runtime","common/vendor"]]]);