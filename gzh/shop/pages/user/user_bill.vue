<template>
	<view class="content">
		<view class="navbar" >
			<view class="nav-item" :class="{current: filterIndex == 0}" @click="tabClick(0)">
				全部
			</view>
			<view class="nav-item" :class="{current: filterIndex == 1}" @click="tabClick(1)">
				消费
			</view>
			<view class="nav-item" :class="{current: filterIndex == 2}" @click="tabClick(2)">
				充值
			</view>
		</view>
		<scroll-view scroll-with-animation scroll-y class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view v-for="(item, index) in goodsList" :key="index">
				<view class="good-list-time">{{item.money}}</view>
				<view class="goods-list ">
					<view 
						v-for="(item, index) in item.list" :key="index"
						class="goods-item "
					>
						<view class="aces-space-between">
							<view class='text'>
								<view class='condition line1'>{{item.title}}</view>
								<view class='data acea-row '>
									<view class="coutime">{{item.add_time}}</view>
								</view>
							</view>
							<view v-if="item.pm">
								<view class='money1'>+<text class='num'>{{item.number}}</text></view>
							</view>
							<view v-else>
								<view class='money'>-<text class='num'>{{item.number}}</text></view>
							</view>
							
						</view>
					</view>
				</view>
				
			</view>
			
			<!-- <uni-load-more :status="status" :icon-size="16" :content-text="contentText" /> -->
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
				sid:0,
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
				limit:5,
				status: 'more',
				keyword:'',
				type:0,
			};
		},
		
		onLoad(options){
			
			this.type = options.type
			console.log('页面',options);
			getApp().user_api(true);
			this.calcSize();
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
			console.log('refresh');
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		//加载更多
		onReachBottom(){
			// this.loadData();
		},
		methods: {
			
			getProductList(val){
				let self = this;
				self.filterIndex = self.type
				console.log('排序',self.filterIndex);
				var path = '/gzh/user_api/get_user_bill_list'
				var data = {
					type:self.type,
					// page:self.page,
					// limit:self.limit
				};
				
				getApp().http(path, data, function(res) {
					console.log('详情',res);
					uni.hideLoading();
					self.goodsList = res;
					
					// let list = res;
					// if (list.length > 0) {
					// 	console.log('总条数',list.length)
					// 	self.status = list.length <= self.limit ? "more" : "";
					// 	if (self.status == "more") {
					// 		self.page += 1;
							
					// 		console.log(self.page);
					// 		console.log('=================444444444444')
					// 		self.goodsList = self.goodsList.concat(list);
					// 	}
					// } else {
					// 	self.status = "";
					// }
				})
			},
			//筛选点击
			tabClick(index){
				let self = this;
				console.log('点击',index)
				
				self.filterIndex = index;
				
				if(index === 2){
					this.priceOrder = this.priceOrder === 1 ? 2: 1;
				}else{
					this.priceOrder = 0;
				}
				
				uni.showLoading({
					title: '正在加载'
				})
				
				if(self.filterIndex == 0){
					console.log('优惠券')
					self.page = 1;
					self.goodsList = []
					self.type = 0
					self.getProductList()
				}else if(self.filterIndex == 1){
					self.page = 1;
					self.goodsList = []
					self.type = 1
					self.getProductList()
					console.log('我的')
				}else if(self.filterIndex == 2){
					self.page = 1;
					self.goodsList = []
					self.type = 2
					self.getProductList()
					console.log('我的')
				}
			},
			
			//领取
			userCoupon(item){
				let self = this;
				let id = item.id
				let path = '/gzh/coupons_api/user_get_coupon'
				
				let data = {
					couponId:id
				};
				getApp().http(path, data, function(res) {
					console.log('领取',res)
					if(res.code == 200){
						getApp().showTip(res.msg, "success")
					}else{
						getApp().showTip(res.msg,'none')
					}
				},true)
				
			},
			
			
			// 到底触发
			scroAddr(){
				let self = this;
				if(self.status){
					// self.getProductList();
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".content");
						self.scrollHeight = res.windowHeight - 38
						// self.scrollHeight = 200
						// console.log(info);
						// console.log('啊啊啊啊',res);
					}
				});
			},
			
		},
	}
</script>

<style lang="scss">
	@import url("user_bill.css");
	page, .content{
		background: $page-color-base;
	}
	.content{
		padding-top: 82upx;
	}

	.navbar{
		position: fixed;
		left: 0;
		top: var(--window-top);
		display: flex;
		width: 100%;
		height: 80upx;
		background: #fff;
		box-shadow: 0 2upx 10upx rgba(0,0,0,.06);
		z-index: 10;
		.nav-item{
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			font-size: 30upx;
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
					width: 120upx;
					height: 0;
					border-bottom: 4upx solid $base-color;
				}
			}
		}
		.p-box{
			display: flex;
			flex-direction: column;
			.yticon{
				display: flex;
				align-items: center;
				justify-content: center;
				width: 30upx;
				height: 14upx;
				line-height: 1;
				margin-left: 4upx;
				font-size: 26upx;
				color: #888;
				&.active{
					color: $base-color;
				}
			}
			.xia{
				transform: scaleY(-1);
			}
		}
		.cate-item{
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			width: 80upx;
			position: relative;
			font-size: 44upx;
			&:after{
				content: '';
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				border-left: 1px solid #ddd;
				width: 0;
				height: 36upx;
			}
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

	
	

</style>
