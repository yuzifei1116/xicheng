<template>
	<view class="content">
		<!-- {{uid}}111 -->
		<view class="navbar" v-if="!uid">
			<view class="aces-center nav-jf">
				<image class="navimg" src="../../static/balance.png"></image>
				<view>佣金：{{userInfo.now_money_spread}}</view>
			</view>
			<view class="aces-center nav-jf" @click="intClickord">
				<image class="navimg" src="../../static/record2.png"></image>
				<view>佣金明细</view>
			</view>
			
		</view>
		<scroll-view scroll-with-animation scroll-y class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view class="goods-list">
				<view 
					v-for="(item, index) in goodsList" :key="index"
					class="order-item"
				>
				<view 
					class="goods-box-single"
				>
					<image class="goods-img" :src="indexUrl(item.avatar)" mode="aspectFill"></image>
					<view class="right">
						<text class="title clamp">{{item.nickname}}</text>
						<text class="attr-box">总订单：{{item.order.order_count}} &nbsp;&nbsp; 总付款：{{item.order.total_price}}</text>
					</view>
				</view>
					<view class="action-box b-t">
						<button class="action-btn recom1 "  @click="extenUser(item)">下级</button>
						<button class="action-btn recom "  @click="orderdet(item)">订单列表</button>
					</view>
				</view>
				
			</view>
			<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
		</scroll-view>

		
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		components: {
			uniLoadMore	
		},
		data() {
			return {
				cateMaskState: 0, //分类面板展开状态
				headerPosition:"fixed",
				headerTop:"0px",
				loadingType: 'more', //加载更多状态
				filterIndex: 0, 
				cateId: 0, //已选三级分类id
				priceOrder: 0, //1 价格从低到高 2价格从高到低
				cateList: [],
				goodsList: [],
				uid:0,
				salesOrder:'',
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
				keyword:'',
				userInfo:[],
			};
		},
		
		onLoad(options){
			console.log('页面',options);
			this.uid = options.uid
			// this.keyword = options.keyword
			this.calcSize();
			getApp().user_api(true);
			this.my();
			this.getProductList();
			
		},
		onShow(){
		},
		onPageScroll(e){
			//兼容iOS端下拉时顶部漂移
			if(e.scrollTop>=0){
				this.headerPosition = "fixed";
			}else{
				this.headerPosition = "absolute";
			}
		},
		//下拉刷新
		onPullDownRefresh() {
		},
		//加载更多
		onReachBottom(){
			// this.loadData();
		},
		methods: {
			
			getProductList(){
				let self = this;
				let path = '/gzh/user_api/user_spread_list'
				let uid = self.uid ? self.uid : ''
				
				let data = {
					uid:uid,
					page:self.page,
					limit:self.limit
				};
				getApp().http(path, data, function(res) {
					console.log('详情',res);
					uni.hideLoading();
					
					let list = res.list;
					if (list.length > 0) {
						console.log('总条数',list.length)
						self.goodsList = self.goodsList.concat(list);
						self.status = list.length >= self.limit ? "more" : "";
						if (self.status == "more") {
							self.page += 1;
						}
						// self.status = list.length <= self.limit ? "more" : "";
						// if (self.status == "more") {
						// 	self.page += 1;
						// 	self.goodsList = self.goodsList.concat(list);
						// }
					} else {
						self.status = "";
					}
				})
			},
			
			my(){
				let self = this;
				let path = '/gzh/user_api/my'
				getApp().http(path, {}, function(res) {
					console.log('个人信息',res)
					self.userInfo = res;
				})
			},
			
			extenUser(item){
				console.log('发鬼地方个',item);
				uni.navigateTo({
					url: '/pages/user/user_extension?uid='+item.uid
				})
			},
			orderdet(item){
				uni.navigateTo({
					url: '/pages/user/user_extension_det?uid='+item.uid
				})
			},
			
			intClickord(){
				uni.navigateTo({
					url: '/pages/user/user_extension_list'
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
			// 到底触发
			scroAddr(){
				let self = this;
				if(self.status){
					self.getProductList();
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".content");
						
						if(!self.uid){
							self.scrollHeight = res.windowHeight - 44
						}else{
							self.scrollHeight = res.windowHeight
						}
						
					}
				});
			},
			
			//详情
			navToDetailPage(item){
				console.log('详情',item);
				let id = item.id;
				uni.navigateTo({
					url: `/pages/integral/index?id=${id}`
				})
			},
			
			
			stopPrevent(){}
		},
	}
</script>

<style lang="scss">
	page, .content{
		background: $page-color-base;
	}
	.content{
		// padding-top: 82upx;
	}

	.navbar{
		// position: fixed;
		// left: 0;
		// top: var(--window-top);
		display: flex;
		width: 100%;
		height: 80upx;
		background: #fff;
		box-shadow: 0 2upx 10upx rgba(0,0,0,.06);
		z-index: 10;
		border-bottom:1px solid #FAFAFA;
		line-height: 80upx;
		.nav-jf{
			width:50%;
			// border:1px solid red;
			text-align: center;
			font-size:30upx;
		}
		.navimg{
			width:44upx;
			height:44upx;
			margin-top:18upx;
			margin-right:10upx;
		}
	}

	/* 分类 */
	.cate-mask{
		position: fixed;
		left: 0;
		top: var(--window-top);
		bottom: 0;
		width: 100%;
		background: rgba(0,0,0,0);
		z-index: 95;
		transition: .3s;
		
		.cate-content{
			width: 630upx;
			height: 100%;
			background: #fff;
			float:right;
			transform: translateX(100%);
			transition: .3s;
		}
		&.none{
			display: none;
		}
		&.show{
			background: rgba(0,0,0,.4);
			
			.cate-content{
				transform: translateX(0);
			}
		}
	}
	.cate-list{
		display: flex;
		flex-direction: column;
		height: 100%;
		.cate-item{
			display: flex;
			align-items: center;
			height: 90upx;
			padding-left: 30upx;
 			font-size: 28upx;
			color: #555;
			position: relative;
		}
		.two{
			height: 64upx;
			color: #303133;
			font-size: 30upx;
			background: #f8f8f8;
		}
		.active{
			color: $base-color;
		}
	}


	.order-item{
		display: flex;
		flex-direction: column;
		padding-left: 30upx;
		background: #fff;
		margin-bottom: 16upx;
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
					margin-top:10upx;
					margin-bottom:10upx;
				}
				.attr-box{
					font-size: $font-sm + 2upx;
					color: $font-color-light;
					padding: 10upx 12upx;
				}
				.price{
					font-size: $font-base + 2upx;
					// color: $font-color-dark;
					color:$base-color;
					&:before{
						content: '';
						font-size: $font-sm;
						// margin: 0 2upx 0 8upx;
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
					content: '';
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
			.recom1{
				color:#555;
			}
		}
	}
	



</style>
