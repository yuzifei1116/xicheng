<template>
	<view class="content">
		
		<view>
			<image class="int-img" src="../../static/数据.jpg"></image>
			<view class="int-hed acea-row">
				<view class="int-hed-jf">当前积分</view>
				<view class="int-hed-jf-num">{{userInfo.integral}}</view>
				<view class="acea-row int-hed-list">
					<view class="int-hed-list1">
						<view class="int-hed-list-num">{{userInfo.sum_integral}}</view>
						<view class="int-hed-list-tit">累计积分</view>
					</view>
					<view class="int-hed-list1">
						<view class="int-hed-list-num">{{userInfo.deduction_integral}}</view>
						<view class="int-hed-list-tit">累计消费</view>
					</view>
					<view class="int-hed-list1">
						<view class="int-hed-list-num">{{userInfo.today_integral}}</view>
						<view class="int-hed-list-tit">今日获得</view>
					</view>
				</view>
				
				<view class="navbar acea-row" >
					<view class="nav-item" :class="{current: filterIndex == 0}" @click="tabClick(0)">
						<view class="yticon icon-daifukuan con-list-icon"></view>
						分值明细
					</view>
					<view class="nav-item" :class="{current: filterIndex == 1}" @click="tabClick(1)">
						<view class="yticon icon-arrow-fine-up con-list-icon"></view>
						分值提升
					</view>
				</view>
			</view>
		</view>
		
		
		
		<scroll-view scroll-with-animation scroll-y class="right-aside"  :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view class="goods-list " v-if="filterIndex == 0">
				<view 
					v-for="(item, index) in goodsList" :key="index"
					class="goods-item "
				>
					<view class="aces-space-between">
						<view class='text'>
							<view class='condition line1'>{{item.mark}}</view>
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
			
			<view v-if="filterIndex == 1" class="goods-list ">
				<view class="goods-item ">
					<view class="acea-row good-list-item">
						<view class="image-wra">
							<image class="couimg" src="../../static/score.png" mode="aspectFill"></image>
						</view>
						<view class='text1'>
							购买商品可获得积分奖励
						</view>
						<view class="coulq" @click="navint">
							<view class="couliqs">赚积分</view>
						</view>
					</view>
				</view>
			</view>
			
			
			<view v-if="filterIndex == 0">
				<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
			</view>
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
				userInfo:[],
			};
		},
		
		onLoad(options){
			
			this.type = options.type
			console.log('页面',options);
			getApp().user_api(true);
			this.calcSize();
			this.getMyUserInfo();
			this.getProductList();
			
		},
		onShow(){
		},
		onPageScroll(e){
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
			
			getMyUserInfo(){
				let self = this;
				console.log('排序',self.filterIndex);
				var path = '/gzh/user_api/get_my_user_info'
				var data = {
					isIntegral:1,
				};
				
				getApp().http(path, data, function(res) {
					self.userInfo = res;
				});
			},
			
			getProductList(val){
				let self = this;
				console.log('排序',self.filterIndex);
				var path = '/gzh/user_api/user_integral_list'
				var data = {
					page:self.page,
					limit:self.limit
				};
				
				getApp().http(path, data, function(res) {
					console.log('详情',res);
					uni.hideLoading();
					// self.goodsList = res;
					
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
			//筛选点击
			tabClick(index){
				let self = this;
				console.log('点击',index)
				
				self.filterIndex = index;
				
				if(self.filterIndex == 0){
					uni.showLoading({
						title: '正在加载'
					})
					console.log('优惠券')
					self.page = 1;
					self.goodsList = []
					self.type = 0
					self.getProductList()
				}else if(self.filterIndex == 1){
					self.page = 1;
					self.goodsList = []
					self.type = 1
					// self.getProductList()
					console.log('我的')
				}
			},
			
			navint(){
				uni.switchTab({
					url: "/pages/index/index"
				})
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
						self.scrollHeight = res.windowHeight - 230
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
	@import url("user_integral.css");
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
		width: 90%;
		height: 100upx;
		// background: #fff;
		background: #F7F7F7;
		border-radius: 20upx 20upx 0upx 0upx;
		// box-shadow: 0 2upx 10upx rgba(0,0,0,.06);
		z-index: 10;
		margin-left:5%;
		.nav-item{
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			font-size: 30upx;
			color: $font-color-dark;
			position: relative;
			border-radius: 20upx 20upx 0upx 0upx;
			&.current{
				color: $base-color;
				// border:1px solid red;
				background: #fff;
				border-radius: 20;
				&:after{
					content: '';
					position: absolute;
					left: 50%;
					bottom: 0;
					transform: translateX(-50%);
					width: 120upx;
					height: 0;
					// border-bottom: 4upx solid $base-color;
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

	
	

</style>
