<template>
	<view>
		<view class="ordsta ">
			{{cartInfo._status._title}}
			
			<!-- <view v-if="cartInfo.status == 0">
				<view  class="ordimg aces-center">
					<image class="ordimage" src="../../static/select.png"></image>
					<view class="ordtit">待发货</view>
				</view>
			</view>
			<view v-else-if="cartInfo.status == 1">
				<view class="ordimg aces-center">
					<image class="ordimage" src="../../static/checked.png"></image>
					<view class="ordtit1">待收货</view>
				</view>
			</view>
			<view v-else-if="cartInfo.status == 2">
				<view class="ordimg aces-center">
					<image class="ordimage" src="../../static/selected.png"></image>
					<view class="ordtit2">已完成</view>
				</view>
			</view> -->
			
		</view>
		<view  class="address-section">
			<view class="order-content">
				<view class="cen">
					<view class="">
						{{cartInfo.real_name}} {{cartInfo.user_phone}}
					</view>
					<view class="dz">
						{{cartInfo.user_address}}
					</view>
				</view>
			</view>
		</view>
		<image class="a-bg" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAu4AAAAFCAYAAAAaAWmiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Rjk3RjkzMjM2NzMxMTFFOUI4RkU4OEZGMDcxQzgzOEYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Rjk3RjkzMjQ2NzMxMTFFOUI4RkU4OEZGMDcxQzgzOEYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpGOTdGOTMyMTY3MzExMUU5QjhGRTg4RkYwNzFDODM4RiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpGOTdGOTMyMjY3MzExMUU5QjhGRTg4RkYwNzFDODM4RiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PrEOZlQAAAiuSURBVHjazJp7bFvV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"></image>
		<view class="dtops" v-if="cartInfo.combination_id > 0 && cartInfo._status._type !=0">
			<view class="acea-row">
				<image class="jdimg" src="../../static/x5.png"></image>
				<view class="jdtit">参团详情</view>
			</view>
			<cmd-progress :percent="infoNum":showInfo="false" stroke-color="linear-gradient(to right, #FFA932, #FF4B67)"></cmd-progress>
			<view class="jdy">已参团：<text class="jdnum">{{cartInfo._pink_info.has_people}}</text>人/需{{cartInfo._pink_info.need_people}}人</view>
		</view>
		<view class="dtops acea-row dd">
			<view v-for="(item,index) in people_list" :key="index">
				<view class="tuan">
					<view><image class="timg" :src="indexUrl(item.avatar)"></image></view>
					<view v-if="index == 0">
						<view class="uname acea-row">团长 </view>
					</view>
				</view>
			</view>
		</view>
		
		
		
		<view class="goods-section">
			<view class="g-header b-b">
				<text class="name">共{{cartInfo.total_num}}件商品</text>
			</view>
			<!-- 商品列表 -->
			<view v-for="(item,index) in cartInfo.cartInfo" :key="index">
				<view class="g-item">
					<image :src="indexUrl(item.productInfo.image)"></image>
					<view class="right">
						<text class="title clamp">{{item.productInfo.store_name}}</text>
						<text class="spec">{{item.productInfo.attrInfo?item.productInfo.attrInfo.suk :''}}</text>
						<view class="price-box">
							<text class="price">￥{{item.truePrice}}</text>
							<text class="number">x {{item.cart_num}}</text>
						</view>
					</view>
					<view class="eval" v-if="cartInfo._status._type == 3" @click="orderpj(item.unique)">评价</view>
				</view>
			</view>
		</view>
		<!--  -->
		<view class="yt-list">
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">订单编号:</text>
				<text class="cell-tip">{{cartInfo.order_id}}</text>
			</view>
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">下单时间:</text>
				<text class="cell-tip ">{{cartInfo.add_time_y +' '+cartInfo.add_time_h}}</text>
			</view>
			<view v-if="cartInfo.presale_id ==0">
				<view class="yt-list-cell b-b">
					<text class="cell-tit clamp">支付状态:</text>
					<text class="cell-tip red" v-if="cartInfo.paid">已支付</text>
					<text class="cell-tip " v-else>未支付</text>
				</view>
			</view>
			<view v-if="cartInfo.paid > 0">
				<view class="yt-list-cell b-b" >
					<text class="cell-tit clamp">支付方式:</text>
					<text class="cell-tip">{{cartInfo._status._payType}}</text>
				</view>
			</view>
			<view class="yt-list-cell b-b" v-if="cartInfo.mark">
				<text class="cell-tit clamp">买家留言:</text>
				<text class="cell-tip">{{cartInfo.mark}}</text>
			</view>
			
		</view>
		
		<view class="yt-list" v-if='cartInfo.delivery_type=="express"'>
			<view class="yt-list-cell b-b" >
				<text class="cell-tit clamp">快递公司:</text>
				<text class="cell-tip">{{cartInfo.delivery_name}}</text>
			</view>
			<view class="yt-list-cell b-b" >
				<text class="cell-tit clamp">快递单号:</text>
				<text class="cell-tip">{{cartInfo.delivery_id}}</text>
				<text @click="copy" class="copy">复制</text>
			</view>
		</view>
		
		<view class="yt-list">
			<view v-if="cartInfo.presale_id ==0">
				<view class="yt-list-cell b-b">
					<text class="cell-tit clamp">支付金额:</text>
					<text class="cell-tip">￥{{cartInfo.total_price}}</text>
				</view>
				<view class="yt-list-cell b-b"  v-if='cartInfo.deduction_price !=0'>
					<text class="cell-tit clamp">积分抵扣:</text>
					<text class="cell-tip">-￥{{cartInfo.deduction_price}}</text>
				</view>
				<view class="yt-list-cell b-b"  v-if='cartInfo.coupon_id'>
					<text class="cell-tit clamp">优惠券抵扣:</text>
					<text class="cell-tip">-￥{{cartInfo.coupon_price}}</text>
				</view>
				<view class="yt-list-cell b-b"  v-if='cartInfo.total_postage >0'>
					<text class="cell-tit clamp">运费:</text>
					<text class="cell-tip">￥{{cartInfo.total_postage}}</text>
				</view>
				<view class="yt-list-cell b-b">
					<text class="cell-tit clamp">实付款:</text>
					<text class="cell-tip red">￥{{cartInfo.pay_price}}</text>
				</view>
			</view>
			<view v-else>
				
				<view v-if="cartInfo.paid > 0">
				
					<view class="yt-list-cell b-b">
						<text class=" clamp blod">支付尾款:{{cartInfo.cartInfo[0].productInfo.wk_start_time}}至{{cartInfo.cartInfo[0].productInfo.wk_stop_time}}</text>
					</view>
					<view class="yt-list-cell b-b">
						<text class="cell-tit clamp">已付定金:</text>
						<text class="cell-tip">￥{{cartInfo.dingjin}}</text>
					</view>
					<view class="yt-list-cell b-b">
						<text class="cell-tit clamp">
							<text v-if="cartInfo.presale_paid ==0">待付尾款:</text>
							<text v-else>已付尾款:</text>
						</text>
						<text class="cell-tip">￥{{cartInfo.weikuan}}</text>
					</view>
					<view class="yt-list-cell b-b">
						<text class="cell-tit clamp">实付款:</text>
						<text class="cell-tip red">￥{{cartInfo.pay_price}}</text>
					</view>
				</view>
				<view v-else>
					<view class="yt-list-cell b-b">
						<text class="cell-tit clamp">定金:</text>
						<text class="cell-tip">￥{{cartInfo.pay_price}}</text>
					</view>
				</view>
				
			</view>
				
				
		</view>


		<!-- 底部 -->
		<view class="footer acea-row-reverse" v-if="cartInfo.paid == 0">
			<view>
				<button class="submit" form-type="submit" @click="toggleMask('show')">立即付款</button>
			</view>
		</view>
		<view class="footer acea-row-reverse" v-if="cartInfo._status._type == 2">
			<view>
				<button class="submit" form-type="submit" @click="orderCon()">确认收货</button>
			</view>
		</view>

		<view class="footer acea-row-reverse" v-if="cartInfo.seckill_id == 0 && cartInfo.combination_id == 0 && cartInfo.presale_id == 0">
			<view>
				<button class="submit" form-type="submit" @click="ordert(cartInfo)">申请退款</button>
			</view>
		</view>

		
		<view class="footer acea-row-reverse" v-if="cartInfo._status._type == 4">
			<!-- <view>
				<button class="submit" form-type="submit" @click="goOrderConfirm">再次购买</button>
			</view> -->
			<view>
				<button class="del" form-type="submit" @click="delOrder">删除订单</button>
			</view>
		</view>
		
		
		<view class="footer acea-row-reverse" v-if="cartInfo.paid == 0">
			<view >
				<button class="submit" form-type="submit" @click="toggleMask('show')">支付定金</button>
			</view>
		</view>
		<view v-else>
			<view class="footer acea-row-reverse" v-if="cartInfo.presale_id !=0 && cartInfo.presale_paid ==0">
				<view v-if="isTime == 1">
					<button class="submit" form-type="submit" >未到支付时间</button>
				</view>
				<view v-else-if="isTime == 2">
					<button class="submit" form-type="submit" @click="toggleMask('show')">支付尾款</button>
				</view>
				<view v-else-if="isTime == 3">
					<button class="submit" form-type="submit">已过期</button>
				</view>
				
			</view>
		</view>
		
		
		
		<view class="mask" :class="maskState===0 ? 'none' : maskState===1 ? 'show' : ''" @click="toggleMask">
			<view class="mask-content" >
				<view class="wxzf" @click="clickPay('weixin')">微信支付</view>
				<view class="wxzf" @click="clickPay('yue')">余额支付</view>
				<view class="qx">取消</view>
			</view>
		</view>
		

	</view>
</template>

<script>
	import h5Copy from '@/components/junyi-h5-copy.js'
	import cmdProgress from '@/components/cmd-progress';
	export default {
		components: {
			cmdProgress
		},
		data() {
			return {
				uni:'',
				cartInfo: [],
				userInfo:[],
				maskState: 0,
				now_money:0,
				infoNum:0,
				people_list:[],
				isTime:0,
			}
		},
		onLoad(option) {
			//商品数据
			console.log('啊啊啊啊', option);
			getApp().user_api(true);
			this.uni = option.uni
		},
		onShow() {
			this.confirm_order();
		},
		methods: {
			confirm_order() {
				let self = this;
				let path = '/gzh/user_api/get_order'
				let data = {
					uni: self.uni
				}
				getApp().http(path, data, function(res) {
					uni.hideLoading();
					self.cartInfo = res;
					self.userInfo = getApp().globalData.userInfo
					self.now_money = getApp().globalData.userInfo.now_money
					
					self.infoNum = 100 / self.cartInfo._pink_info.need_people * self.cartInfo._pink_info.has_people
					
					self.people_list = res._pink_info.people_list;
					
					
					// 开始
					let str = res.cartInfo[0].productInfo.wk_start_time;
					let date = new Date(str); 
					let time = Date.parse(date);
					let wk_start_time = time / 1000;
					// 结束
					let stop = res.cartInfo[0].productInfo.wk_stop_time;
					let dates = new Date(stop); 
					let stop_time = Date.parse(dates);
					let wk_stop_time = stop_time / 1000;
					// 当前
					let not_time = new Date()
					let n_time =  Date.parse(not_time);
					let not_times = n_time / 1000;
					
					if(not_times < wk_start_time){
						console.log('未到尾款支付时间')
						self.isTime = 1;
						
					}else if(not_times > wk_stop_time){
						self.isTime = 3;
						console.log('尾款支付已过期')
					}else{
						self.isTime = 2;
						console.log('支付尾款')
					}
					
					console.log('结束时间',wk_stop_time,not_times);
					
					
					
				})
			},
			clickPay(i){
				uni.showLoading({})
				let self = this;
				console.log(self.now_money < self.cartInfo.pay_price);
				console.log('水电费水电费',i,self.now_money,self.cartInfo.pay_price)
				
				if (i == 'yue' && self.now_money < self.cartInfo.pay_price) {
					getApp().showTip("余额不足", "none")
					return false;
				}
				
				let path = '/gzh/deal_api/pay_order';
				let data = {
					uni:self.cartInfo.order_id,
					paytype:i
					};
				getApp().http(path, data, function(res) {
					console.log('支付',res);
					if(res.code == 200){
						if(res.data == ''){
							uni.hideLoading();
							getApp().showTip(res.msg, "none")
							uni.showLoading({})
							self.confirm_order();
							console.log('余额支付撒大声地');
							
						}else{
							uni.hideLoading();
							
							let	jsConfig = res.data ? res.data.result.jsConfig :''
							console.log("创建订单返回2", jsConfig)
							
							let path = '/gzh/auth_api/get_jsdk';
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
									console.log('微信支付')
									let jssdk = JSON.parse(res.data);
									console.log('微信支付11',jssdk)
									mapleWx(jssdk, function() {
										this.chooseWXPay(
											jsConfig,
											function() {
												console.log("执行着了么#####")
												uni.hideLoading();
												getApp().showTip(res.msg, "none")
												uni.showLoading({})
												self.confirm_order();
											}, {
												fail: function(res) {
													console.log("失败", res)
													
													uni.showToast({
														title: "支付失败"
													})
													window.history.back(-1);
													
												},
												cancel: function() {
													uni.showToast({
														title: "您已取消支付"
													})
													window.history.back(-1);
												},
												success: function() {
													console.log("成功过过")
													window.history.back(-1);
												}
													
										});
									})
													
								}
							})
							
						}
					}else{
						getApp().showTip(res.msg, "none")
					}
				},true,'post')
				
			},
			
			orderpj(unique){
				uni.navigateTo({
					url: "/pages/order/orderEval?unique="+unique
				})

			},
			
			ordert(item){
				console.log('支付',item)
				uni.navigateTo({
					url: "/pages/order/orderT?uni="+item.order_id
				})
			},
			
			// 确认收货
			orderCon(){
				let self = this;
				uni.showModal({
					title: '确认收货',
					content: '为保障权益，请收到货确认无误后，再确认收货',
					success: (md) => {
						console.log('sssss')
						if(md.confirm == true){
							let path = '/gzh/user_api/user_take_order'
							let data = {
								uni: self.cartInfo.order_id
							}
							getApp().http(path, data, function(res) {
								uni.hideLoading();
								if(res.code == 200){
									getApp().showTip(res.msg, "success")
									self.confirm_order();
								}else{
									getApp().showTip(res.msg, "none")
								}
							},true)
						}
					}
				})
				
				
				
			},
			
			copy() {
				let content = this.cartInfo.delivery_id 
				const result = h5Copy(content)
				if (result === false) {
					uni.showToast({
						title:'不支持',
					})
				} else {
					uni.showToast({
						title:'复制成功',
						icon:'none'
					})
				}
			},
			
			// 再次购买
			goOrderConfirm(){
				let self = this;
				let path = '/gzh/user_api/get_order'
				let data = {
					uni: self.uni
				}
				getApp().http(path, data, function(res) {
					
					let cartArr = res.cart_id
					let cartId = cartArr.toString();
					console.log(res,cartId);
					
					uni.navigateTo({
						url: "/pages/order/createOrder?cartId="+res.cart_id
					})
				})
			},
			
			// 删除订单
			delOrder(){
				
				uni.showModal({
					title: '删除',
					content: '确定删除此订单吗？',
					success: (md) => {
						console.log('sssss')
						uni.showLoading()
						if(md.confirm == true){
							let self = this;
							let path = '/gzh/user_api/user_remove_order'
							let data = {
								uni: self.uni
							}
							getApp().http(path, data, function(res) {
								uni.hideLoading();
								if(res.code == 200){
									uni.showToast({
									    title: res.msg,
									    duration: 1500,
										success(){
											setTimeout(function(){
												uni.navigateBack(3)
											},1500)
										},
									});
								}else{
									getApp().showTip(res.msg, "none")
								}
								
							},true);
						}
					}
				})

				
				
			},
			
			indexUrl(icon) {
				let http = icon.indexOf('http')
				if (http == -1) {
					return this.webUrl + '/' + icon
				} else {
					return icon;
				}
			},
			//显示面板
			toggleMask(type) {
				let timer = type === 'show' ? 10 : 300;
				let state = type === 'show' ? 1 : 0;
				this.maskState = 2;
				setTimeout(() => {
					this.maskState = state;
				}, timer)
			},

			
		}
	}
</script>

<style lang="scss">
	page {
		background: $page-color-base;
		padding-bottom: 100upx;
	}
	
	.blod{
		font-weight: bold;
		color:#000;
		font-size: 24upx;
	}
	
	/* 进度条 */
	.dtops{
		padding:30upx 30upx 10upx 30upx;
		background: #fff;
		// margin-top:20upx;
	}
	.jdimg{
		width:35upx;
		height:30upx;
	}
	.jdtit{
		font-size:30upx;
		margin-left:15upx;
	}
	.jdy{
		font-size: 31upx;
		color:#797979;
	}
	.jdnum{
		color:$base-color;
	}
	.tuan{
		padding:15upx;
	}
	.dd{
		padding-bottom: 40upx;
	}
	.timg{
		width:100upx;
		height:100upx;
		border-radius: 50%;
	}
	.uname{
		width:90upx;
		height:35upx;
		border-radius: 50px;
		margin-top:-20upx;
		background-image: linear-gradient(to right,#FF056B 0%,#FD5E49 100%);
		display: flex;
		align-items: center;
		justify-content: center;
		position: absolute;
		color:#fff;
		font-size:26upx;
		z-index: 999;
	}
	
	.eval{
		font-size:28upx;
		border:1px solid $base-color;
		height:40upx;
		width:80upx;
		color:$base-color;
		text-align: center;
	}
	.copy{
		font-size: 25upx;
		color:$base-color;
		// border:1px solid #FA436A;
		// height:30upx;
		margin-left:10upx;
	}
	
	/* 订单状态 */
	.dz{
		font-size:26upx;
		color:#6F6F6F;
	}
	.ordsta{
		// border:1px solid red;
		background:#fff;
		margin-bottom:20upx;
		padding:30upx 20upx;
		font-weight: bold;
		text-align: center;
		color:$base-color;
	}
	.ordimg{
		// color:#7E7E7E
	}
	.ordimage{
		width:44upx;
		height:44upx;
	}
	.ordtit{
		margin-top:-5upx;
		margin-left:5upx;
		color:#7E7E7E
	}

	.ordtit1{
		margin-top:-5upx;
		margin-left:5upx;
		color:#43A639
	}
	
	.ordtit2{
		margin-top:-5upx;
		margin-left:5upx;
		color:#FA436A
	}

	.address-section {
		padding: 30upx 20upx;
		background: #fff;
		position: relative;
		// border:1px solid red;
		font-size:30upx;
	}

	.a-bg {
		display: block;
		width: 100%;
		height: 5upx;
	}

	.goods-section {
		margin-top: 16upx;
		background: #fff;
		padding-bottom: 1px;

		.g-header {
			display: flex;
			align-items: center;
			height: 84upx;
			padding: 0 30upx;
			position: relative;
		}

		.logo {
			display: block;
			width: 50upx;
			height: 50upx;
			border-radius: 100px;
		}

		.name {
			font-size: 30upx;
			color: $font-color-base;
			margin-left: 24upx;
		}

		.g-item {
			display: flex;
			margin: 20upx 30upx;

			image {
				flex-shrink: 0;
				display: block;
				width: 140upx;
				height: 140upx;
				border-radius: 4upx;
			}

			.right {
				flex: 1;
				padding-left: 24upx;
				overflow: hidden;
			}

			.title {
				font-size: 30upx;
				color: $font-color-dark;
				// color: red
			}

			.spec {
				font-size: 26upx;
				color: $font-color-light;
			}

			.price-box {
				display: flex;
				align-items: center;
				font-size: 32upx;
				color: $font-color-dark;
				padding-top: 10upx;

				.price {
					margin-bottom: 4upx;
				}

				.number {
					font-size: 26upx;
					color: $font-color-base;
					margin-left: 20upx;
				}
			}

			.step-box {
				position: relative;
			}
		}
	}

	.yt-list {
		margin-top: 16upx;
		background: #fff;
	}

	.yt-list-cell {
		display: flex;
		align-items: center;
		padding: 10upx 30upx 10upx 40upx;
		line-height: 70upx;
		position: relative;

		&.cell-hover {
			background: #fafafa;
		}

		&.b-b:after {
			left: 30upx;
		}

		.cell-icon {
			height: 32upx;
			width: 32upx;
			font-size: 22upx;
			color: #fff;
			text-align: center;
			line-height: 32upx;
			background: #f85e52;
			border-radius: 4upx;
			margin-right: 12upx;

			&.hb {
				background: #ffaa0e;
			}

			&.lpk {
				background: #3ab54a;
			}

		}

		.cell-more {
			align-self: center;
			font-size: 24upx;
			color: $font-color-light;
			margin-left: 8upx;
			margin-right: -10upx;
		}

		.cell-tit {
			flex: 1;
			font-size: 26upx;
			color: $font-color-light;
			margin-right: 10upx;
		}

		.cell-tip {
			font-size: 26upx;
			color: $font-color-dark;

			&.disabled {
				color: $font-color-light;
			}

			&.active {
				color: $base-color;
			}

			&.red {
				color: $base-color;
			}
		}

		&.desc-cell {
			.cell-tit {
				max-width: 90upx;
			}
		}

		.desc {
			flex: 1;
			font-size: $font-base;
			color: $font-color-dark;
		}
	}
	
	/* 优惠券面板 */
	.mask {
		display: flex;
		align-items: flex-end;
		position: fixed;
		left: 0;
		top: var(--window-top);
		bottom: 0;
		width: 100%;
		background: rgba(0, 0, 0, 0);
		z-index: 9995;
		transition: .3s;
	
		.mask-content {
			width: 100%;
			// min-height: 40vh;
			// max-height: 70vh;
			background: #fff;
			transform: translateY(100%);
			transition: .3s;
			overflow-y: scroll;
			text-align: center;
		}
		.wxzf{
			padding:25upx;
			border-bottom:1px solid #F1F1F1;
			font-size:35upx;
		}
		.qx{
			padding:25upx;
			font-size:35upx;
			border-top:5px solid #F1F1F1;
		}
	
		&.none {
			display: none;
		}
	
		&.show {
			background: rgba(0, 0, 0, .4);
	
			.mask-content {
				transform: translateY(0);
			}
		}
	}
	
	
	
	
	.footer {
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 995;
		display: flex;
		// align-items: center;
		line-height: 90upx;
		width: 100%;
		height: 90upx;
		// justify-content: space-between;
		font-size: 30upx;
		background-color: #fff;
		z-index: 998;
		color: $font-color-base;
		box-shadow: 0 -1px 5px rgba(0, 0, 0, .1);
	
		// border:1px solid red;
		.price-content {
			padding-left: 30upx;
		}
	
		.price-tip {
			color: $base-color;
			margin-left: 8upx;
		}
	
		.price {
			font-size: 36upx;
			color: $base-color;
		}
	
		.submit {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 280upx;
			height: 100%;
			color: #fff;
			font-size: 32upx;
			background-color: $base-color;
			border-radius: 0upx;
			// height:100%;
			margin: 0;
		}
		.del{
			display: flex;
			align-items: center;
			justify-content: center;
			width: 280upx;
			height: 100%;
			font-size: 32upx;
			color:#b3b3b3;
			border-radius: 0upx;;
			// background-color: $base-color;
			border-radius: 0upx;
			// height:100%;
			margin: 0;
		}
	}
	

	
	
</style>
