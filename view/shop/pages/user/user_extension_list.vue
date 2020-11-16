<template>
	<view class="content">
		<scroll-view scroll-with-animation scroll-y class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view class="goods-list">
				<view 
					v-for="(item, index) in goodsList" :key="index"
					class="order-item"
				>
				<view 
					class="goods-box-single"
				>
					<view class="right">
						<view class="aces-space-between">
							<text class="title clamp">{{item.title}}</text>
							<text v-if="item.number >0">
								<text class="extnum">+{{item.number}}</text>
							</text>
							<text v-else>
								<text class="extnum1">-{{item.number}}</text>
							</text>
						</view>
						<text class="attr-box">时间：{{item.add_time}}</text>
					</view>
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
				let path = '/gzh/user_api/get_user_bill_spread_list'
				let uid = self.uid ? self.uid : ''
				
				let data = {
					page:self.page,
					limit:self.limit
				};
				getApp().http(path, data, function(res) {
					console.log('详情',res);
					uni.hideLoading();
					
					let list = res;
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
							self.scrollHeight = res.windowHeight
						}else{
							self.scrollHeight = res.windowHeight
						}
						
					}
				});
			},
			
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

	.order-item{
		display: flex;
		flex-direction: column;
		padding-left: 30upx;
		background: #fff;
		margin-bottom: 10upx;
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
					// padding: 10upx 12upx;
				}
				.price{
					font-size: $font-base + 2upx;
					// color: $font-color-dark;
					color:#fa436a;
					&:before{
						content: '';
						font-size: $font-sm;
						// margin: 0 2upx 0 8upx;
					}
				}
			}
		}
		
		.extnum{
			font-size:28upx;
			color:red;
		}
		.extnum1{
			font-size:28upx;
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
