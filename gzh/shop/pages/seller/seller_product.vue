<template>
	<view class="content">
		
		<view class="carousel" v-if="merDetail.banner">
			<swiper indicator-dots autoplay interval="3000">
				<swiper-item class="swiper-item" v-for="(item,index) in merDetail.banner" :key="index" >
				<!-- <swiper-item class="swiper-item" > -->
					<view class="image-wrapper">
						<image
							:src="indexUrl(item)" 
							class="loaded" 
							mode="aspectFill"
						></image>
					</view>
				</swiper-item>
			</swiper>
		</view>
		<view class="d-hed acea-row">
			<view>
				<image class="d-img" :src="indexUrl(merDetail.image)"></image>
			</view>
			<view class="d-hed-name">
				<view class="d-hed-dname">{{merDetail.mer_name}}</view>
				<!-- <view class="d-hed-xl">销量：1</view> -->
				<view class="d-hed-xl">{{merDetail.info}}</view>
				<view class="d-hed-xl" @click="clickDao">
					<text class="yticon icon-dizhi dz-icon"></text>
					查看地址
				</view>
			</view>
			
		</view>
		
		
		<view class="navbar" >
			
			<view class="nav-item" :class="{current: filterIndex === 1}" @click="tabClick(1)">
				销量优先
			</view>
			<view class="nav-item" :class="{current: filterIndex === 2}" @click="tabClick(2)">
				<text>价格</text>
				<view class="p-box">
					<text :class="{active: priceOrder === 1 && filterIndex === 2}" class="yticon icon-shang"></text>
					<text :class="{active: priceOrder === 2 && filterIndex === 2}" class="yticon icon-shang xia"></text>
				</view>
			</view>
			<view class="nav-item" @click="toggleCateMask('show')">
				活动
			</view>
			<!-- <text class="cate-item yticon icon-fenlei" @click="toggleCateMask('show')"></text> -->
		</view>
		<scroll-view scroll-with-animation scroll-y class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
			<view class="goods-list">
				<view 
					v-for="(item, index) in goodsList" :key="index"
					class="goods-item"
					@click="navToDetailPage(item)"
				>
					<view class="image-wrapper">
						<image :src="item.image" mode="aspectFill"></image>
					</view>
					<text class="title clamp">{{item.store_name}}</text>
					<view class="price-box">
						<text class="price">{{item.price}}</text>
						<text>已售 {{item.sales}}</text>
					</view>
				</view>
			</view>
			<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
		</scroll-view>
		
		<view class="cate-mask" :class="cateMaskState===0 ? 'none' : cateMaskState===1 ? 'show' : ''" @click="toggleCateMask">
			<view class="cate-content" @click.stop.prevent="stopPrevent" @touchmove.stop.prevent="stopPrevent">
				<!-- sdfsdfsdf -->
				<scroll-view scroll-y class="cate-list">
					<view class="cate-item b-b two">店铺活动</view>
					<view class="cate-item b-b" @click="clickSell('/pages/collage/list?mer_id='+id)">拼团</view>
					<view class="cate-item b-b" @click="clickSell('/pages/seckill/list?mer_id='+id)">秒杀</view>
					<view class="cate-item b-b" @click="clickSell('/pages/user/coupon?mer_id='+id)">优惠券</view>
					<!-- <view class="cate-item b-b">预售</view>  -->
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
				filterIndex: 1, 
				cateId: 0, //已选三级分类id
				priceOrder: 0, //1 价格从低到高 2价格从高到低
				cateList: [],
				goodsList: [],
				id:0,
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
				merDetail:[],
				type:0,
				
			};
		},
		
		onLoad(options){
			
			console.log('页面',options);
			this.id = options.id
			this.calcSize();
			getApp().user_api(true);
			
			this.filterIndex = 1;
			this.page = 1;
			// self.type = 0
			this.goodsList = []
			this.getProductList();
			
			
			this.merchantDetail();
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
		
		methods: {
			
			merchantDetail(){
				let self = this;
				let path = '/gzh/merchant_api/merchant_detail'
				let data = {id:self.id};
				getApp().http(path, data, function(res) {
					self.merDetail = res;
				});
			},
			getProductList(salesOrder,priceOrder){
				let self = this;
				let path = '/gzh/merchant_api/merchant_product'
				let id1 = self.id 
				let salesOrder1 = salesOrder ? salesOrder : ''
				let priceOrder1 = priceOrder ? priceOrder : ''
				let type1 = self.type ? self.type : 2
				console.log('排序',salesOrder1);
				
				let data = {
					id:id1,
					sales_order:salesOrder1,
					price_order:priceOrder1,
					type:type1,
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
				
				if(index === 2){
					this.priceOrder = this.priceOrder === 1 ? 2: 1;
				}else{
					this.priceOrder = 0;
				}
				
				uni.showLoading({
					title: '正在加载'
				})
				
				if(self.filterIndex == 0){
					console.log('综合排序')
					self.page = 1;
					self.type = 0
					self.goodsList = []
					self.getProductList()
				}else if(self.filterIndex == 1){
					self.page = 1;
					self.type = 2
					self.goodsList = []
					self.getProductList('desc')
					console.log('销量')
				}else if(self.filterIndex == 2){
					console.log('价格',this.priceOrder)
					self.type = 1
					if(this.priceOrder == 1){
						self.page = 1;
						self.goodsList = []
						self.getProductList('','desc')
					}else{
						self.page = 1;
						self.goodsList = []
						self.getProductList('','asc')
					}
				}
			},
			
			
			clickSell(i){
				console.log(i,'水电费第三方')
				uni.navigateTo({
					url: i
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
			
			clickDao(){
				let self = this;
				
				// uni.navigateTo({
				// 	url: '/pages/seller/map?longitude='+self.merDetail.longitude+'&latitude='+self.merDetail.latitude
				// })
				
				
				
				
				
				
				uni.showActionSheet({
				     itemList: ['腾讯地图', '高德地图'],
				     success: (res) => {
				      switch (res.tapIndex) {
				       case 0:
				        window.location.href = "http://apis.map.qq.com/uri/v1/marker?marker=coord:" + self.merDetail.latitude + "," + self.merDetail.longitude +
				         ";title:目的地;addr:" + '' + "&referer=myapp";
				        break;
				       case 1:
				        window.location.href = "http://uri.amap.com/marker?position=" + self.merDetail.longitude  + "," + self.merDetail.latitude+
				         "&name=" + '' + "&src=yellowpage&coordinate=gaode&callnative=1";
				        break;
				       case 2:
				        let x_pi = 3.14159265358979324 * 3000.0 / 180.0;
				        let x = order.longitude;
				        let y = order.latitude;
				        let z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * x_pi);
				        let theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * x_pi);
				        let lngs = z * Math.cos(theta) + 0.0065;
				        let lats = z * Math.sin(theta) + 0.006;
				        window.location.href = "http://api.map.baidu.com/marker?location=" + lats + "," + lngs +
				         "&title=目的地&content=" + order.user_address + "&output=html&src=yellowpage";
				        break;
				       default:
				        uni.showToast({
				         title: "没有相关内容",
				         icon: "none"
				        });
				      }
				     }
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
						self.scrollHeight = res.windowHeight - 38
						// self.scrollHeight = 200
						// console.log(info);
						// console.log('啊啊啊啊',res);
					}
				});
			},
			
			
			
			
			//加载分类
			async loadCateList(fid, sid){
				let list = await this.$api.json('cateList');
				let cateList = list.filter(item=>item.pid == fid);
				
				cateList.forEach(item=>{
					let tempList = list.filter(val=>val.pid == item.id);
					item.child = tempList;
				})
				// this.cateList = cateList;
			},
			//加载商品 ，带下拉刷新和上滑加载
			// async loadData(type='add', loading) {
				//没有更多直接返回
				// if(type === 'add'){
				// 	if(this.loadingType === 'nomore'){
				// 		return;
				// 	}
				// 	this.loadingType = 'loading';
				// }else{
				// 	this.loadingType = 'more'
				// }
				
				// let goodsList = await this.$api.json('goodsList');
				// if(type === 'refresh'){
				// 	this.goodsList = [];
				// }
				// //筛选，测试数据直接前端筛选了
				// if(this.filterIndex === 1){
				// 	goodsList.sort((a,b)=>b.sales - a.sales)
				// }
				// if(this.filterIndex === 2){
				// 	goodsList.sort((a,b)=>{
				// 		if(this.priceOrder == 1){
				// 			return a.price - b.price;
				// 		}
				// 		return b.price - a.price;
				// 	})
				// }
				
				// this.goodsList = this.goodsList.concat(goodsList);
				
				// //判断是否还有下一页，有是more  没有是nomore(测试数据判断大于20就没有了)
				// this.loadingType  = this.goodsList.length > 20 ? 'nomore' : 'more';
				// if(type === 'refresh'){
				// 	if(loading == 1){
				// 		uni.hideLoading()
				// 	}else{
				// 		uni.stopPullDownRefresh();
				// 	}
				// }
			// },
			
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
					url: `/pages/product/product?id=${id}`
				})
			},
			
			
			stopPrevent(){}
		},
	}
</script>

<style lang="scss">
	@import url("seller_product.css");
	page, .content{
		background: $page-color-base;
	}
	.content{
		// padding-top: 82upx;
	}
	// 轮播图
	.carousel {
		height: 280upx;
		position:relative;
		swiper{
			width: 100%;
			height:100%;
		}
		.image-wrapper{
			width: 100%;
			height: 100%;
			/* border:1px solid red; */
		}
		.swiper-item {
			display: flex;
			justify-content: center;
			align-content: center;
			height: 750upx;
			overflow: hidden;
			image {
				width: 100%;
				height: 100%;
			}
		}
		
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
			width: 350upx;
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
		padding: 10upx 30upx;
		background: #fff;
		.goods-item{
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
		.price-box{
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding-right: 10upx;
			font-size: 24upx;
			color: $font-color-light;
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
	}
	

</style>
