<template>  
    <view class="container">  
		
		<view class="user-section">
			<image class="bg" src="/static/user-bg.jpg"></image>
			<view class="user-info-box">
				<view class="portrait-box">
					<image class="portrait" :src="indexUrl(userInfo.avatar) || '/static/missing-face.png'"></image>
				</view>
				<view class="info-box">
					<text class="username">{{userInfo.nickname || '游客'}}</text>
				</view>
			</view>
			<view class="vip-card-box">
				<image class="card-bg" src="/static/vip-card-bg.png" mode=""></image>
				<!-- <view class="b-btn">
					立即开通
				</view> -->
				<view class="tit">
					<text class="yticon icon-iLinkapp-"></text>
					会员
				</view>
				<!-- <text class="e-m">DCloud Union</text>
				<text class="e-b">开通会员开发无bug 一测就上线</text> -->
			</view>
		</view>
		
		<view 
			class="cover-container"
			:style="[{
				transform: coverTransform,
				transition: coverTransition
			}]"
			@touchstart="coverTouchstart"
			@touchmove="coverTouchmove"
			@touchend="coverTouchend"
		>
			<image class="arc" src="/static/arc.png"></image>
			
			<view class="tj-sction">
				<view class="tj-item" @click="omney('/pages/user/user_money')">
					<text class="num">{{userInfo.now_money || 0}}</text>
					<text>余额</text>
				</view>
				<view class="tj-item" @click="coupon">
					<text class="num">{{userInfo.couponCount || 0}}</text>
					<text>优惠券</text>
				</view>
				<view class="tj-item"  @click="omney('/pages/user/user_integral')">
					<text class="num">{{userInfo.integral || 0}}</text>
					<text>积分</text>
				</view>
		</view>
		<!-- 订单 -->
		<view class="order-section">
			<view class="order-item"  @click="navToOrder('/pages/order/order?state=0')" hover-class="common-hover"  :hover-stay-time="50">
				<view class="ordernum" v-if="orderNum.unpaid_count">{{orderNum.unpaid_count}}</view>
				<view class="onum" v-else></view>
				<text class="yticon icon-shouye"></text>
				<text>待付款</text>
			</view>
			<view class="order-item" @click="navToOrder('/pages/order/order?state=1')"  hover-class="common-hover" :hover-stay-time="50">
				<view class="ordernum" v-if="orderNum.unshipped_count">{{orderNum.unshipped_count}}</view>
				<view class="onum" v-else></view>
				<text class="yticon icon-daifukuan"></text>
				<text>待发货</text>
			</view>
			<view class="order-item" @click="navToOrder('/pages/order/order?state=2')" hover-class="common-hover"  :hover-stay-time="50">
				<view class="ordernum" v-if="orderNum.received_count">{{orderNum.received_count}}</view>
				<view class="onum" v-else></view>
				<text class="yticon icon-yishouhuo"></text>
				<text>待收货</text>
			</view>
			<view class="order-item" @click="navToOrder('/pages/order/order?state=4')" hover-class="common-hover"  :hover-stay-time="50">
				<view class="onum" ></view>
				<text class="yticon icon-shouhoutuikuan"></text>
				<text>退款/售后</text>
			</view>
		</view>
		<!-- 浏览历史 -->
		<view class="history-section icon">
			<view class="sec-header">
				<text class="yticon icon-lishijilu"></text>
				<text>浏览历史</text>
			</view>
			<scroll-view scroll-x class="h-list ">
				<text v-for="(item,index) in storeVisit" :key="index" class="storsimg">
					<image @click="navTo(item)" :src="indexUrl(item.image)" mode="aspectFill"></image>
				</text>
				
			</scroll-view>
			
			<view v-for="(item,index) in MyNaviga" >
				<list-cell :icon="indexUrl(item.icon)" :url="item.url" :title="item.name" :tips="item.description"></list-cell>
			</view>
			<button class="aces-space-between kf-btn" hover-class="none" @click="phone">
				<view class="acea-row">
					<view>
						<image class="btn-img" src="../../static/5e130b55e5331.png"></image>
					</view>
					<view>
						<text class="btn-tit">联系客服</text>
					</view>
				</view>
				
				<text class="yticon icon-you btn-icon"></text>
			</button>
			
		</view>
	</view>
    </view>  
</template>  
<script>  
	import listCell from '@/components/mix-list-cell';
    import {  
        mapState 
    } from 'vuex';  
	let startY = 0, moveY = 0, pageAtTop = true;
    export default {
		components: {
			listCell
		},
		data(){
			return {
				coverTransform: 'translateY(0px)',
				coverTransition: '0s',
				moving: false,
				userInfo:[],
				storeVisit:[],
				MyNaviga:[],
				orderNum:[],
			}
		},
		onLoad(){
			getApp().user_api(true);
			// this.my();
			// this.getStoreVisit();
			this.getMyNaviga();
		},
		onShow(){
			this.getOrderData();
			this.my();
			this.getStoreVisit();
		},
		
		
		methods: {
			my(){
				let self = this;
				let path = '/gzh/user_api/my'
				getApp().http(path, {}, function(res) {
					console.log('个人信息',res)
					self.userInfo = res;
				})
			},
			getStoreVisit(){
				let self = this;
				let path = '/gzh/store_api/get_store_visit'
				getApp().http(path, {}, function(res) {
					self.storeVisit = res;
				})
			},
			getMyNaviga(){
				let self = this;
				let path = '/gzh/public_api/get_my_naviga'
				getApp().http(path, {}, function(res) {
					console.log('列表',res)
					self.MyNaviga = res;
				})
			},
			
			getOrderData(){
				let self = this;
				let path = '/gzh/auth_api/get_order_data'
				getApp().http(path, {}, function(res) {
					self.orderNum = res;
				})
			},
			
			phone(){
				
				uni.showModal({
					title: '客服电话',
					content: '是否拨打客服电话',
					success: (md) => {
						console.log('sssss')
						if(md.confirm == true){
							uni.makePhoneCall({
							    phoneNumber: '111' //仅为示例
							});
						}
					}
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
			omney(item){
				uni.navigateTo({
					url:item
				}) 
			},
			navTo(item){
				uni.navigateTo({  
					url:'/pages/product/product?id='+item.id
				})  
			}, 
			navToOrder(item){
				uni.navigateTo({
					url:item
				}) 
			},
			coupon(){
				uni.navigateTo({
					url:'/pages/user/coupon'
				}) 
			},
			
		// 	/**
		// 	 *  会员卡下拉和回弹
		// 	 *  1.关闭bounce避免ios端下拉冲突
		// 	 *  2.由于touchmove事件的缺陷（以前做小程序就遇到，比如20跳到40，h5反而好很多），下拉的时候会有掉帧的感觉
		// 	 *    transition设置0.1秒延迟，让css来过渡这段空窗期
		// 	 *  3.回弹效果可修改曲线值来调整效果，推荐一个好用的bezier生成工具 http://cubic-bezier.com/
		// 	 */
			coverTouchstart(e){
				if(pageAtTop === false){
					return;
				}
				this.coverTransition = 'transform .1s linear';
				startY = e.touches[0].clientY;
			},
			// 会员下拉 
			coverTouchmove(e){
				moveY = e.touches[0].clientY;
				let moveDistance = moveY - startY;
				if(moveDistance < 0){
					this.moving = false;
					return;
				}
				this.moving = true;
				if(moveDistance >= 80 && moveDistance < 100){
					moveDistance = 80;
				}
		
				if(moveDistance > 0 && moveDistance <= 80){
					this.coverTransform = `translateY(${moveDistance}px)`;
				}
			},
			coverTouchend(){
				if(this.moving === false){
					return;
				}
				this.moving = false;
				this.coverTransition = 'transform 0.3s cubic-bezier(.21,1.93,.53,.64)';
				this.coverTransform = 'translateY(0px)';
			}
		}, 
		   
		
		
		
		
		onNavigationBarButtonTap(e) {
			const index = e.index;
			if (index === 0) {
				// this.navTo('/pages/set/set');
				uni.navigateTo({
					url: '/pages/notice/notice'
				})
			}else if(index === 1){
				
				// uni.navigateTo({
				// 	url: '/pages/notice/notice'
				// })
			}
		},
        computed: {
			// ...mapState(['hasLogin','userInfo'])
		},
       
	}  
</script>  
<style lang='scss'>
	
	@import url("user.css");
	%flex-center {
	 display:flex;
	 flex-direction: column;
	 justify-content: center;
	 align-items: center;
	}
	%section {
	  display:flex;
	  justify-content: space-around;
	  align-content: center;
	  background: #fff;
	  border-radius: 10upx;
	}


	.ordernum{
		color:#fff;
		border-radius: 50upx;
		background:$base-color;
		position: relative;
		left:25upx;
		top:18upx;
		padding:2upx 12upx;
		font-size:20upx;
	}

	.user-section{
		height: 520upx;
		padding: 100upx 30upx 0;
		position:relative;
		.bg{
			position:absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			filter: blur(1px);
			opacity: .7;
		}
	}
	.user-info-box{
		height: 180upx;
		display:flex;
		align-items:center;
		position:relative;
		z-index: 1;
		.portrait{
			width: 130upx;
			height: 130upx;
			border:5upx solid #fff;
			border-radius: 50%;
		}
		.username{
			font-size: $font-lg + 6upx;
			color: $font-color-dark;
			margin-left: 20upx;
		}
	}

	.vip-card-box{
		display:flex;
		flex-direction: column;
		color: #f7d680;
		height: 240upx;
		background: linear-gradient(left, rgba(0,0,0,.7), rgba(0,0,0,.8));
		border-radius: 16upx 16upx 0 0;
		overflow: hidden;
		position: relative;
		padding: 20upx 24upx;
		.card-bg{
			position:absolute;
			top: 20upx;
			right: 0;
			width: 380upx;
			height: 260upx;
		}
		.b-btn{
			position: absolute;
			right: 20upx;
			top: 16upx;
			width: 132upx;
			height: 40upx;
			text-align: center;
			line-height: 40upx;
			font-size: 22upx;
			color: #36343c;
			border-radius: 20px;
			background: linear-gradient(left, #f9e6af, #ffd465);
			z-index: 1;
		}
		.tit{
			font-size: $font-base+2upx;
			color: #f7d680;
			margin-bottom: 28upx;
			.yticon{
				color: #f6e5a3;
				margin-right: 16upx;
			}
		}
		.e-b{
			font-size: $font-sm;
			color: #d8cba9;
			margin-top: 10upx;
		}
	}
	.cover-container{
		background: $page-color-base;
		margin-top: -150upx;
		padding: 0 30upx;
		position:relative;
		background: #f5f5f5;
		padding-bottom: 20upx;
		.arc{
			position:absolute;
			left: 0;
			top: -34upx;
			width: 100%;
			height: 36upx;
		}
	}
	.tj-sction{
		@extend %section;
		.tj-item{
			@extend %flex-center;
			flex-direction: column;
			height: 140upx;
			font-size: $font-sm;
			color: #75787d;
		}
		.num{
			font-size: $font-lg;
			color: $font-color-dark;
			margin-bottom: 8upx;
		}
	}
	.order-section{
		@extend %section;
		padding: 28upx 0upx 38upx 0upx;
		margin-top: 20upx;
		.order-item{
			@extend %flex-center;
			width: 120upx;
			height: 120upx;
			border-radius: 10upx;
			font-size: $font-sm;
			color: $font-color-dark;
		}
		.yticon{
			font-size: 48upx;
			margin-bottom: 18upx;
			/* color: #fa436a; */
			color:$base-color;
		}
		.icon-shouhoutuikuan{
			font-size:44upx;
		}
	}
	.history-section{
		padding: 30upx 0 0;
		margin-top: 20upx;
		background: #fff;
		border-radius:10upx;
		.sec-header{
			display:flex;
			align-items: center;
			font-size: $font-base;
			color: $font-color-dark;
			line-height: 40upx;
			margin-left: 30upx;
			.yticon{
				font-size: 44upx;
				color: #5eba8f;
				margin-right: 16upx;
				line-height: 40upx;
			}
		}
		.h-list{
			white-space: nowrap;
			padding: 30upx 30upx 0;
			.storsimg{
				/* border:1px solid red; */
				width: 160upx;
				image{
					display:inline-block;
					width: 160upx;
					height: 160upx;
					margin-right: 20upx;
					border-radius: 10upx;
				}
			}
			
		}
	}
	
</style>