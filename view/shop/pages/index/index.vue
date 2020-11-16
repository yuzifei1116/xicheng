<template>
	<view class="container">
		<!-- 小程序头部兼容 -->
		<!-- #ifdef MP -->
		<view class="mp-search-box">
			<input class="ser-input" type="text" value="输入关键字搜索" disabled />
		</view>
		<!-- #endif -->
		
		<view class="carousel-section">
			<view class="titleNview-placing"></view>
			<view class="titleNview-background" :style="{backgroundColor:titleNViewBackground}"></view>
			<!-- <swiper class="carousel" circular autoplay interval="3000" @change="swiperChange"> -->
				<swiper class="carousel" circular  @change="swiperChange">
				<swiper-item v-for="(item, index) in indexBanner" :key="index" class="carousel-item"  @click="adUrl(item)">
					<image :src="indexUrl(item.img)" />
				</swiper-item>
			</swiper>
			<view class="swiper-dots">
				<text class="num">{{swiperCurrent+1}}</text>
				<text class="sign">/</text>
				<text class="num">{{bannerLength}}</text>
			</view>
		</view>
		<view class="cate-section">
			<view v-for="(item, key) in menus" :key="key">
				<view class="cate-item" >
					<view class="cate-item2" @click="adUrl(item)">
						<image :src=" indexUrl(item.icon)"></image>
						<view class="cate-tit">{{item.name}}</view>
					</view>
					
				</view>
			</view>
			
			
		</view>
		
		<button @click="login"></button>
		
		
		<view class="not ">
			<view class="notice">
				<screenTextScroll :list="list"/>
			</view>
		</view>
		
		<view v-for="(item,key1) in ad1" :key ="key1">
			<view class="ad-1" @click="adUrl(item)">
				<image :src="indexUrl(item.img)" mode="scaleToFill"></image>
			</view >
		</view>
		
		<view class="f-header m-t">
			<image src="/static/temp/h1.png"></image>
			<view class="tit-box">
				<text class="tit">精品推荐</text>
				<text class="tit2">Boutique Recommendation</text>
			</view>
		</view>
		
		<view class="guess-section">
			<view 
				v-for="(item, index) in is_best" :key="index"
				class="guess-item"
				@click="navToDetailPage(item)"
			>
				<view class="image-wrapper">
					<image :src="indexUrl(item.image)" mode="aspectFill"></image>
				</view>
				<text class="title clamp">{{item.store_name}}</text>
				<text class="price">￥{{item.price}}</text>
			</view>
		</view>
		<view v-for="(item,key2) in ad2" :key ="key2">
			<view class="ad-1" @click="adUrl(item)">
				<image :src="indexUrl(item.img)" mode="scaleToFill"></image>
			</view>
		</view>
		
		<view class="f-header m-t">
			<image src="/static/temp/h1.png"></image>
			<view class="tit-box">
				<text class="tit">热门榜单</text>
				<text class="tit2">Competitive Products For You</text>
			</view>
		</view>
		<view class="hot-floor">
			<view class="floor-img-box">
				<image class="floor-img" :src="indexUrl(is_hot_img)" mode="scaleToFill"></image>
			</view>
			<view class="floor-list">
					<view class="scoll-wrapper">
						<view 
							v-for="(item, index) in is_hot" :key="index"
							class="floor-item"
							@click="navToDetailPage(item)"
						>
							<image :src="indexUrl(item.image) " mode="aspectFill"></image>
							<text class="title clamp">{{item.store_name}}</text>
							<text class="price">￥{{item.price}}</text>
						</view>
					</view>
			</view>
			
		</view>
		
		<!-- 广告位3 -->
		<view v-for="(item,key3) in ad3" :key ="key3">
			<view class="ad-1" @click="adUrl(item)">
				<image :src="indexUrl(item.img)" mode="scaleToFill"></image>
			</view>
		</view>
		
		<!-- 促销商品 -->
		<view class="f-header m-t">
			<image src="/static/temp/h1.png"></image>
			<view class="tit-box">
				<text class="tit">促销商品</text>
				<text class="tit2">Cheap And Fine Products For You</text>
			</view>
		</view>
		<view class="prom">
			<view 
				v-for="(item, index) in is_benefit" :key="index"
				class="prom-item"
				@click="navToDetailPage(item)"
			>
				<view class="prom-image">
					<image :src="indexUrl(item.image) " mode="aspectFill"></image>
				</view>
				<view class="prom-det">
					<text class="prom-title clamp">{{item.store_name}}</text>
					<view class="prom-price fcolor">￥{{item.price}}</view>
					<view class="prom-win aces-space-between">
						<view class="ypri">原价：{{item.ot_price}}</view>
						<view>
							仅剩：{{item.stock}}{{item.unit_name}}
						</view>
					</view>
					
				</view>
				
			</view>
		</view>
		
		<view v-for="(item,key4) in ad4" :key ="key4">
			<view class="ad-1" @click="adUrl(item)">
				<image :src="indexUrl(item.img)" mode="scaleToFill"></image>
			</view>
		</view>
	</view>
</template>

<script>
	import screenTextScroll from '@/components/p-screenTextScroll/screenTextScroll.vue';
	
	export default {
		components:{
			screenTextScroll
		},
		data() {
			return {
				titleNViewBackground: '',
				swiperCurrent: 0,
				swiperLength: 0,
				carouselList: [],
				goodsList: [],
				list:[],
				indexBanner:[],
				bannerLength:0,
				menus:[],
				ad1:[],
				ad2:null,
				ad3:null,
				ad4:null,
				is_best:[],
				is_hot:[],
				is_benefit:[],
				is_hot_img:'http://www.my.com/public/uploads/attach/2020/04/29/5ea91c68d6d26.jpg',
			};
		},

		onLoad() {
			// this.loadData();
			getApp().user_api(true);
			this.index();
			
			
		
			
			
		},
		methods: {
			login(){
				var self=this;
				uni.showLoading({
					mask:true,
					title: '正在登录···',
					complete:()=>{}
				});
				uni.login({
				  provider: 'weixin',
				  success: function (loginRes) {
					  console.log(loginRes,'成功成功成功成功成功成功11111')
					let js_code=loginRes.code;//js_code可以给后台获取unionID或openID作为用户标识
					// 获取用户信息
					uni.getUserInfo({
					  provider: 'weixin',
					  success: function (infoRes) {
						  
						  console.log(infoRes,'成功成功成功成功成功成功')
						  
						//infoRes里面有用户信息需要的话可以取一下
						let username=infoRes.userInfo.nickName;//用户名
						let gender=infoRes.userInfo.gender;//用户性别
						let formdata={code:js_code,username:username,sex:gender};
						//login是接口地址，看下面PHP代码
						// self.$go.post("/login",formdata).then(res=>{//这是我封装的请求方法
						// 	if(res.code==200){
						// 		//登录成功
						// 	}
						// })
					  },
					  fail:function(res){}
					})
				  },
				  fail:function(res){}
				})
			},
			indexUrl(icon){
				
				let http = icon.indexOf('http')
				if(http == -1){
					return this.webUrl +'/'+icon
				}else{
					return icon;
				}
			},
			index(){
				
				let self = this;
				let path = '/gzh/auth_api/index'
				getApp().http(path, {}, function(result) {
					self.indexBanner = result.banner
					self.bannerLength = self.indexBanner.length;
					self.titleNViewBackground = self.indexBanner[0].bg_color;
					self.menus = result.menus;
					self.list = result.roll
					self.ad1 = result.ad[1];
					self.ad2 = result.ad[2];
					self.ad3 = result.ad[3];
					self.ad4 = result.ad[4];
					self.is_best = result.is_best;
					self.is_hot = result.is_hot;
					
					self.is_benefit = result.is_benefit;
					self.is_hot_img = result.is_hot_img;
					
				})
			},
			adUrl(e){
				console.log('跳转跳转',e);
				window.location.href=e.url;
			},
			
			//轮播图切换修改背景色
			swiperChange(e) {
				let self = this;
				let index = e.detail.current;
				self.swiperCurrent = index;
				self.titleNViewBackground = self.indexBanner[index].bg_color;
			
			},
			//详情页
			navToDetailPage(item) {
				let id = item.id;
				
				console.log(item);
				console.log('详情')
				uni.navigateTo({
					url: `/pages/product/product?id=${id}`
				})
			},
		},
		
		onNavigationBarSearchInputConfirmed(e){
			console.log('点击了搜索',e)
			let res = e.text;
			uni.navigateTo({
				url: "/pages/product/list?keyword="+res
			})
			
		},
		
	}
</script>

<style lang="scss">
	
	@import url("index.css");
	
	/* #ifdef MP */
	.mp-search-box{
		position:absolute;
		left: 0;
		top: 30upx;
		z-index: 9999;
		width: 100%;
		padding: 0 80upx;
		.ser-input{
			flex:1;
			height: 56upx;
			line-height: 56upx;
			text-align: center;
			font-size: 28upx;
			color:$font-color-base;
			border-radius: 20px;
			background: rgba(255,255,255,.6);
		}
	}
	page{
		.cate-section{
			position:relative;
			z-index:5;
			border-radius:16upx 16upx 0 0;
			margin-top:-20upx;
		}
		.carousel-section{
			padding: 0;
			.titleNview-placing {
				padding-top: 0;
				height: 0;
			}
			.carousel{
				.carousel-item{
					padding: 0;
				}
			}
			.swiper-dots{
				left:45upx;
				bottom:40upx;
			}
		}
	}
	/* #endif */
	
	
	page {
		background: #f5f5f5;
	}
	.m-t{
		margin-top: 16upx;
	}
	/* 头部 轮播图 */
	.carousel-section {
		position: relative;
		padding-top: 10px;

		.titleNview-placing {
			height: var(--status-bar-height);
			padding-top: 44px;
			box-sizing: content-box;
		}

		.titleNview-background {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 426upx;
			transition: .4s;
		}
	}
	.carousel {
		width: 100%;
		height: 350upx;

		.carousel-item {
			width: 100%;
			height: 100%;
			padding: 0 28upx;
			overflow: hidden;
		}

		image {
			width: 100%;
			height: 100%;
			border-radius: 10upx;
		}
	}
	.swiper-dots {
		display: flex;
		position: absolute;
		left: 60upx;
		bottom: 15upx;
		width: 72upx;
		height: 36upx;
		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAYAAADDhn8LAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTMyIDc5LjE1OTI4NCwgMjAxNi8wNC8xOS0xMzoxMzo0MCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTk4MzlBNjE0NjU1MTFFOUExNjRFQ0I3RTQ0NEExQjMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTk4MzlBNjA0NjU1MTFFOUExNjRFQ0I3RTQ0NEExQjMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTcgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6Q0E3RUNERkE0NjExMTFFOTg5NzI4MTM2Rjg0OUQwOEUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6Q0E3RUNERkI0NjExMTFFOTg5NzI4MTM2Rjg0OUQwOEUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4Gh5BPAAACTUlEQVR42uzcQW7jQAwFUdN306l1uWwNww5kqdsmm6/2MwtVCp8CosQtP9vg/2+/gY+DRAMBgqnjIp2PaCxCLLldpPARRIiFj1yBbMV+cHZh9PURRLQNhY8kgWyL/WDtwujjI8hoE8rKLqb5CDJaRMJHokC6yKgSCR9JAukmokIknCQJpLOIrJFwMsBJELFcKHwM9BFkLBMKFxNcBCHlQ+FhoocgpVwwnv0Xn30QBJGMC0QcaBVJiAMiec/dcwKuL4j1QMsVCXFAJE4s4NQA3K/8Y6DzO4g40P7UcmIBJxbEesCKWBDg8wWxHrAiFgT4fEGsB/CwIhYE+AeBAAdPLOcV8HRmWRDAiQVcO7GcV8CLM8uCAE4sQCDAlHcQ7x+ABQEEAggEEAggEEAggEAAgQACASAQQCCAQACBAAIBBAIIBBAIIBBAIABe4e9iAe/xd7EAJxYgEGDeO4j3EODp/cOCAE4sYMyJ5cwCHs4rCwI4sYBxJ5YzC84rCwKcXxArAuthQYDzC2JF0H49LAhwYUGsCFqvx5EF2T07dMaJBetx4cRyaqFtHJ8EIhK0i8OJBQxcECuCVutxJhCRoE0cZwMRyRcFefa/ffZBVPogePihhyCnbBhcfMFFEFM+DD4m+ghSlgmDkwlOgpAl4+BkkJMgZdk4+EgaSCcpVX7bmY9kgXQQU+1TgE0c+QJZUUz1b2T4SBbIKmJW+3iMj2SBVBWz+leVfCQLpIqYbp8b85EskIxyfIOfK5Sf+wiCRJEsllQ+oqEkQfBxmD8BBgA5hVjXyrBNUQAAAABJRU5ErkJggg==);
		background-size: 100% 100%;

		.num {
			width: 36upx;
			height: 36upx;
			border-radius: 50px;
			font-size: 24upx;
			color: #fff;
			text-align: center;
			line-height: 36upx;
		}

		.sign {
			position: absolute;
			top: 0;
			left: 50%;
			line-height: 36upx;
			font-size: 12upx;
			color: #fff;
			transform: translateX(-50%);
		}
	}
	/* 分类 */
	.cate-section {
		display: flex;
		flex-wrap:wrap;
		padding: 30upx 22upx; 
		background: #fff;
		.cate-item {
			width: 140upx;
			/* width:200upx; */
			background: #fff;
			padding-top: 12upx;
			&:after{
				content: '';
				flex: 99;
				height: 0;
			}
		}
		
		.cate-item2{
			flex-shrink: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			flex-direction: column;
			font-size: 26upx;
			color: #666;
			padding-bottom: 20upx;
			.cate-tit{
				text-align: center;
			}
			
		}
		
		image {
			width: 90upx;
			height: 90upx;
			margin-bottom: 14upx;
			border-radius: 50%;
			opacity: .7;
			box-shadow: 4upx 4upx 20upx rgba(250, 67, 106, 0.3);
		}
	}
	.ad-1{
		width: 100%;
		padding:10upx 20upx 0upx 20upx;
		background: #fff;
		image{
			width:100%;
			height: 210upx; 
		}
	}
	/* 秒杀专区 */
	.seckill-section{
		padding: 4upx 30upx 24upx;
		background: #fff;
		.s-header{
			display:flex;
			align-items:center;
			height: 92upx;
			line-height: 1;
			.s-img{
				width: 140upx;
				height: 30upx;
			}
			.tip{
				font-size: $font-base;
				color: $font-color-light;
				margin: 0 20upx 0 40upx;
			}
			.timer{
				display:inline-block;
				width: 40upx;
				height: 36upx;
				text-align:center;
				line-height: 36upx;
				margin-right: 14upx;
				font-size: $font-sm+2upx;
				color: #fff;
				border-radius: 2px;
				background: rgba(0,0,0,.8);
			}
			.icon-you{
				font-size: $font-lg;
				color: $font-color-light;
				flex: 1;
				text-align: right;
			}
		}
		.floor-list{
			white-space: nowrap;
		}
		.scoll-wrapper{
			display:flex;
			align-items: flex-start;
		}
		.floor-item{
			width: 150upx;
			margin-right: 20upx;
			font-size: $font-sm+2upx;
			color: $font-color-dark;
			line-height: 1.8;
			image{
				width: 150upx;
				height: 150upx;
				border-radius: 6upx;
			}
			.price{
				color: $uni-color-primary;
			}
		}
	}
	
	.f-header{
		display:flex;
		align-items:center;
		height: 140upx;
		padding: 6upx 30upx 8upx;
		background: #fff;
		image{
			flex-shrink: 0;
			width: 80upx;
			height: 80upx;
			margin-right: 20upx;
		}
		.tit-box{
			flex: 1;
			display: flex;
			flex-direction: column;
		}
		.tit{
			font-size: $font-lg +2upx;
			color: #font-color-dark;
			line-height: 1.3;
		}
		.tit2{
			font-size: $font-sm;
			color: $font-color-light;
		}
		.icon-you{
			font-size: $font-lg +2upx;
			color: $font-color-light;
		}
	}
	/* 团购楼层 */
	.group-section{
		background: #fff;
		.g-swiper{
			height: 650upx;
			padding-bottom: 30upx;
		}
		.g-swiper-item{
			width: 100%;
			padding: 0 30upx;
			display:flex;
		}
		image{
			width: 100%;
			height: 460upx;
			border-radius: 4px;
		}
		.g-item{
			display:flex;
			flex-direction: column;
			overflow:hidden;
		}
		.left{
			flex: 1.2;
			margin-right: 24upx;
			.t-box{
				padding-top: 20upx;
			}
		}
		.right{
			flex: 0.8;
			flex-direction: column-reverse;
			.t-box{
				padding-bottom: 20upx;
			}
		}
		.t-box{
			height: 160upx;
			font-size: $font-base+2upx;
			color: $font-color-dark;
			line-height: 1.6;
		}
		.price{
			color:$uni-color-primary;
		}
		.m-price{
			font-size: $font-sm+2upx;
			text-decoration: line-through;
			color: $font-color-light;
			margin-left: 8upx;
		}
		.pro-box{
			display:flex;
			align-items:center;
			margin-top: 10upx;
			font-size: $font-sm;
			color: $font-base;
			padding-right: 10upx;
		}
		.progress-box{
			flex: 1;
			border-radius: 10px;
			overflow: hidden;
			margin-right: 8upx;
		}
	}
	.hot-floor{
		width: 100%;
		overflow: hidden;
		margin-bottom: 20upx;
		.floor-img-box{
			width: 100%;
			height:320upx;
			position:relative;
			&:after{
				content: '';
				position:absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				background: linear-gradient(rgba(255,255,255,.06) 30%, #f8f8f8);
			}
		}
		.floor-img{
			width: 100%;
			height: 100%;
		}
		.floor-list{
			white-space: nowrap;
			padding: 20upx;
			border-radius: 10upx;
			margin-top:-140upx;
			margin-left: 30upx;
			margin-right:30rpx;
			background: #fff;
			box-shadow: 1px 1px 5px rgba(0,0,0,.2);
			position: relative;
			z-index: 1;
		}
		.scoll-wrapper{
			display:flex;
			flex-wrap:wrap;
			padding-top:10upx;
		}
		.floor-item{
			width: 195upx;
			margin:0upx 10upx;
			font-size: $font-sm+2upx;
			color: $font-color-dark;
			line-height: 1.8;
			image{
				width: 195upx;
				height: 195upx;
				border-radius: 6upx;
			}
			.price{
				color: $uni-color-primary;
			}
		}
		.more{
			display:flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			flex-shrink: 0;
			width: 180upx;
			height: 180upx;
			border-radius: 6upx;
			background: #f3f3f3;
			font-size: $font-base;
			color: $font-color-light;
			text:first-child{
				margin-bottom: 4upx;
			}
		}
	}
	/* 猜你喜欢 */
	.guess-section{
		display:flex;
		flex-wrap:wrap;
		padding: 0 30upx;
		background: #fff;
		margin-bottom:15upx;
		.guess-item{
			display:flex;
			flex-direction: column;
			width: 48%;
			padding-bottom: 40upx;
			&:nth-child(2n+1){
				margin-right: 4%;
			}
		}
		.image-wrapper{
			width: 100%;
			height: 330upx;
			border-radius: 3px;
			overflow: hidden;
			image{
				width: 100%;
				height: 100%;
				opacity: 1;
			}
		}
		.title{
			font-size: $font-lg;
			color: $font-color-dark;
			line-height: 80upx;
		}
		.price{
			font-size: $font-lg;
			color: $uni-color-primary;
			line-height: 1;
		}
	}
	

</style>
