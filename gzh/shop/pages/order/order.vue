<template>
	<view class="content">
		<view class="navbar">
			<view 
				v-for="(item, index) in navList" :key="index" 
				class="nav-item" 
				:class="{current: tabCurrentIndex == index}"
				@click="tabClick(index,item)"
			>
				{{item.text}}
			</view>
		</view>

		<!-- <swiper :current="tabCurrentIndex" class="swiper-box" duration="300" @change="changeTab">
			<swiper-item class="tab-content" v-for="(tabItem,tabIndex) in navList" :key="tabIndex"> -->
			<!-- <view class="tab-content" v-for="(tabItem,tabIndex) in navList" :key="tabIndex"> -->
				<scroll-view 
					class="list-scroll-content" 
					scroll-y
					scroll-with-animation :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr"
				>
					<!-- 订单列表 -->
					<view 
						v-for="(item,index) in orderList" :key="index"
						class="order-item"
					>
						<view class="i-top b-b">
							<text class="time">{{item._add_time}}</text>
							<text class="state" :style="{color: item.stateTipColor}">{{item._status._title}}</text>
							<text 
								v-if="item.state===9" 
								class="del-btn yticon icon-iconfontshanchu1"
								@click="deleteOrder(index)"
							></text>
						</view>
						<view 
							class="goods-box-single"
							v-for="(goodsItem, goodsIndex) in item.cartInfo" :key="goodsIndex"
							@click="orderdet(item)"
						>
							<image class="goods-img" :src="indexUrl(goodsItem.productInfo.image)" mode="aspectFill"></image>
							<view class="right">
								<text class="title clamp">{{goodsItem.productInfo.store_name}}</text>
								<text class="attr-box">{{goodsItem.productInfo.attrInfo ? goodsItem.productInfo.attrInfo.suk : ''}}  x {{goodsItem.cart_num}}</text>
								<text class="price">{{goodsItem.truePrice}}</text>
							</view>
						</view>
						
						<view class="price-box">
							共
							<text class="num">{{item.cartInfo.length}}</text>
							件商品 总金额
							<text class="price">{{item.total_price}}</text>
						</view>
						<view class="action-box b-t" v-if="item.paid == 0">
							<button class="action-btn" @click="cancelOrder(item)">取消订单</button>
							<button class="action-btn recom "  @click="orderzf(item)">立即支付</button>
						</view>
						<view class="action-box b-t" v-if="tabCurrentIndex == 1 && item.seckill_id == 0 && item.combination_id == 0 && item.presale_id == 0">
							<button class="action-btn recom "  @click="ordert(item)">申请退款</button>
						</view>
						<view class="action-box b-t" v-if="tabCurrentIndex == 2">
							<button class="action-btn" @click="orderdet(item)">查看物流</button>
							<button class="action-btn recom "  @click="orderdet(item)">确认收货</button>
						</view>
						<view class="action-box b-t" v-if="tabCurrentIndex == 3">
							<!-- <button class="action-btn" @click="orderdet(item)">查看物流</button> -->
							<button class="action-btn recom "  @click="orderdet(item)">评价</button>
						</view>
						<view class="action-box b-t" v-if="tabCurrentIndex == 4">
							<button class="action-btn" @click="orderdet(item)">删除订单</button>
							<!-- <button class="action-btn recom "  @click="orderdet(item)">再次购买</button> -->
						</view>
					</view>
					 
					<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
				</scroll-view>
			<!-- </view> -->
		<!-- 	</swiper-item>
		</swiper> -->
	</view>
</template> 

<script>
	import empty from "@/components/empty";
	import Json from '@/Json';
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		components: {
			uniLoadMore,
			empty
		},
		data() {
			return {
				tabCurrentIndex: 0,
				navList: [
					{
						state: 0,
						text: '待付款',
						loadingType: 'more',
						orderList: []
					},
					{
						state: 1,
						text: '待发货',
						loadingType: 'more',
						orderList: []
					},
					{
						state: 2,
						text: '待收货',
						loadingType: 'more',
						orderList: []
					},
					{
						state: 3,
						text: '待评价',
						loadingType: 'more',
						orderList: []
					},
					{
						state: 4,
						text: '已完成',
						loadingType: 'more',
						orderList: []
					}
				],
				orderList:[],
				
				source: 0,
				addressList: [],
				scrollHeight: 100,
				tabScrollTop: 0,
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '我是有底线的'
				},
				page:1,
				limit:20,
				status: 'more',
			};
		},
		
		onLoad(options){
			
			this.tabCurrentIndex = options.state
			this.page = 1
			this.limit = 20
			this.orderList = []
			this.getUserOrderList();
			// this.tabCurrentIndex = 0
			console.log(options,'sdfd',this.tabCurrentIndex);
			this.calcSize();
		},
		onShow(){
			
		},
		 
		methods: {
			getUserOrderList(type){
				let self = this;
				
				console.log(type,'哈哈哈哈哈哈哈',this.tabCurrentIndex)
				let path = '/gzh/user_api/get_user_order_list'
				let data = {
					type: type ? type : this.tabCurrentIndex,
					page:self.page,
					limit:self.limit
				}
				getApp().http(path, data, function(res) {
					uni.hideLoading();
					let list = res;
					if (list.length > 0) {
						console.log(list.length)
						self.orderList = self.orderList.concat(list);
						self.status = list.length >= self.limit ? "more" : "";
						if (self.status == "more") {
							self.page += 1;
						}
					} else {
						self.status = "";
					}
					
					console.log('水电费水电费',self.orderLis);
				})
			},
			//顶部tab点击
			tabClick(index,item){
				let self = this;
				self.tabCurrentIndex = index;
				let type = item.state
				
				uni.showLoading({})
				
				self.orderList = [];
				self.page = 1;
				self.getUserOrderList(type);
				console.log('点击',this.tabCurrentIndex,type)
				
			},
			orderdet(item){
				console.log('详情',item)
				uni.navigateTo({
					url: "/pages/order/orderDetail?uni="+item.order_id
				})
			},
			// 立即付款
			orderzf(item){
				console.log('支付',item)
				uni.navigateTo({
					url: "/pages/order/orderDetail?uni="+item.order_id
				})
			},
			
			ordert(item){
				console.log('支付',item)
				uni.navigateTo({
					url: "/pages/order/orderT?uni="+item.order_id
				})
			},
			
			//取消订单
			cancelOrder(item){
				uni.showLoading({})
				let self = this;
				let path = '/gzh/auth_api/cancel_order'
				let data = {
					order_id: item.order_id
				}
				getApp().http(path, data, function(res) {
					uni.hideLoading();
					if(res.code == 200){
						self.getUserOrderList('0');
						getApp().showTip(res.msg, "success")
					}else{
						getApp().showTip(res.msg, "none")
					}
				},true)
			},
			
			orderEval(item){
				
			},
			
			indexUrl(icon) {
				let http = icon.indexOf('http')
				if (http == -1) {
					return this.webUrl + '/' + icon
				} else {
					return icon;
				}
			},
			
			// 到底触发
			scroAddr(){
				let self = this;
				if(self.status){
					self.getUserOrderList();
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".content");
						self.scrollHeight = res.windowHeight - 40
						// self.scrollHeight = 300
						console.log(info);
						console.log('啊啊啊啊',res);
					}
				});
			},
			
			//swiper 切换
			changeTab(e){
				this.tabCurrentIndex = e.target.current;
				this.loadData('tabChange');
			},
			
			//删除订单
			deleteOrder(index){
				uni.showLoading({
					title: '请稍后'
				})
				setTimeout(()=>{
					this.navList[this.tabCurrentIndex].orderList.splice(index, 1);
					uni.hideLoading();
				}, 600)
			},
			

		},
	}
</script>

<style lang="scss">
	page, .content{
		background: $page-color-base;
		height: 100%;
	}
	
	.swiper-box{
		height: calc(100% - 40px);
	}
	.list-scroll-content{
		height: 100%;
	}
	
	.navbar{
		display: flex;
		height: 40px;
		padding: 0 5px;
		background: #fff;
		box-shadow: 0 1px 5px rgba(0,0,0,.06);
		position: relative;
		z-index: 10;
		.nav-item{
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			font-size: 15px;
			color: $font-color-dark;
			position: relative;
			&.current{
				color: $base-color;
				&:after{
					content: '';
					position: absolute;
					left: 50%;
					bottom: 0;
					transform: translateX(-50%);
					width: 44px;
					height: 0;
					border-bottom: 2px solid $base-color;
				}
			}
		}
	}

	.uni-swiper-item{
		height: auto;
	}
	.order-item{
		display: flex;
		flex-direction: column;
		padding-left: 30upx;
		background: #fff;
		margin-top: 16upx;
		.i-top{
			display: flex;
			align-items: center;
			height: 80upx;
			padding-right:30upx;
			font-size: $font-base;
			color: $font-color-dark;
			position: relative;
			.time{
				flex: 1;
			}
			.state{
				color: $base-color;
			}
			.del-btn{
				padding: 10upx 0 10upx 36upx;
				font-size: $font-lg;
				color: $font-color-light;
				position: relative;
				&:after{
					content: '';
					width: 0;
					height: 30upx;
					border-left: 1px solid $border-color-dark;
					position: absolute;
					left: 20upx;
					top: 50%;
					transform: translateY(-50%);
				}
			}
		}
		/* 多条商品 */
		.goods-box{
			height: 160upx;
			padding: 20upx 0;
			white-space: nowrap;
			.goods-item{
				width: 120upx;
				height: 120upx;
				display: inline-block;
				margin-right: 24upx;
			}
			.goods-img{
				display: block;
				width: 100%;
				height: 100%;
			}
		}
		/* 单条商品 */
		.goods-box-single{
			display: flex;
			padding: 20upx 0;
			.goods-img{
				display: block;
				width: 120upx;
				height: 120upx;
			}
			.right{
				flex: 1;
				display: flex;
				flex-direction: column;
				padding: 0 30upx 0 24upx;
				overflow: hidden;
				.title{
					font-size: $font-base + 2upx;
					color: $font-color-dark;
					line-height: 1;
				}
				.attr-box{
					font-size: $font-sm + 2upx;
					color: $font-color-light;
					padding: 10upx 12upx;
				}
				.price{
					font-size: $font-base + 2upx;
					color: $font-color-dark;
					&:before{
						content: '￥';
						font-size: $font-sm;
						margin: 0 2upx 0 8upx;
					}
				}
			}
		}
		
		.price-box{
			display: flex;
			justify-content: flex-end;
			align-items: baseline;
			padding: 20upx 30upx;
			font-size: $font-sm + 2upx;
			color: $font-color-light;
			.num{
				margin: 0 8upx;
				color: $font-color-dark;
			}
			.price{
				font-size: $font-lg;
				color: $font-color-dark;
				&:before{
					content: '￥';
					font-size: $font-sm;
					margin: 0 2upx 0 8upx;
				}
			}
		}
		.action-box{
			display: flex;
			justify-content: flex-end;
			align-items: center;
			height: 100upx;
			position: relative;
			padding-right: 30upx;
		}
		.action-btn{
			width: 160upx;
			height: 60upx;
			margin: 0;
			margin-left: 24upx;
			padding: 0;
			text-align: center;
			line-height: 60upx;
			font-size: $font-sm + 2upx;
			color: $font-color-dark;
			background: #fff;
			border-radius: 100px;
			&:after{
				border-radius: 100px;
			}
			&.recom{
				background: #fff9f9;
				color: $base-color;
				&:after{
					border-color: #f7bcc8;
				}
			}
		}
	}
	
	
	/* load-more */
	.uni-load-more {
		display: flex;
		flex-direction: row;
		height: 80upx;
		align-items: center;
		justify-content: center
	}
	
	.uni-load-more__text {
		font-size: 28upx;
		color: #999
	}
	
	.uni-load-more__img {
		height: 24px;
		width: 24px;
		margin-right: 10px
	}
	
	.uni-load-more__img>view {
		position: absolute
	}
	
	.uni-load-more__img>view view {
		width: 6px;
		height: 2px;
		border-top-left-radius: 1px;
		border-bottom-left-radius: 1px;
		background: #999;
		position: absolute;
		opacity: .2;
		transform-origin: 50%;
		animation: load 1.56s ease infinite
	}
	
	.uni-load-more__img>view view:nth-child(1) {
		transform: rotate(90deg);
		top: 2px;
		left: 9px
	}
	
	.uni-load-more__img>view view:nth-child(2) {
		transform: rotate(180deg);
		top: 11px;
		right: 0
	}
	
	.uni-load-more__img>view view:nth-child(3) {
		transform: rotate(270deg);
		bottom: 2px;
		left: 9px
	}
	
	.uni-load-more__img>view view:nth-child(4) {
		top: 11px;
		left: 0
	}
	
	.load1,
	.load2,
	.load3 {
		height: 24px;
		width: 24px
	}
	
	.load2 {
		transform: rotate(30deg)
	}
	
	.load3 {
		transform: rotate(60deg)
	}
	
	.load1 view:nth-child(1) {
		animation-delay: 0s
	}
	
	.load2 view:nth-child(1) {
		animation-delay: .13s
	}
	
	.load3 view:nth-child(1) {
		animation-delay: .26s
	}
	
	.load1 view:nth-child(2) {
		animation-delay: .39s
	}
	
	.load2 view:nth-child(2) {
		animation-delay: .52s
	}
	
	.load3 view:nth-child(2) {
		animation-delay: .65s
	}
	
	.load1 view:nth-child(3) {
		animation-delay: .78s
	}
	
	.load2 view:nth-child(3) {
		animation-delay: .91s
	}
	
	.load3 view:nth-child(3) {
		animation-delay: 1.04s
	}
	
	.load1 view:nth-child(4) {
		animation-delay: 1.17s
	}
	
	.load2 view:nth-child(4) {
		animation-delay: 1.3s
	}
	
	.load3 view:nth-child(4) {
		animation-delay: 1.43s
	}
	
	@-webkit-keyframes load {
		0% {
			opacity: 1
		}
	
		100% {
			opacity: .2
		}
	}
</style>
