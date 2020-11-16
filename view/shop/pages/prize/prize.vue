<template>
	<view class="content">
		<scroll-view scroll-with-animation scroll-y class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view class="goods-list ">
				<view 
					v-for="(item, index) in goodsList" :key="index"
					class="goods-item "
				>
					<view class="acea-row" @click="prizeDet(item)">
						<view class="image-wra">
							<image class="couimg" :src="indexUrl(item.prize_image)" mode="aspectFill"></image>
						</view>
						<view class='text'>
							<view class='condition line1'>{{item.prize_name}}</view>
						</view>
						
						<view v-if="item.status == 1">
							<view class="sx sxl" >
								<text class="">未领取</text>
							</view>
						</view>
						<view v-if="item.status == 2">
							<view class="sx sxy" >
								<text class="">已领取</text>
							</view>
						</view>
						<view v-if="item.status == 3">
							<view class="sx" >
								<text class="">已作废</text>
							</view>
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
				limit:20,
				status: 'more',
				keyword:'',
				type:0,
			};
		},
		
		onLoad(options){
			
			console.log('页面',options);
			this.sid = options.sid
			this.keyword = options.keyword
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
		},
		//加载更多
		onReachBottom(){
			// this.loadData();
		},
		methods: {
			
			getProductList(){
				let self = this;
				
				var path = '/gzh/activity_api/my_prize'
				var data = {
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
			
			
			prizeDet(item){
				console.log(item);
				uni.navigateTo({
					url:'/pages/prize/prizeDetail?id='+item.id
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
						self.scrollHeight = res.windowHeight
					}
				});
			},
			
		},
	}
</script>

<style lang="scss">
	@import url("prize.css");
	page, .content{
		background: $page-color-base;
	}
	.content{
		// padding-top: 82upx;
	}
	.sxl{
		 color:$base-color;
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
