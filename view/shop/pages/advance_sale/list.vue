<template>
	<view class="content">
		<!-- <view class="navbar" >
			<view class="nav-item" :class="{current: filterIndex === 0}" @click="tabClick(0)">
				综合排序
			</view>
			<view class="nav-item" :class="{current: filterIndex === 1}" @click="tabClick(1)">
				销量优先
			</view>
			<view class="nav-item" :class="{current: filterIndex === 2}" @click="tabClick(2)">
				<text>价格</text>
				<view class="p-box">
					<text :class="{active: priceOrder === 1 && filterIndex === 2}" class="yticon icon-shang"></text>
					<text :class="{active: priceOrder === 2 && filterIndex === 2}" class="yticon icon-shang xia"></text>
				</view>
			</view> -->
			<!-- <text class="cate-item yticon icon-fenlei1" @click="toggleCateMask('show')"></text> -->
		<!-- </view> -->
		<scroll-view scroll-with-animation scroll-y class="right-aside"  :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view class="goods-list">
				<view 
					v-for="(item, index) in goodsList" :key="index"
					class="goods-item "
					@click="navToDetailPage(item)"
				>
				<view class="acea-row">
					<view class="image-wrapper">
						<image :src="indexUrl(item.image)" mode="aspectFill"></image>
					</view>
					<view class="seck-pri">
						<text class="title clamp">{{item.title}}</text>
						<view class="price-box aces-space-between">
							<text class="seck-price">限时价 <text class="seck-pric"> ￥{{item.price}}</text> </text>
							<!-- <text>已售 {{item.sales}}</text> -->
						</view>
						<view class="aces-space-between">
							<view class="seck-time">开始时间:{{item.start_time}}</view>
						</view>
						<view class="acea-row-reverse ">
							<view class="seck-q">马上抢</view>
						</view>
					</view>
					
				</view>
					
				</view>
			</view>
			<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
		</scroll-view>
		
		<view class="cate-mask" :class="cateMaskState===0 ? 'none' : cateMaskState===1 ? 'show' : ''" @click="toggleCateMask">
			<view class="cate-content" @click.stop.prevent="stopPrevent" @touchmove.stop.prevent="stopPrevent">
				<scroll-view scroll-y class="cate-list">
					<view v-for="item in cateList" :key="item.id">
						<view class="cate-item b-b two">{{item.name}}</view>
						<view 
							v-for="tItem in item.child" :key="tItem.id" 
							class="cate-item b-b" 
							:class="{active: tItem.id==cateId}"
							@click="changeCate(tItem)">
							{{tItem.name}}
						</view>
					</view>
				</scroll-view>
			</view>
		</view>
		
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
			};
		},
		
		onLoad(options){
			
			this.calcSize();
			getApp().user_api(true);
			this.getProductList();
		},
		onShow(){
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
				let path = '/gzh/presale_api/presale_list'
				
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
			indexUrl(icon){
				
				let http = icon.indexOf('http')
				// console.log('图谱爱',http)
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
						// self.scrollHeight = 200
						// console.log(info);
						// console.log('啊啊啊啊',res);
					}
				});
			},
			
			//显示分类面板
			toggleCateMask(type){
				let timer = type === 'show' ? 10 : 300;
				let	state = type === 'show' ? 1 : 0;
				this.cateMaskState = 2;
				setTimeout(()=>{
					this.cateMaskState = state;
				}, timer)
			},
			//分类点击
			changeCate(item){
				this.cateId = item.id;
				this.toggleCateMask();
				uni.pageScrollTo({
					duration: 300,
					scrollTop: 0
				})
				this.loadData('refresh', 1);
				uni.showLoading({
					title: '正在加载'
				})
			},
			//详情
			navToDetailPage(item){
				console.log('详情',item);
				let id = item.id;
				uni.navigateTo({
					url: `/pages/advance_sale/advance_sale?id=${id}`
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

	/* 商品列表 */
	.goods-list{
		display:flex;
		flex-wrap:wrap;
		padding: 20upx;
		background: #fff;
		.goods-item{
			display:flex;
			flex-direction: column;
			width: 100%;
			padding: 20upx 0;
			// &:nth-child(2n+1){
			// 	margin-right: 4%;
			// }
			border-bottom:1px solid #FAFAFA;
		}
		.image-wrapper{
			width: 220upx;
			height: 220upx;
			border-radius: 3px;
			overflow: hidden;
			// border:1px solid red;
			margin-right:15upx;
			image{
				width: 100%;
				height: 100%;
				opacity: 1;
			}
		}
		.title{
			font-size: $font-lg;
			color: $font-color-dark;
			line-height: 60upx;
		}
		.price-box{
			// display: flex;
			// align-items: center;
			// justify-content: space-between;
			// padding-right: 10upx;
			font-size: 24upx;
			color: $font-color-light;
		}
		.seck-pri{
			width:460upx;
			// border:1px solid red;
		}
		.seck-price{
			font-size:28upx;
		}
		.seck-pric{
			color:$uni-color-primary;
			font-size: 34upx;
			font-weight: bold;
		}
		.price{
			font-size: $font-lg;
			color: $uni-color-primary;
			line-height: 1;
			&:before{
				content: '￥';
				font-size: 26upx;
			}
		}
		.seck-time{
			font-size:26upx;
			color:$uni-color-primary;
			margin-top:10upx;
		}
		.seck-q{
			font-size:28upx;
			background:#E83222;
			padding:8upx 30upx;
			color:#fff;
			margin-top:8upx;
		}
	}
	

</style>
