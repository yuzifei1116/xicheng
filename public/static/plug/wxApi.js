!function(e,n){"function"==typeof define&&(define.amd||define.cmd)?define(function(){return n(e)}):n(e,!0)}(this,function(e,n){function i(n,i,t){e.WeixinJSBridge?WeixinJSBridge.invoke(n,o(i),function(e){c(n,e,t)}):u(n,t)}function t(n,i,t){e.WeixinJSBridge?WeixinJSBridge.on(n,function(e){t&&t.trigger&&t.trigger(e),c(n,e,i)}):t?u(n,t):u(n,i)}function o(e){return e=e||{},e.appId=C.appId,e.verifyAppId=C.appId,e.verifySignType="sha1",e.verifyTimestamp=C.timestamp+"",e.verifyNonceStr=C.nonceStr,e.verifySignature=C.signature,e}function r(e){return{timeStamp:e.timestamp+"",nonceStr:e.nonceStr,package:e.package,paySign:e.paySign,signType:e.signType||"SHA1"}}function a(e){return e.postalCode=e.addressPostalCode,delete e.addressPostalCode,e.provinceName=e.proviceFirstStageName,delete e.proviceFirstStageName,e.cityName=e.addressCitySecondStageName,delete e.addressCitySecondStageName,e.countryName=e.addressCountiesThirdStageName,delete e.addressCountiesThirdStageName,e.detailInfo=e.addressDetailInfo,delete e.addressDetailInfo,e}function c(e,n,i){"openEnterpriseChat"==e&&(n.errCode=n.err_code),delete n.err_code,delete n.err_desc,delete n.err_detail;var t=n.errMsg;t||(t=n.err_msg,delete n.err_msg,t=s(e,t),n.errMsg=t),(i=i||{})._complete&&(i._complete(n),delete i._complete),t=n.errMsg||"",C.debug&&!i.isInnerInvoke&&alert(JSON.stringify(n));var o=t.indexOf(":");switch(t.substring(o+1)){case"ok":i.success&&i.success(n);break;case"cancel":i.cancel&&i.cancel(n);break;default:i.fail&&i.fail(n)}i.complete&&i.complete(n)}function s(e,n){var i=e,t=v[i];t&&(i=t);var o="ok";if(n){var r=n.indexOf(":");"confirm"==(o=n.substring(r+1))&&(o="ok"),"failed"==o&&(o="fail"),-1!=o.indexOf("failed_")&&(o=o.substring(7)),-1!=o.indexOf("fail_")&&(o=o.substring(5)),"access denied"!=(o=(o=o.replace(/_/g," ")).toLowerCase())&&"no permission to execute"!=o||(o="permission denied"),"config"==i&&"function not exist"==o&&(o="ok"),""==o&&(o="fail")}return n=i+":"+o}function d(e){if(e){for(var n=0,i=e.length;n<i;++n){var t=e[n],o=h[t];o&&(e[n]=o)}return e}}function u(e,n){if(!(!C.debug||n&&n.isInnerInvoke)){var i=v[e];i&&(e=i),n&&n._complete&&delete n._complete,console.log('"'+e+'",',n||"")}}function l(e){if(!(k||w||C.debug||x<"6.0.2"||V.systemType<0)){var n=new Image;V.appId=C.appId,V.initTime=A.initEndTime-A.initStartTime,V.preVerifyTime=A.preVerifyEndTime-A.preVerifyStartTime,N.getNetworkType({isInnerInvoke:!0,success:function(e){V.networkType=e.networkType;var i="https://open.weixin.qq.com/sdk/report?v="+V.version+"&o="+V.isPreVerifyOk+"&s="+V.systemType+"&c="+V.clientVersion+"&a="+V.appId+"&n="+V.networkType+"&i="+V.initTime+"&p="+V.preVerifyTime+"&u="+V.url;n.src=i}})}}function p(){return(new Date).getTime()}function f(n){T&&(e.WeixinJSBridge?n():S.addEventListener&&S.addEventListener("WeixinJSBridgeReady",n,!1))}function m(){N.invoke||(N.invoke=function(n,i,t){e.WeixinJSBridge&&WeixinJSBridge.invoke(n,o(i),t)},N.on=function(n,i){e.WeixinJSBridge&&WeixinJSBridge.on(n,i)})}function g(e){if("string"==typeof e&&e.length>0){var n=e.split("?")[0],i=e.split("?")[1];return n+=".html",void 0!==i?n+"?"+i:n}}if(!e.jWeixin){var h={config:"preVerifyJSAPI",onMenuShareTimeline:"menu:share:timeline",onMenuShareAppMessage:"menu:share:appmessage",onMenuShareQQ:"menu:share:qq",onMenuShareWeibo:"menu:share:weiboApp",onMenuShareQZone:"menu:share:QZone",previewImage:"imagePreview",getLocation:"geoLocation",openProductSpecificView:"openProductViewWithPid",addCard:"batchAddCard",openCard:"batchViewCard",chooseWXPay:"getBrandWCPayRequest",openEnterpriseRedPacket:"getRecevieBizHongBaoRequest",startSearchBeacons:"startMonitoringBeacons",stopSearchBeacons:"stopMonitoringBeacons",onSearchBeacons:"onBeaconsInRange",consumeAndShareCard:"consumedShareCard",openAddress:"editAddress"},v=function(){var e={};for(var n in h)e[h[n]]=n;return e}(),S=e.document,I=S.title,y=navigator.userAgent.toLowerCase(),_=navigator.platform.toLowerCase(),k=!(!_.match("mac")&&!_.match("win")),w=-1!=y.indexOf("wxdebugger"),T=-1!=y.indexOf("micromessenger"),M=-1!=y.indexOf("android"),P=-1!=y.indexOf("iphone")||-1!=y.indexOf("ipad"),x=function(){var e=y.match(/micromessenger\/(\d+\.\d+\.\d+)/)||y.match(/micromessenger\/(\d+\.\d+)/);return e?e[1]:""}(),A={initStartTime:p(),initEndTime:0,preVerifyStartTime:0,preVerifyEndTime:0},V={version:1,appId:"",initTime:0,preVerifyTime:0,networkType:"",isPreVerifyOk:1,systemType:P?1:M?2:-1,clientVersion:x,url:encodeURIComponent(location.href)},C={},L={_completes:[]},B={state:0,data:{}};f(function(){A.initEndTime=p()});var O=!1,E=[],N={config:function(e){C=e,u("config",e);var n=!1!==C.check;f(function(){if(n)i(h.config,{verifyJsApiList:d(C.jsApiList)},function(){L._complete=function(e){A.preVerifyEndTime=p(),B.state=1,B.data=e},L.success=function(e){V.isPreVerifyOk=0},L.fail=function(e){L._fail?L._fail(e):B.state=-1};var e=L._completes;return e.push(function(){l()}),L.complete=function(n){for(var i=0,t=e.length;i<t;++i)e[i]();L._completes=[]},L}()),A.preVerifyStartTime=p();else{B.state=1;for(var e=L._completes,t=0,o=e.length;t<o;++t)e[t]();L._completes=[]}}),m()},ready:function(e){0!=B.state?e():(L._completes.push(e),!T&&C.debug&&e())},error:function(e){x<"6.0.2"||(-1==B.state?e(B.data):L._fail=e)},checkJsApi:function(e){var n=function(e){var n=e.checkResult;for(var i in n){var t=v[i];t&&(n[t]=n[i],delete n[i])}return e};i("checkJsApi",{jsApiList:d(e.jsApiList)},(e._complete=function(e){if(M){var i=e.checkResult;i&&(e.checkResult=JSON.parse(i))}e=n(e)},e))},onMenuShareTimeline:function(e){t(h.onMenuShareTimeline,{complete:function(){i("shareTimeline",{title:e.title||I,desc:e.title||I,img_url:e.imgUrl||"",link:e.link||location.href,type:e.type||"link",data_url:e.dataUrl||""},e)}},e)},onMenuShareAppMessage:function(e){t(h.onMenuShareAppMessage,{complete:function(n){"favorite"===n.scene?i("sendAppMessage",{title:e.title||I,desc:e.desc||"",link:e.link||location.href,img_url:e.imgUrl||"",type:e.type||"link",data_url:e.dataUrl||""}):i("sendAppMessage",{title:e.title||I,desc:e.desc||"",link:e.link||location.href,img_url:e.imgUrl||"",type:e.type||"link",data_url:e.dataUrl||""},e)}},e)},onMenuShareQQ:function(e){t(h.onMenuShareQQ,{complete:function(){i("shareQQ",{title:e.title||I,desc:e.desc||"",img_url:e.imgUrl||"",link:e.link||location.href},e)}},e)},onMenuShareWeibo:function(e){t(h.onMenuShareWeibo,{complete:function(){i("shareWeiboApp",{title:e.title||I,desc:e.desc||"",img_url:e.imgUrl||"",link:e.link||location.href},e)}},e)},onMenuShareQZone:function(e){t(h.onMenuShareQZone,{complete:function(){i("shareQZone",{title:e.title||I,desc:e.desc||"",img_url:e.imgUrl||"",link:e.link||location.href},e)}},e)},updateTimelineShareData:function(e){i("updateTimelineShareData",{title:e.title,link:e.link,imgUrl:e.imgUrl},e)},updateAppMessageShareData:function(e){i("updateAppMessageShareData",{title:e.title,desc:e.desc,link:e.link,imgUrl:e.imgUrl},e)},startRecord:function(e){i("startRecord",{},e)},stopRecord:function(e){i("stopRecord",{},e)},onVoiceRecordEnd:function(e){t("onVoiceRecordEnd",e)},playVoice:function(e){i("playVoice",{localId:e.localId},e)},pauseVoice:function(e){i("pauseVoice",{localId:e.localId},e)},stopVoice:function(e){i("stopVoice",{localId:e.localId},e)},onVoicePlayEnd:function(e){t("onVoicePlayEnd",e)},uploadVoice:function(e){i("uploadVoice",{localId:e.localId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},downloadVoice:function(e){i("downloadVoice",{serverId:e.serverId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},translateVoice:function(e){i("translateVoice",{localId:e.localId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},chooseImage:function(e){i("chooseImage",{scene:"1|2",count:e.count||9,sizeType:e.sizeType||["original","compressed"],sourceType:e.sourceType||["album","camera"]},(e._complete=function(e){if(M){var n=e.localIds;try{n&&(e.localIds=JSON.parse(n))}catch(e){}}},e))},getLocation:function(e){},previewImage:function(e){i(h.previewImage,{current:e.current,urls:e.urls},e)},uploadImage:function(e){i("uploadImage",{localId:e.localId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},downloadImage:function(e){i("downloadImage",{serverId:e.serverId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},getLocalImgData:function(e){!1===O?(O=!0,i("getLocalImgData",{localId:e.localId},(e._complete=function(e){if(O=!1,E.length>0){var n=E.shift();wx.getLocalImgData(n)}},e))):E.push(e)},getNetworkType:function(e){var n=function(e){var n=e.errMsg;e.errMsg="getNetworkType:ok";var i=e.subtype;if(delete e.subtype,i)e.networkType=i;else{var t=n.indexOf(":"),o=n.substring(t+1);switch(o){case"wifi":case"edge":case"wwan":e.networkType=o;break;default:e.errMsg="getNetworkType:fail"}}return e};i("getNetworkType",{},(e._complete=function(e){e=n(e)},e))},openLocation:function(e){i("openLocation",{latitude:e.latitude,longitude:e.longitude,name:e.name||"",address:e.address||"",scale:e.scale||28,infoUrl:e.infoUrl||""},e)},getLocation:function(e){e=e||{},i(h.getLocation,{type:e.type||"wgs84"},(e._complete=function(e){delete e.type},e))},hideOptionMenu:function(e){i("hideOptionMenu",{},e)},showOptionMenu:function(e){i("showOptionMenu",{},e)},closeWindow:function(e){i("closeWindow",{},e=e||{})},hideMenuItems:function(e){i("hideMenuItems",{menuList:e.menuList},e)},showMenuItems:function(e){i("showMenuItems",{menuList:e.menuList},e)},hideAllNonBaseMenuItem:function(e){i("hideAllNonBaseMenuItem",{},e)},showAllNonBaseMenuItem:function(e){i("showAllNonBaseMenuItem",{},e)},scanQRCode:function(e){i("scanQRCode",{needResult:(e=e||{}).needResult||0,scanType:e.scanType||["qrCode","barCode"]},(e._complete=function(e){if(P){var n=e.resultStr;if(n){var i=JSON.parse(n);e.resultStr=i&&i.scan_code&&i.scan_code.scan_result}}},e))},openAddress:function(e){i(h.openAddress,{},(e._complete=function(e){e=a(e)},e))},openProductSpecificView:function(e){i(h.openProductSpecificView,{pid:e.productId,view_type:e.viewType||0,ext_info:e.extInfo},e)},addCard:function(e){for(var n=e.cardList,t=[],o=0,r=n.length;o<r;++o){var a=n[o],c={card_id:a.cardId,card_ext:a.cardExt};t.push(c)}i(h.addCard,{card_list:t},(e._complete=function(e){var n=e.card_list;if(n){for(var i=0,t=(n=JSON.parse(n)).length;i<t;++i){var o=n[i];o.cardId=o.card_id,o.cardExt=o.card_ext,o.isSuccess=!!o.is_succ,delete o.card_id,delete o.card_ext,delete o.is_succ}e.cardList=n,delete e.card_list}},e))},chooseCard:function(e){i("chooseCard",{app_id:C.appId,location_id:e.shopId||"",sign_type:e.signType||"SHA1",card_id:e.cardId||"",card_type:e.cardType||"",card_sign:e.cardSign,time_stamp:e.timestamp+"",nonce_str:e.nonceStr},(e._complete=function(e){e.cardList=e.choose_card_info,delete e.choose_card_info},e))},openCard:function(e){for(var n=e.cardList,t=[],o=0,r=n.length;o<r;++o){var a=n[o],c={card_id:a.cardId,code:a.code};t.push(c)}i(h.openCard,{card_list:t},e)},consumeAndShareCard:function(e){i(h.consumeAndShareCard,{consumedCardId:e.cardId,consumedCode:e.code},e)},chooseWXPay:function(e){i(h.chooseWXPay,r(e),e)},openEnterpriseRedPacket:function(e){i(h.openEnterpriseRedPacket,r(e),e)},startSearchBeacons:function(e){i(h.startSearchBeacons,{ticket:e.ticket},e)},stopSearchBeacons:function(e){i(h.stopSearchBeacons,{},e)},onSearchBeacons:function(e){t(h.onSearchBeacons,e)},openEnterpriseChat:function(e){i("openEnterpriseChat",{useridlist:e.userIds,chatname:e.groupName},e)},launchMiniProgram:function(e){i("launchMiniProgram",{targetAppId:e.targetAppId,path:g(e.path),envVersion:e.envVersion},e)},miniProgram:{navigateBack:function(e){e=e||{},f(function(){i("invokeMiniProgramAPI",{name:"navigateBack",arg:{delta:e.delta||1}},e)})},navigateTo:function(e){f(function(){i("invokeMiniProgramAPI",{name:"navigateTo",arg:{url:e.url}},e)})},redirectTo:function(e){f(function(){i("invokeMiniProgramAPI",{name:"redirectTo",arg:{url:e.url}},e)})},switchTab:function(e){f(function(){i("invokeMiniProgramAPI",{name:"switchTab",arg:{url:e.url}},e)})},reLaunch:function(e){f(function(){i("invokeMiniProgramAPI",{name:"reLaunch",arg:{url:e.url}},e)})},postMessage:function(e){f(function(){i("invokeMiniProgramAPI",{name:"postMessage",arg:e.data||{}},e)})},getEnv:function(n){f(function(){n({miniprogram:"miniprogram"===e.__wxjs_environment})})}}},b=1,R={};return S.addEventListener("error",function(e){if(!M){var n=e.target,i=n.tagName,t=n.src;if(("IMG"==i||"VIDEO"==i||"AUDIO"==i||"SOURCE"==i)&&-1!=t.indexOf("wxlocalresource://")){e.preventDefault(),e.stopPropagation();var o=n["wx-id"];if(o||(o=b++,n["wx-id"]=o),R[o])return;R[o]=!0,wx.ready(function(){wx.getLocalImgData({localId:t,success:function(e){n.src=e.localData}})})}}},!0),S.addEventListener("load",function(e){if(!M){var n=e.target,i=n.tagName;n.src;if("IMG"==i||"VIDEO"==i||"AUDIO"==i||"SOURCE"==i){var t=n["wx-id"];t&&(R[t]=!1)}}},!0),n&&(e.wx=e.jWeixin=N),N}});

(function (global) {
    global.mapleWx = mapleWx(global.wx);
    var margin = function(o,n){
        for (var p in n){
            if(n.hasOwnProperty(p))
                o[p]=n[p];
        }
        return o;
    };
    function mapleWx(wx) {
        'use strict';
        var mapleApi = new _mapleApi();
        var jsApiList = ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone', 'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice', 'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation', 'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem', 'closeWindow', 'scanQRCode', 'chooseWXPay', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard'];
        function _mapleApi() {
            var that = this;
            //微信接口初始化
            this.init = function (config, readFn, errorFn) {
                mapleApi.option.config = config;
                mapleApi.option.wx = wx;
                wx.config({
                    debug: config.debug || false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: config.appId, // 必填，公众号的唯一标识
                    timestamp: config.timestamp, // 必填，生成签名的时间戳
                    nonceStr: config.nonceStr, // 必填，生成签名的随机串
                    signature: config.signature,// 必填，签名，见附录1
                    jsApiList: config.jsApiList || jsApiList // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                });
                wx.ready(function () {
                    readFn && readFn.call(mapleApi);
                });
                wx.error(function (error) {
                    errorFn && errorFn.call(mapleApi, error);
                });
                return mapleApi;
            };

            //隐藏不安全接口
            that.hideNonSafetyMenuItem = function () {
                var list = ['menuItem:copyUrl', 'menuItem:delete', '', 'menuItem:originPage', 'menuItem:openWithQQBrowser', 'menuItem:openWithSafari', 'menuItem:share:email', 'menuItem:share:brand', 'menuItem:delete', 'menuItem:editTag'];
                that.hideMenuItems(list);
            };
            //一键配置所有分享
            that.onMenuShareAll = function(options,successFn,closeFn){

                //新增2个微信分享取消原来的4个
                that.updateAppMessageShareData(options,function(){
                    successFn && successFn('AppMessage');
                },function(){
                    closeFn && closeFn('AppMessage');
                });
                that.updateTimelineShareData(options,function(){
                    successFn && successFn('Timeline');
                },function(){
                    closeFn && closeFn('Timeline');
                });




                //新增的暂时无效，先用下面4个
                that.onMenuShareAppMessage(options,function(){
                    successFn && successFn('AppMessage');
                },function(){
                    closeFn && closeFn('AppMessage');
                });
                that.onMenuShareQQ(options,function(){
                    successFn && successFn('QQ');
                },function(){
                    closeFn && closeFn('QQ');
                });
                that.onMenuShareQZone(options,function(){
                    successFn && successFn('QZone');
                },function(){
                    closeFn && closeFn('QZone');
                });
                that.onMenuShareTimeline(options,function(){
                    successFn && successFn('Timeline');
                },function(){
                    closeFn && closeFn('Timeline');
                });


                that.onMenuShareWeibo(options,function(){
                    successFn && successFn('Weibo');
                },function(){
                    closeFn && closeFn('Weibo');
                });
            };
        };
        //拍照或从手机相册中选图接口
        _mapleApi.prototype.chooseImage = function (options, successFn) {
            options || (options = {});
            if (typeof(options) == 'function') {
                successFn = options;
                options = {};
            }
            wx.chooseImage({
                count: options.count || 1, // 默认9
                sizeType: options.sizeType || ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: options.sourceType || ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    successFn && successFn.call(mapleApi, localIds, res);
                },
                fail:function(err){
                }
            });
        };
        //预览图片接口
        _mapleApi.prototype.previewImage = function (current, urls) {
            wx.previewImage({
                current: current, // 当前显示图片的http链接
                urls: urls || [] // 需要预览的图片http链接列表
            });
        };
        //获取本地图片接口
        _mapleApi.prototype.getLocalImgData = function (localId, successFn) {
            wx.getLocalImgData({
                localId: localId, // 图片的localID
                success: function (res) {
                    var localData = res.localData; // localData是图片的base64数据，可以用img标签显示
                    successFn && successFn.call(mapleApi, localIds, res);
                }
            });
        };
        //上传图片接口
        _mapleApi.prototype.uploadImageOne = function (localId, successFn, isShowProgressTips) {
            wx.uploadImage({
                localId: localId, // 需要上传的图片的本地ID，由chooseImage接口获得
                isShowProgressTips: isShowProgressTips || 1, // 默认为1，显示进度提示
                success: function (res) {
                    var serverId = res.serverId; // 返回图片的服务器端ID
                    successFn && successFn.call(mapleApi, serverId, res);
                }
            });
        };
        //上传多张图片接口
        _mapleApi.prototype.uploadImage = function (localIds, successFn, errorFn) {
            // var _this = this,allFn=[];
            // localIds.forEach(function(localId,k){
            //     allFn.push(new Promise(function(resolve){
            //         _this.uploadImageOne(localId,function(serverId){
            //             return resolve(serverId);
            //         })
            //     }));
            // });
            // Promise.all(allFn).then(function(){
            //     var i = arguments.length,serverIdList = new Array(i);
            //     while(i--){serverIdList[i] = arguments[i];}
            //     successFn && successFn.call(mapleApi,serverIdList[0]);
            // }).catch(function(err){
            //     errorFn && errorFn.call(mapleApi,err,localIds);
            // });
            var serverIdList = [], length = localIds.length, _this = this;
            var _upload = function () {
                var localId = localIds[--length];
                if (!localId) return errorFn && errorFn.call(mapleApi, localIds, serverIdList);
                _this.uploadImageOne(localId, function (serverId) {
                    serverIdList.push(serverId);
                    length==0 ? successFn.call(mapleApi, serverIdList) : _upload();
                })
            };
            _upload();


        };
        //下载图片接口
        _mapleApi.prototype.downloadImage = function (serverId, successFn, isShowProgressTips) {
            wx.downloadImage({
                serverId: serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
                isShowProgressTips: isShowProgressTips || 1, // 默认为1，显示进度提示
                success: function (res) {
                    var localId = res.localId; // 返回图片下载后的本地ID
                    successFn && successFn.call(mapleApi, localId);
                }
            });
        };


        //开始录音接口
        _mapleApi.prototype.startRecord = function () {
            wx.startRecord.call(mapleApi);
        };
        //停止录音接口
        _mapleApi.prototype.stopRecord = function (successFn) {
            wx.stopRecord({
                success: function (res) {
                    var localId = res.localId;
                    successFn && successFn.call(mapleApi, localId, res);
                }
            });
        };
        //监听录音自动停止接口
        _mapleApi.prototype.onVoiceRecordEnd = function (completeFn) {
            wx.onVoiceRecordEnd({
                // 录音时间超过一分钟没有停止的时候会执行 complete 回调
                complete: function (res) {
                    var localId = res.localId;
                    completeFn && completeFn.call(mapleApi, localId, res);
                }
            });
        };
        //播放语音接口
        _mapleApi.prototype.playVoice = function (localId) {
            wx.playVoice({
                localId: localId // 需要播放的音频的本地ID，由stopRecord接口获得
            });
        };
        //暂停播放接口
        _mapleApi.prototype.pauseVoice = function (localId) {
            wx.pauseVoice({
                localId: localId // 需要暂停的音频的本地ID，由stopRecord接口获得
            });
        };
        //停止播放接口
        _mapleApi.prototype.stopVoice = function (localId) {
            wx.stopVoice({
                localId: localId // 需要停止的音频的本地ID，由stopRecord接口获得
            });
        };
        //监听语音播放完毕接口
        _mapleApi.prototype.onVoicePlayEnd = function (successFn) {
            wx.onVoicePlayEnd({
                success: function (res) {
                    var localId = res.localId; // 返回音频的本地ID
                    successFn && successFn.call(mapleApi, localId, res);
                }
            });
        };
        //上传语音接口
        _mapleApi.prototype.uploadVoice = function (localId, successFn, isShowProgressTips) {
            wx.uploadVoice({
                localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
                isShowProgressTips: isShowProgressTips || 1, // 默认为1，显示进度提示
                success: function (res) {
                    var serverId = res.serverId; // 返回音频的服务器端ID
                    successFn && successFn.call(mapleApi, serverId, res);
                }
            });
        };
        //下载语音接口
        _mapleApi.prototype.downloadVoice = function (serverId, successFn, isShowProgressTips) {
            wx.downloadVoice({
                serverId: serverId, // 需要下载的音频的服务器端ID，由uploadVoice接口获得
                isShowProgressTips: isShowProgressTips || 1, // 默认为1，显示进度提示
                success: function (res) {
                    var localId = res.localId; // 返回音频的本地ID
                    successFn && successFn.call(mapleApi, localId, res);
                }
            });
        };
        //识别音频并返回识别结果接口
        _mapleApi.prototype.translateVoice = function (localId, successFn, isShowProgressTips) {
            wx.translateVoice({
                localId: localId, // 需要识别的音频的本地Id，由录音相关接口获得
                isShowProgressTips: isShowProgressTips || 1, // 默认为1，显示进度提示
                success: function (res) {
                    successFn && successFn.call(mapleApi, res.translateResult, res);
                }
            });
        };

        //获取网络状态接口
        _mapleApi.prototype.getNetworkType = function (successFn) {
            wx.getNetworkType({
                success: function (res) {
                    successFn && successFn.call(mapleApi, res.networkType, res);
                }
            });
        };
        //使用微信内置地图查看位置接口
        _mapleApi.prototype.openLocation = function (options) {
            wx.openLocation({
                latitude: options.latitude || 0, // 纬度，浮点数，范围为90 ~ -90
                longitude: options.longitude || 0, // 经度，浮点数，范围为180 ~ -180。
                name: options.name || '', // 位置名
                address: options.address || '', // 地址详情说明
                scale: options.scale || 14, // 地图缩放级别,整形值,范围从1~28。默认为最大
                infoUrl: options.infoUrl || '' // 在查看位置界面底部显示的超链接,可点击跳转
            });
        };
        //获取地理位置接口
        _mapleApi.prototype.getLocation = function (successFn, type) {
            wx.getLocation({
                type: type || 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
//                        var speed = res.speed; // 速度，以米/每秒计
//                        var accuracy = res.accuracy; // 位置精度
                    successFn && successFn.call(mapleApi, latitude, longitude, res);
                }
            });
        };
        //开启查找周边ibeacon设备接口
        _mapleApi.prototype.startSearchBeacons = function (completeFn, ticket) {
            wx.startSearchBeacons({
                ticket: ticket || "",  //摇周边的业务ticket, 系统自动添加在摇出来的页面链接后面
                complete: function (argv) {
                    //开启查找完成后的回调函数
                    completeFn && completeFn.call(mapleApi, argv);
                }
            });
        };
        //关闭查找周边ibeacon设备接口
        _mapleApi.prototype.stopSearchBeacons = function (completeFn) {
            wx.stopSearchBeacons({
                complete: function (res) {
                    //关闭查找完成后的回调函数
                    completeFn && completeFn.call(mapleApi, res);
                }
            });
        };
        //监听周边ibeacon设备接口
        _mapleApi.prototype.onSearchBeacons = function (completeFn) {
            wx.onSearchBeacons({
                complete: function (argv) {
                    //回调函数，可以数组形式取得该商家注册的在周边的相关设备列表
                    completeFn && completeFn.call(mapleApi, argv);
                }
            });
        };
        //关闭当前网页窗口接口
        _mapleApi.prototype.closeWindow = function () {
            wx.closeWindow();
        };
        //批量隐藏功能按钮接口
        _mapleApi.prototype.hideMenuItems = function (menuList) {
            wx.hideMenuItems({
                menuList: menuList || [] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
            });
        };
        //批量显示功能按钮接口
        _mapleApi.prototype.showMenuItems = function (menuList) {
            wx.showMenuItems({
                menuList: menuList || [] // 要显示的菜单项，所有menu项见附录3
            });
        };
        //隐藏所有非基础按钮接口
        _mapleApi.prototype.hideAllNonBaseMenuItem = function () {
            wx.hideAllNonBaseMenuItem();
        };
        //显示所有功能按钮接口
        _mapleApi.prototype.showAllNonBaseMenuItem = function () {
            wx.showAllNonBaseMenuItem();
        };
        //调起微信扫一扫接口
        _mapleApi.prototype.scanQRCode = function (options, successFn) {
            options || (options = {});
            if (typeof(options) == 'function') {
                successFn = options;
                options = {};
            }
            wx.scanQRCode({
                needResult: options.needResult || 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: options.scanType || ["qrCode", "barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    successFn && successFn.call(mapleApi, result, res);
                }
            });
        };
        //跳转微信商品页接口
        _mapleApi.prototype.openProductSpecificView = function (productId, viewType) {
            wx.openProductSpecificView({
                productId: productId, // 商品id
                viewType: viewType || 0 // 0.默认值，普通商品详情页1.扫一扫商品详情页2.小店商品详情页
            });
        };
        //拉取适用卡券列表并获取用户选择信息
        _mapleApi.prototype.chooseCard = function (options, successFn) {
            wx.chooseCard({
                shopId: options.shopId, // 门店Id
                cardType: options.cardType, // 卡券类型
                cardId: options.cardId, // 卡券Id
                timestamp: options.timestamp, // 卡券签名时间戳
                nonceStr: options.nonceStr, // 卡券签名随机串
                signType: options.signType || 'SHA1', // 签名方式，默认'SHA1'
                cardSign: options.cardSign, // 卡券签名
                success: function (res) {
                    var cardList = res.cardList; // 用户选中的卡券列表信息
                    successFn && successFn.call(mapleApi, cardList, res);
                }
            });
        };
        //批量添加卡券接口
        _mapleApi.prototype.addCard = function (cardList, successFn) {
            wx.addCard({
                cardList: cardList, // 需要添加的卡券列表
                success: function (res) {
                    var cardList = res.cardList; // 添加的卡券列表信息
                    successFn && successFn.call(mapleApi, cardList, res);
                }
            });
        };
        //查看微信卡包中的卡券接口
        _mapleApi.prototype.openCard = function (cardList) {
            wx.openCard({
                cardList: cardList// 需要打开的卡券列表
            });
        };
        //发起一个微信支付请求
        _mapleApi.prototype.chooseWXPay = function (config, successFn,groupFn) {
            groupFn || (groupFn = {});

            margin(groupFn,{
                timestamp: parseInt(config.timestamp), // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                nonceStr: config.nonceStr, // 支付签名随机串，不长于 32 位
                package: config.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                signType: config.signType || 'SHA1', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                paySign: config.paySign, // 支付签名
                success: function (res) {
                    // 支付成功后的回调函数
                    successFn && successFn.call(mapleApi, res);
                }
            });
            wx.chooseWXPay(groupFn);
        };

        //新增2个接口以下4个接口即将废弃
        _mapleApi.prototype.updateAppMessageShareData = function (options, successFn, cancelFn) {
            options || (options = {});
            wx.onMenuShareTimeline({
                title: options.title || '', // 分享标题
                desc: options.desc || '', // 分享描述
                link: options.link || location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: options.imgUrl || '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };
        _mapleApi.prototype.updateTimelineShareData = function (options, successFn, cancelFn) {
            options || (options = {});
            wx.onMenuShareTimeline({
                title: options.title || '', // 分享标题
                link: options.link || location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: options.imgUrl || '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };


        //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
        _mapleApi.prototype.onMenuShareTimeline = function (options, successFn, cancelFn) {
            options || (options = {});
            wx.onMenuShareTimeline({
                title: options.title || '', // 分享标题
                link: options.link || location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: options.imgUrl || '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };
        //获取“分享给朋友”按钮点击状态及自定义分享内容接口
        _mapleApi.prototype.onMenuShareAppMessage = function (options, successFn, cancelFn) {
            options || (options = {});
            wx.onMenuShareAppMessage({
                title: options.title || '', // 分享标题
                desc: options.desc || '', // 分享描述
                imgUrl: options.imgUrl || '', // 分享图标
                link: options.link || location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                type: options.type || 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: options.dataUrl || '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };
        //获取“分享到QQ”按钮点击状态及自定义分享内容接口
        _mapleApi.prototype.onMenuShareQQ = function (options, successFn, cancelFn) {
            options || (options = {});
            wx.onMenuShareQQ({
                title: options.title || '', // 分享标题
                desc: options.desc || '', // 分享描述
                link: options.link || location.href, // 分享链接
                imgUrl: options.imgUrl || '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };
        //获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
        _mapleApi.prototype.onMenuShareWeibo = function (options, successFn, cancelFn) {
            options || (options = {});

            wx.onMenuShareWeibo({
                title: options.title || '', // 分享标题
                desc: options.imgUrl || '', // 分享描述
                link: options.imgUrl || location.href, // 分享链接
                imgUrl: options.imgUrl || '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };
        //获取“分享到QQ空间”按钮点击状态及自定义分享内容接口
        _mapleApi.prototype.onMenuShareQZone = function (options, successFn, cancelFn) {
            options || (options = {});
            wx.onMenuShareQZone({
                title: options.title || '', // 分享标题
                desc: options.desc || '', // 分享描述
                link: options.link || location.href, // 分享链接
                imgUrl: options.imgUrl || '', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    successFn && successFn.call(mapleApi);
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    cancelFn && cancelFn.call(mapleApi);
                }
            });
        };
        _mapleApi.prototype.option = {};

        return mapleApi.init;
    }
}(this));