<template>
	<view class="container">
		<view class="carousel">
			<swiper indicator-dots autoplay interval="3000">
				<swiper-item class="swiper-item" v-for="(item,index) in storeInfo.slider_image" :key="index" >
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
		
		<view class="introduce-section">
			<text class="title">{{storeInfo.store_name}}</text>
			<view class="price-box">
				<text class="price-tip">¥</text>
				<text class="price">{{storeInfo.price}}</text>
				<text class="m-price">¥{{storeInfo.ot_price}}</text>
				<!-- <text class="coupon-tip">7折</text> -->
			</view>
			<view class="bot-row">
				<text>销量: {{storeInfo.sales}}{{storeInfo.unit_name}}</text>
				<text>库存: {{storeInfo.stock}}{{storeInfo.unit_name}}</text>
				<text>浏览量: {{storeInfo.browse}}</text>
			</view>
		</view>
		
		<!--  分享 -->
		<!-- <view class="share-section" @click="share">
			<view class="share-icon">
				<text class="yticon icon-xingxing"></text>
				 返
			</view>
			<text class="tit">该商品分享可领49减10红包</text>
			<text class="yticon icon-bangzhu1"></text>
			<view class="share-btn">
				立即分享
				<text class="yticon icon-you"></text>
			</view>
			
		</view> -->
		
		<view class="c-list">
			<view class="c-row b-b" @click="toggleSpec">
				<text class="tit">购买类型</text>
				<view class="con">
					<text class="selected-text" v-for="(item,index) in value" :key="index">
						{{item}}
					</text>
				</view>
				<text class="yticon icon-you"></text>
			</view>
			<!-- <view class="c-row b-b">
				<text class="tit">优惠券</text>
				<text class="con t-r red">领取优惠券</text>
				<text class="yticon icon-you"></text>
			</view>
			<view class="c-row b-b">
				<text class="tit">促销活动</text>
				<view class="con-list">
					<text>新人首单送20元无门槛代金券</text>
					<text>订单满50减10</text>
					<text>订单满100减30</text>
					<text>单笔购买满两件免邮费</text>
				</view>
			</view> -->
			<view class="c-row b-b">
				<text class="tit">服务</text>
				<view class="bz-list con">
					<text>7天无理由退换货 ·</text>
					<text>假一赔十 ·</text>
				</view>
			</view>
		</view>
		
		<!-- 评价 -->
		<!-- <view class="eva-section">
			<view class="e-header">
				<text class="tit">评价</text>
				<text>(86)</text>
				<text class="tip">好评率 100%</text>
				<text class="yticon icon-you"></text>
			</view> 
			<view class="eva-box">
				<image class="portrait" src="http://img3.imgtn.bdimg.com/it/u=1150341365,1327279810&fm=26&gp=0.jpg" mode="aspectFill"></image>
				<view class="right">
					<text class="name">Leo yo</text>
					<text class="con">商品收到了，79元两件，质量不错，试了一下有点瘦，但是加个外罩很漂亮，我很喜欢</text>
					<view class="bot">
						<text class="attr">购买类型：XL 红色</text>
						<text class="time">2019-04-01 19:21</text>
					</view>
				</view>
			</view>
		</view> -->
		
		<view class="detail-desc">
			<view class="d-header">
				<text>图文详情</text>
			</view>
			<rich-text class="richimg" :nodes="storeInfo.description"></rich-text>
		</view>
		
		<!-- 底部操作菜单 -->
		<view class="page-bottom">
			<navigator url="/pages/index/index" open-type="switchTab" class="p-b-btn">
				<text class="yticon icon-xiatubiao--copy"></text>
				<text>首页</text>
			</navigator>
			<navigator url="/pages/cart/cart" open-type="switchTab" class="p-b-btn">
				<text class="yticon icon-gouwuche"></text>
				<text>购物车</text>
			</navigator>
			<view class="p-b-btn" :class="{active: storeInfo.userCollect}" @click="toFavorite">
				<text class="yticon icon-shoucang"></text>
				<text>收藏</text>
			</view>
			
			<view class="action-btn-group">
				<!-- <button type="primary" class=" action-btn no-border buy-now-btn" @click="buy">立即购买</button>
				<button type="primary" class=" action-btn no-border add-cart-btn"  @click="toggleSpec">加入购物车</button> -->
				<button type="primary" class=" action-btn no-border add-cart-btn" :data-id="1"  @click="toggleSpec">加入购物车</button>
				<button type="primary" class=" action-btn no-border buy-now-btn" :data-id="2" @click="toggleSpec">立即购买</button>
			</view>
		</view>
		
		
		<!-- 规格-模态层弹窗 -->
		<view 
			class="popup spec" 
			:class="specClass"
			@touchmove.stop.prevent="stopPrevent"
			@click="toggleSpec"
		>
			<!-- 遮罩层 -->
			<view class="mask"></view>
			<view class="layer attr-content" @click.stop="stopPrevent">
				<view class="a-t">
					<image :src="indexUrl(productSelect.image)"></image>
					<view class="right">
						<text class="maskname">{{storeInfo.store_name}}</text>
						<text class="price maskpri">¥{{productSelect.price}}</text>
						<text class="stock">库存：{{productSelect.stock}}</text>
						<!-- <view class="selected">
							已选：
							<text class="selected-text" >
								{{value}}
							</text>
						</view> -->
					</view>
				</view>
				<view v-for="(item,index) in productAttr" :key="index" class="attr-list">
					<text>{{item.attr_name}}</text>
					<view class="item-list">
						<text 
							v-for="(childItem, childIndex) in item.attr_values" 
							:key="childIndex" class="tit"
							:class='{on:testNum[index]==childIndex}'
							@click="selectSpec(index,childIndex,childItem)"
						>
							{{childItem}}
						</text>
						
					</view>
				</view>
				
				<view class="makenum">数量</view>
				<view class="acea-row cnum">
					<view class="clnum clnuml" :class="{onnuml:num <= 1}"  @click="reduceNum">-</view>
					<view class="clnum clinum">{{num}}</view>
					<view class="clnum clnumr aa" :class="{onnumr:num >= productSelect.stock}" @click="addNum">+</view>
				</view>
				
				<button class="btn" @click="complete">完成</button>
			</view>
		</view>
		<!-- 分享 -->
		<share 
			ref="share" 
			:contentHeight="580"
			:shareList="shareList"
		></share>
	</view>
</template>

<script>
	import share from '@/components/share';
	export default{
		components: {
			share
		},
		data() {
			return {
				testNum:[],
				specClass: 'none',
				specSelected:[],
				productAttr:[],
				testarr:[],
				productValue:[],
				productSelect:[],
				storeInfo:[
					{
						store_name:'',
					}
				],
				value:[],
				banner:[],
				detid:0,
				favorite: false,
				shareList: [],
				clickId:0,
				num:1,
				unique:'',
			};
		},
		onLoad(options){
			let self = this;
			self.detid = options.id
			self.details();
			let id = options.id;
		},
		methods:{
			// 详情
			details(){
				let self = this;
				let path = '/gzh/store_api/details?id='+self.detid
				getApp().http(path, {}, function(result) {
					self.storeInfo = result.storeInfo
					
					getApp().user_api(true,self.storeInfo.store_name,self.storeInfo.store_info,self.storeInfo.slider_image[0]);
					for(let i = 0 ; i < result.productAttr.length ; i++ ){
						// 默认选中第一条
						self.testNum.push(0);
						// 默认类型现实
						self.value[i] = result.productAttr[i].attr_values[0]
						
					}
					self.productAttr = result.productAttr
					self.testarr = result.productAttr
					self.productValue = result.productValue
					
					// 默认sku
					let provalue = self.value
					let prodata =provalue.join(',')
					// let prodata =provalue.substring(0,provalue.length-1)
					
					// 是否有sku
					if(prodata){
						self.productSelect = self.productValue[prodata];
						self.unique = self.productSelect.unique
						console.log('不为空',self.unique)
						
					}else{
						console.log('空')
						self.productSelect.image = self.storeInfo.slider_image[0];
						self.productSelect.price= self.storeInfo.price
						self.productSelect.stock= self.storeInfo.stock
					}
					
				})
			},
			
			//规格弹窗开关
			toggleSpec(e) {
				if(this.specClass === 'show'){
					this.specClass = 'hide';
					// self.clickId = 0;
					setTimeout(() => {
						this.specClass = 'none';
					}, 250);
				}else if(this.specClass === 'none'){
					this.specClass = 'show';
					this.clickId = e.currentTarget.dataset.id;
					console.log('点击',e);
					console.log('打开',this.clickId)
					
					
				}
			},
			//选择规格
			selectSpec(index,child,childItem){
				let self = this;
				self.testNum[index] = child;
				
				
				self.productAttr =[];
				// self.value = [];
				self.$nextTick(() => {
					self.productAttr = self.testarr;
					
					// 改变sku
					let provalue = self.value
					self.value[index] = childItem;
					let prodata =provalue.join(',')
					self.productSelect = self.productValue[prodata];
					self.unique = self.productSelect.unique
					console.log('啊啊啊啊啊',self.unique);
					// console.log(self.productSelect);
					// console.log('点击改变',self.value)
				})
				
				console.log(index,child,childItem)
				
			},
			
			addNum(){
				let self = this;
				console.log('数量',self.num,self.productSelect.stockself)
				if(self.num >= self.productSelect.stock){
					console.log('不加')
				}else{
					console.log('加')
					self.num = self.num +1;
				}
			},
			reduceNum(){
				let self = this;
				if(self.num == 1){
					console.log('不减')
				}else{
					self.num = self.num -1;
				}
			},
			// 完成
			complete(){
				let self = this;
				
				self.specClass = 'hide';
				setTimeout(() => {
					self.specClass = 'none';
				}, 250);
				console.log('完成',self.clickId);
				if(self.clickId == '1'){
					console.log('购物车')
					
					let path = '/gzh/auth_api/set_cart'
					let data = {
						productId:self.detid,
						cartNum:self.num,
						uniqueId:self.unique
					};
					getApp().http(path, data, function(res) {
						console.log('数据',res);
						if(res.code == 200){
							getApp().showTip(res.msg, "success")
						}
						
					},true)
					
				}else{
					// console.log('购买');
					let path = '/gzh/deal_api/now_buy'
					let data = {
						productId:self.detid,
						cartNum:self.num,
						uniqueId:self.unique,
						combinationId:0,
						secKillId:0,
						bargainId:0,
					};
					getApp().http(path, data, function(res) {
						console.log('购买',res);
						
						if(res.code == 200){
							// getApp().showTip(res.msg, "success")
							uni.navigateTo({
								url: `/pages/order/createOrder?cartId=`+res.data.cartId
							})
							
						}else{
							getApp().showTip(res.msg, "none")
						}
						
					},true,'post')
				}
				
			},
			
			indexUrl(icon){
				let http = icon.indexOf('http')
				if(http == -1){
					return this.webUrl +'/'+icon
				}else{
					return icon;
				}
			},
			//分享
			share(){
				// this.$refs.share.toggleMask();	
			},
			//收藏
			toFavorite(){
				let self = this;
				self.storeInfo.userCollect = !self.storeInfo.userCollect;
				if(self.storeInfo.userCollect){
					
					let path = '/gzh/store_api/collect_product'
					let data = {productId:self.detid}
					getApp().http(path,data, function(res) {
						console.log('收藏',self.favorite)
						if(res.code == 200){
							getApp().showTip(res.msg, "success")
						}else{
							getApp().showTip(res.msg, "none")
						}
					},true);
					
					
				}else{
					console.log('取消')
					let path = '/gzh/store_api/uncollect_product'
					let data = {productId:self.detid}
					getApp().http(path,data, function(res) {
						if(res.code == 200){
							getApp().showTip(res.msg, "success")
						}else{
							getApp().showTip(res.msg, "none")
						}
					},true);
				}
			},
			buy(){
				uni.navigateTo({
					url: `/pages/order/createOrder`
				})
			},
			stopPrevent(){
			}
		},

	}
</script>

<style lang='scss'>
	@import url("product.css");
	
	page{
		background: $page-color-base;
		padding-bottom: 160upx;
	}
	.icon-you{
		font-size: $font-base + 2upx;
		color: #888;
	}
	.carousel {
		height: 720upx;
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
	
	/* 标题简介 */
	.introduce-section{
		background: #fff;
		padding: 20upx 30upx;
		
		.title{
			font-size: 32upx;
			color: $font-color-dark;
			height: 50upx;
			line-height: 50upx;
		}
		.price-box{
			display:flex;
			align-items:baseline;
			height: 64upx;
			padding: 10upx 0;
			font-size: 26upx;
			color:$uni-color-primary;
		}
		.price{
			font-size: $font-lg + 2upx;
		}
		.m-price{
			margin:0 12upx;
			color: $font-color-light;
			text-decoration: line-through;
		}
		.coupon-tip{
			align-items: center;
			padding: 4upx 10upx;
			background: $uni-color-primary;
			font-size: $font-sm;
			color: #fff;
			border-radius: 6upx;
			line-height: 1;
			transform: translateY(-4upx); 
		}
		.bot-row{
			display:flex;
			align-items:center;
			height: 50upx;
			font-size: $font-sm;
			color: $font-color-light;
			text{
				flex: 1;
			}
		}
	}
	/* 分享 */
	.share-section{
		display:flex;
		align-items:center;
		color: $font-color-base;
		background: linear-gradient(left, #fdf5f6, #fbebf6);
		padding: 12upx 30upx;
		.share-icon{
			display:flex;
			align-items:center;
			width: 70upx;
			height: 30upx;
			line-height: 1;
			border: 1px solid $uni-color-primary;
			border-radius: 4upx;
			position:relative;
			overflow: hidden;
			font-size: 22upx;
			color: $uni-color-primary;
			&:after{
				content: '';
				width: 50upx;
				height: 50upx;
				border-radius: 50%;
				left: -20upx;
				top: -12upx;
				position:absolute;
				background: $uni-color-primary;
			}
		}
		.icon-xingxing{
			position:relative;
			z-index: 1;
			font-size: 24upx;
			margin-left: 2upx;
			margin-right: 10upx;
			color: #fff;
			line-height: 1;
		}
		.tit{
			font-size: $font-base;
			margin-left:10upx;
			/* border:1px solid red; */
		}
		.icon-bangzhu1{
			padding: 10upx;
			font-size: 30upx;
			line-height: 1;
		}
		.share-btn{
			flex: 1;
			text-align:right;
			font-size: $font-sm;
			color: $uni-color-primary;
		}
		.icon-you{
			font-size: $font-sm;
			margin-left: 4upx;
			color: $uni-color-primary;
		}
	}
	
	.c-list{
		font-size: $font-sm + 2upx;
		color: $font-color-base;
		background: #fff;
		.c-row{
			display:flex;
			align-items:center;
			padding: 20upx 30upx;
			position:relative;
		}
		.tit{
			width: 140upx;
		}
		.con{
			flex: 1;
			color: $font-color-dark;
			.selected-text{
				margin-right: 10upx;
			}
		}
		.bz-list{
			height: 40upx;
			font-size: $font-sm+2upx;
			color: $font-color-dark;
			text{
				display: inline-block;
				margin-right: 30upx;
			}
		}
		.con-list{
			flex: 1;
			display:flex;
			flex-direction: column;
			color: $font-color-dark;
			line-height: 40upx;
		}
		.red{
			color: $uni-color-primary;
		}
	}
	
	/* 评价 */
	.eva-section{
		display: flex;
		flex-direction: column;
		padding: 20upx 30upx;
		background: #fff;
		margin-top: 16upx;
		.e-header{
			display: flex;
			align-items: center;
			height: 70upx;
			font-size: $font-sm + 2upx;
			color: $font-color-light;
			.tit{
				font-size: $font-base + 2upx;
				color: $font-color-dark;
				margin-right: 4upx;
			}
			.tip{
				flex: 1;
				text-align: right;
			}
			.icon-you{
				margin-left: 10upx;
			}
		}
	}
	.eva-box{
		display: flex;
		padding: 20upx 0;
		.portrait{
			flex-shrink: 0;
			width: 80upx;
			height: 80upx;
			border-radius: 100px;
		}
		.right{
			flex: 1;
			display: flex;
			flex-direction: column;
			font-size: $font-base;
			color: $font-color-base;
			padding-left: 26upx;
			.con{
				font-size: $font-base;
				color: $font-color-dark;
				padding: 20upx 0;
			}
			.bot{
				display: flex;
				justify-content: space-between;
				font-size: $font-sm;
				color:$font-color-light;
			}
		}
	}
	/*  详情 */
	.detail-desc{
		background: #fff;
		margin-top: 16upx;
		.d-header{
			display: flex;
			justify-content: center;
			align-items: center;
			height: 80upx;
			font-size: $font-base + 2upx;
			color: $font-color-dark;
			position: relative;
				
			text{
				padding: 0 20upx;
				background: #fff;
				position: relative;
				z-index: 1;
			}
			&:after{
				position: absolute;
				left: 50%;
				top: 50%;
				transform: translateX(-50%);
				width: 300upx;
				height: 0;
				content: '';
				border-bottom: 1px solid #ccc; 
			}
		}
	}
	
	/* 规格选择弹窗 */
	.attr-content{
		padding: 10upx 30upx;
		.a-t{
			display: flex;
			image{
				width: 170upx;
				height: 170upx;
				flex-shrink: 0;
				margin-top: -40upx;
				border-radius: 8upx;;
			}
			.right{
				display: flex;
				flex-direction: column;
				padding-left: 24upx;
				font-size: $font-sm + 2upx;
				color: $font-color-base;
				line-height: 42upx;
				.price{
					font-size: 31upx;
					color: $uni-color-primary;
					/* margin-bottom: 10upx; */
				}
				.selected-text{
					margin-right: 10upx;
				}
			}
		}
		.attr-list{
			display: flex;
			flex-direction: column;
			font-size: $font-base + 2upx;
			color: $font-color-base;
			padding-top: 30upx;
			padding-left: 10upx;
		}
		.item-list{
			padding: 20upx 0 0;
			display: flex;
			flex-wrap: wrap;
			text{
				display: flex;
				align-items: center;
				justify-content: center;
				background: #eee;
				margin-right: 20upx;
				margin-bottom: 20upx;
				border-radius: 100upx;
				min-width: 60upx;
				height: 50upx;
				padding: 0 20upx;
				font-size: $font-base;
				color: $font-color-dark;
			}
			.on{
				background: #fbebee;
				color: $uni-color-primary;
			}
		}
	}
	
	/*  弹出层 */
	.popup {
		position: fixed;
		left: 0;
		top: 0;
		right: 0;
		bottom: 0;
		z-index: 99;
		
		&.show {
			display: block;
			.mask{
				animation: showPopup 0.2s linear both;
			}
			.layer {
				animation: showLayer 0.2s linear both;
			}
		}
		&.hide {
			.mask{
				animation: hidePopup 0.2s linear both;
			}
			.layer {
				animation: hideLayer 0.2s linear both;
			}
		}
		&.none {
			display: none;
		}
		.mask{
			position: fixed;
			top: 0;
			width: 100%;
			height: 100%;
			z-index: 1;
			background-color: rgba(0, 0, 0, 0.4);
		}
		.layer {
			position: fixed;
			z-index: 99;
			bottom: 0;
			width: 100%;
			min-height: 40vh;
			border-radius: 10upx 10upx 0 0;
			background-color: #fff;
			.btn{
				height: 66upx;
				line-height: 66upx;
				border-radius: 100upx;
				background: $uni-color-primary;
				font-size: $font-base + 2upx;
				color: #fff;
				margin: 30upx auto 20upx;
			}
		}
		@keyframes showPopup {
			0% {
				opacity: 0;
			}
			100% {
				opacity: 1;
			}
		}
		@keyframes hidePopup {
			0% {
				opacity: 1;
			}
			100% {
				opacity: 0;
			}
		}
		@keyframes showLayer {
			0% {
				transform: translateY(120%);
			}
			100% {
				transform: translateY(0%);
			}
		}
		@keyframes hideLayer {
			0% {
				transform: translateY(0);
			}
			100% {
				transform: translateY(120%);
			}
		}
	}
	
	/* 底部操作菜单 */
	.page-bottom{
		position:fixed;
		left: 30upx;
		bottom:30upx;
		z-index: 95;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 690upx;
		height: 100upx;
		background: rgba(255,255,255,.9);
		box-shadow: 0 0 20upx 0 rgba(0,0,0,.5);
		border-radius: 16upx;
		
		.p-b-btn{
			display:flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			font-size: $font-sm;
			color: $font-color-base;
			width: 96upx;
			height: 80upx;
			.yticon{
				font-size: 40upx;
				line-height: 48upx;
				color: $font-color-light;
			}
			&.active, &.active .yticon{
				color: $uni-color-primary;
			}
			.icon-fenxiang2{
				font-size: 42upx;
				transform: translateY(-2upx);
			}
			.icon-shoucang{
				font-size: 46upx;
			}
		}
		.action-btn-group{
			display: flex;
			height: 76upx;
			border-radius: 100px;
			overflow: hidden;
			box-shadow: 0 20upx 40upx -16upx #fa436a;
			box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
			background: linear-gradient(to right, #ffac30,#fa436a,#F56C6C);
			margin-left: 20upx;
			position:relative;
			&:after{
				content: '';
				position:absolute;
				top: 50%;
				right: 50%;
				transform: translateY(-50%);
				height: 28upx;
				width: 0;
				border-right: 1px solid rgba(255,255,255,.5);
			}
			.action-btn{
				display:flex;
				align-items: center;
				justify-content: center;
				width: 180upx;
				height: 100%;
				font-size: $font-base ;
				padding: 0;
				border-radius: 0;
				background: transparent;
			}
		}
	}
	
</style>
