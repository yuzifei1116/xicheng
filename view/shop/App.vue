<script>
	
	import {
		mapMutations
	} from 'vuex';
	export default {
		globalData:{
			online: true,
			userInfo:{
				now_money:0,
				integral:0,
			},
			ShareInfo:null,
			webUrl:'https://www.my.com',
		},
		
		onLaunch: function() {
			
		},
		onShow: function() {
			// console.log('App Show')
		},
		onHide: function() {
			// console.log('App Hide')
		},
		
		methods: {
			user_api(setShare,tit,des,img) {
				let self = this;
				let path = "/gzh/user_api/get_user_info_only";
				let data = {}
				getApp().http(path, data, function(result) {
					
					self.globalData.userInfo = result.data;
					
					if(setShare){
						console.log("返回值 ",result)
						let res = result.data.share
						let tit1 = tit ? tit : res.title
						let des1 = des ? des : res.descript
						let img1 = img ? img : res.img
						self.shareLink(tit1,des1,img1);
					}
				},true,'POST')
			},
			
			
			http(path, data, callback, needAll, md, header) {
				// var self = this
				uni.request({
					url: this.globalData.webUrl+ path,
					data: data,
					method: md ? md : "GET",
					header: {
						'Content-Type': 'application/x-www-form-urlencoded',
						// "X-Requested-With": "XMLHttpRequest"
					},
					success: (res) => {
						// console.log('请求请求',path, res)
						let result = res.data;
						let code = result.code;
						if (code == 200) {
							if (needAll) {
								callback(result)
							} else {
								callback(result.data)
							}

						} else if (code == 901 || code == 902 || code == 903) {
							if (getApp().globalData.online) {
								
								let sp_uid = this.getUrlParms('spuid');
								// let base =btoa(window.location.href);
								// console.log(aa);
								console.log('[[[[[[[[[[[[[[[[]]]]]]]]]]]]]]]]');
								
								 // window.location.href= this.webUrl+"/gzh/index/index?re_url="+base+'&spuid='+sp_uid;
							} else {
								console.log('未登录未登录未登录未登录')
								callback(901)
							}
						} else {
							if (needAll) {
								callback(result)
							} else {
								uni.showToast({
									title: result.msg,
									icon: "none",
								})
							}

						}

					},
					fail: (res) => {
						uni.showToast({
							title: path + "  网络问题",
							icon: "none",
						})
					}
				})
			},
			
			getUrlParms(name){
				let reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
				let r = window.location.search.substr(1).match(reg);
				if(r!=null)
					return unescape(r[2]);
				return null;
			},
			showTip(msg, icon, src) {
				uni.showToast({
					title: msg,
					icon: icon,
					image: src,
					duration: 2000,
					mask: true,
				})
			},
			
			shareLink(tit,desc,image) {
				
				let self = this;
				let uid = getApp().globalData.userInfo.uid;
				// console.log("dfdfdf fodjfd", uid)
				let path = "/gzh/auth_api/get_jsdk"
				let iconsrc = self.webUrl + "/gzh/shop/static/user-bg.jpg"
				if(image){
					iconsrc = self.webUrl + image
				}
				let dr = tit?tit:"商城";
				let rr = desc?desc:"商城";
				uni.request({
					url: self.webUrl + path,
					method: "GET",
					header: {
						"X-Requested-With": "XMLHttpRequest"
					},
					data: {
						url: btoa(window.location.href)
					},
					success: (res) => {
						console.log('分享----------------------')
						let jssdk = JSON.parse(res.data);
						console.log("分享的连接 == ", self.webUrl + "/gzh?spuid=" + uid + '/' + window.location.hash)
						mapleWx(jssdk, function() {
							this.onMenuShareAll({
									title: dr,
									desc: rr,
									imgUrl:iconsrc ,
									link: self.webUrl + "/gzh?spuid=" + uid + '/' + window.location.hash,
								},
								function(res) {
									console.log("分享成功", res)
								},
								function(err) {
									console.log("分享失败", err)
								}
							)
						})
			
					}
				})
			}
			
		},
		
	}
</script>

<style lang='scss'>
	/*
		全局公共样式和字体图标
	*/
   
   .acea-row{display:flex;flex-wrap:wrap;}
   /* 起点右 */
   .acea-row-reverse{display:flex;flex-direction:row-reverse;}
   /* 两端对齐，项目之间的间隔都相等 */
   .aces-space-between{display:flex;justify-content: space-between}
   .aces-center{display:flex;justify-content: center}
   .fcolor{color:#FA436A}
   
   
	@font-face {
		font-family: yticon;
		font-weight: normal;
		font-style: normal;
		src: url('https://at.alicdn.com/t/font_1078604_w4kpxh0rafi.ttf') format('truetype');
	}

	.yticon {
		font-family: "yticon" !important;
		font-size: 16px;
		font-style: normal;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}

	/* 已过期 */
	.icon-yiguoqi1:before {
		content: "\e700";
	}
	
	/* 删除 */
	.icon-iconfontshanchu1:before {
		content: "\e619";
	}
	
	/* 微信 */
	.icon-iconfontweixin:before {
		content: "\e611";
	}
	
	.icon-alipay:before {
		content: "\e636";
	}
	
	.icon-shang:before {
		content: "\e624";
	}
	
	/* 首页 */
	.icon-shouye:before {
		content: "\e626";
	}
	
	/* 删除u */
	.icon-shanchu4:before {
		content: "\e622";
	}
	
	/* 消息 */
	.icon-xiaoxi:before {
		content: "\e618";
	}
	
	/* 箭头下 */
	.icon-jiantour-copy:before {
		content: "\e600";
	}
	
	/* 分享 */
	.icon-fenxiang2:before {
		content: "\e61e";
	}
	
	.icon-pingjia:before {
		content: "\e67b";
	}
	
	/* 代付款 */
	.icon-daifukuan:before {
		content: "\e68f";
	}
	
	/* 评论 */
	.icon-pinglun-copy:before {
		content: "\e612";
	}
	
	/* 电话 */
	.icon-dianhua-copy:before {
		content: "\e621";
	}
	
	/* 收藏 */
	.icon-shoucang:before {
		content: "\e645";
	}
	
	/* 选中 */
	.icon-xuanzhong2:before {
		content: "\e62f";
	}
	
	/* 购物车 */
	.icon-gouwuche_:before {
		content: "\e630";
	}
	
	.icon-icon-test:before {
		content: "\e60c";
	}
	
	.icon-icon-test1:before {
		content: "\e632";
	}
	
	/* 编辑 */
	.icon-bianji:before {
		content: "\e646";
	}
	
	/* 加载 */
	.icon-jiazailoading-A:before {
		content: "\e8fc";
	}
	
	/* 箭头 左上角 */
	.icon-zuoshang:before {
		content: "\e613";
	}
	
	/* 加 */
	.icon-jia2:before {
		content: "\e60a";
	}
	
	/* 回复 */
	.icon-huifu:before {
		content: "\e68b";
	}
	
	/* 搜索 */
	.icon-sousuo:before {
		content: "\e7ce";
	}
	
	/*  箭头上 */
	.icon-arrow-fine-up:before {
		content: "\e601";
	}
	
	.icon-hot:before {
		content: "\e60e";
	}
	
	/* 历史记录 时间 */
	.icon-lishijilu:before {
		content: "\e6b9";
	}
	
	/* 支付宝 */
	.icon-zhengxinchaxun-zhifubaoceping-:before {
		content: "\e616";
	}
	
	/* 闹钟 */
	.icon-naozhong:before {
		content: "\e64a";
	}
	
	/* 首页 */
	.icon-xiatubiao--copy:before {
		content: "\e608";
	}
	
	/* 星星 */
	.icon-shoucang_xuanzhongzhuangtai:before {
		content: "\e6a9";
	}
	
	/* 加 */
	.icon-jia1:before {
		content: "\e61c";
	}
	
	/* 问号 */
	.icon-bangzhu1:before {
		content: "\e63d";
	}
	
	/* 箭头 左下 */
	.icon-arrow-left-bottom:before {
		content: "\e602";
	}
	/* 箭头 右下 */
	.icon-arrow-right-bottom:before {
		content: "\e603";
	}
	/* 箭头 左上 */
	.icon-arrow-left-top:before {
		content: "\e604";
	}
	
	/* 消息 */
	.icon-icon--:before {
		content: "\e744";
	}
	
	/* 箭头左 */
	.icon-zuojiantou-up:before {
		content: "\e605";
	}
	
	/* 下 */
	.icon-xia:before {
		content: "\e62d";
	}
	
	/* 减号 - */
	.icon--jianhao:before {
		content: "\e60b";
	}
	
	/* 微信 */
	.icon-weixinzhifu:before {
		content: "\e61a";
	}
	
	/* 消息 */
	.icon-comment:before {
		content: "\e64f";
	}
	
	/* 微信 */
	.icon-weixin:before {
		content: "\e61f";
	}
	
	/* 分类 */
	.icon-fenlei1:before {
		content: "\e620";
	}
	
	/* 存款 */
	.icon-erjiye-yucunkuan:before {
		content: "\e623";
	}
	
	/* 下载 */
	.icon-Group-:before {
		content: "\e688";
	}
	
	/* 右 */
	.icon-you:before {
		content: "\e606";
	}
	
	/* 右箭头 */
	.icon-forward:before {
		content: "\e607";
	}
	
	/* 推荐 */
	.icon-tuijian:before {
		content: "\e610";
	}
	
	/* 帮助 */
	.icon-bangzhu:before {
		content: "\e679";
	}
	
	/* 分享 */
	.icon-share:before {
		content: "\e656";
	}
	
	/* 已过期 */
	.icon-yiguoqi:before {
		content: "\e997";
	}
	
	/* 设置 */
	.icon-shezhi1:before {
		content: "\e61d";
	}
	
	/* 关闭 */
	.icon-fork:before {
		content: "\e61b";
	}
	
	/* 咖啡 */
	.icon-kafei:before {
		content: "\e66a";
	}
	
	/* 钻石标志 */
	.icon-iLinkapp-:before {
		content: "\e654";
	}
	
	/* 二维码 扫描 */
	.icon-saomiao:before {
		content: "\e60d";
	}
	
	/* 设置 */
	.icon-shezhi:before {
		content: "\e60f";
	}
	
	/* 售后退款 */
	.icon-shouhoutuikuan:before {
		content: "\e631";
	}
	
	/* 购物车 */
	.icon-gouwuche:before {
		content: "\e609";
	}
	
	/* 地址 */
	.icon-dizhi:before {
		content: "\e614";
	}
	
	/* 分类 */
	.icon-fenlei:before {
		content: "\e706";
	}
	
	/* 星星 */
	.icon-xingxing:before {
		content: "\e70b";
	}
	
	/* 团队 */
	.icon-tuandui:before {
		content: "\e633";
	}
	
	/* 钻石 */
	.icon-zuanshi:before {
		content: "\e615";
	}
	
	/* 左 */
	.icon-zuo:before {
		content: "\e63c";
	}
	
	/* 星星 */
	.icon-shoucang2:before {
		content: "\e62e";
	}
	
	/* 地址 */
	.icon-shouhuodizhi:before {
		content: "\e712";
	}
	
	/* 已收货 */
	.icon-yishouhuo:before {
		content: "\e71a";
	}
	
	/* 点赞 */
	.icon-dianzan-ash:before {
		content: "\e617";
	}





	view,
	scroll-view,
	swiper,
	swiper-item,
	cover-view,
	cover-image,
	icon,
	text,
	rich-text,
	progress,
	button,
	checkbox,
	form,
	input,
	label,
	radio,
	slider,
	switch,
	textarea,
	navigator,
	audio,
	camera,
	image,
	video {
		box-sizing: border-box;
	}
	/* 骨架屏替代方案 */
	.Skeleton {
		background: #f3f3f3;
		padding: 20upx 0;
		border-radius: 8upx;
	}

	/* 图片载入替代方案 */
	.image-wrapper {
		font-size: 0;
		background: #f3f3f3;
		border-radius: 4px;

		image {
			width: 100%;
			height: 100%;
			transition: .6s;
			opacity: 0;

			&.loaded {
				opacity: 1;
			}
		}
	}

	.clamp {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		display: block;
	}

	.common-hover {
		background: #f5f5f5;
	}

	/*边框*/
	.b-b:after,
	.b-t:after {
		position: absolute;
		z-index: 3;
		left: 0;
		right: 0;
		height: 0;
		content: '';
		transform: scaleY(.5);
		border-bottom: 1px solid $border-color-base;
	}

	.b-b:after {
		bottom: 0;
	}

	.b-t:after {
		top: 0;
	}

	/* button样式改写 */
	uni-button,
	button {
		height: 80upx;
		line-height: 80upx;
		font-size: $font-lg + 2upx;
		font-weight: normal;

		&.no-border:before,
		&.no-border:after {
			border: 0;
		}
	}

	uni-button[type=default],
	button[type=default] {
		color: $font-color-dark;
	}

	/* input 样式 */
	.input-placeholder {
		color: #999999;
	}

	.placeholder {
		color: #999999;
	}
</style>
