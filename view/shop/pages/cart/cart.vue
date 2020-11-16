<template>
	<view class="container">
		<!-- 空白页 -->
	<!-- 	<view v-if="!hasLogin || empty===true" class="empty">
			<image src="/static/emptyCart.jpg" mode="aspectFit"></image>
			<view v-if="hasLogin" class="empty-tips">
				空空如也
				<navigator class="navigator" v-if="hasLogin" url="../index/index" open-type="switchTab">随便逛逛></navigator>
			</view>
			<view v-else class="empty-tips">
				空空如也
				<view class="navigator" @click="navToLogin">去登陆></view>
			</view>
		</view> -->
		<view >
			<!-- 列表 -->
			<!-- <scroll-view scroll-with-animation scroll-y class="right-aside" style="border:1px solid red" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr"> -->
	
			<view class="cart-list">
				<block v-for="(item, index) in cartList" :key="item.id">
					<view
						class="cart-item" 
						:class="{'b-b': index!==cartList.length-1}"
					>
						<view class="image-wrapper" >
							<image :src="indexUrl(item.productInfo.image)" 
								class="loaded"
								mode="aspectFill" 
								lazy-load 
								@click="nav(item.productInfo.id)"
							></image>
							<view 
								class="yticon icon-xuanzhong2 checkbox"
								:class="{checked: item.checked}"
								:data-id='1'
								@click="check('item', index,$event)"
							></view>
						</view>
						<view class="item-right">
							<text class="clamp title">{{item.productInfo.store_name}}</text>
							<text v-if="item.productInfo.attrInfo">
								<text class="attr">{{item.productInfo.attrInfo.suk}}</text>
							</text>
							<text v-else></text>
							<text class="price">¥{{item.productInfo.attrInfo ? item.productInfo.attrInfo.price : item.productInfo.price}}</text>
							<!-- <uni-number-box 
								class="step"
								:min="1" 
								:max="item.productInfo.attrInfo ? item.productInfo.attrInfo.stock : item.productInfo.stock"
								:value="item.cart_num>item.productInfo.stock?item.productInfo.stock:item.cart_num"
								:isMax="item.cart_num>=item.productInfo.stock?true:false"
								:isMin="item.cart_num===1"
								:index="index"
								:dataItem="item"
								@eventChange="numberChange"
							></uni-number-box> -->
							
							<view class="acea-row cnum">
								<view class="clnum clnuml" :class="{onnuml:item.cart_num <= 1}"  @click="reduceNum(index,item)">-</view>
								<view class="clnum clinum">{{item.cart_num}}</view>
								<view class="clnum clnumr" :class="{onnumr:item.cart_num >= [item.productInfo.attrInfo?item.productInfo.attrInfo.stock:item.productInfo.stock]}" @click="addNum(index,item)">+</view>
							</view>
							
							
						</view>
						<text v-if="clickdel">
							<text class="del-btn yticon icon-fork" @click="deleteCartItem(item)"></text>
						</text>
					</view>
				</block>
			
				<!-- <uni-load-more :status="status" :icon-size="16" :content-text="contentText" /> -->
			</view>
			<!-- </scroll-view> -->
			
			
			
			<!-- 底部菜单栏 -->
			<view class="action-section">
				<view class="checkbox acea-row">
					<image 
						:src="allChecked?'/static/selected.png':'/static/select.png'" 
						mode="aspectFit"
						@click="check('all')"
					></image>
					<view class="allshop">全选</view>
					<!-- 全选 -->
					<!-- <view class="clear-btn" :class="{show: allChecked}" @click="clearCart">
						清空
					</view> -->
				</view>
				<view class="total-box">
					<text class="price">¥{{total}}</text>
					<!-- <text class="coupon">
						已优惠
						<text>74.35</text>
						元
					</text> -->
				</view>
				<button type="primary" class="no-border confirm-btn" @click="createOrder">去结算</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import uniNumberBox from '@/components/uni-number-box.vue'
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		components: {
			uniNumberBox,
			uniLoadMore
		},
		data() {
			return {
				total: 0, //总价格
				allChecked: false, //全选状态  true|false
				empty: false, //空白页现实  true|false
				cartList: [],
				cartListArr:[],
				num:1,
				selectValue:[],
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
				limit:2,
				status: 'more',
				clickdel:true,
			};
		},
		onLoad(){
			this.calcSize();
			getApp().user_api(true);
		},
		onShow(){
			this.selectValue = [];
			this.total = 0;
			this.allChecked = false;
			this.getCartList();
		},
		watch:{
			//显示空白页
			cartList(e){
				// let empty = e.length === 0 ? true: false;
				// if(this.empty !== empty){
				// 	this.empty = empty;
				// }
			}
		},
		computed:{
			...mapState(['hasLogin'])
		},
		methods: {
			getCartList(){
				let self = this;
				let path = '/gzh/auth_api/get_cart_list'
				// let data = {page:self.page,limit:self.limit};
				getApp().http(path, {}, function(result) {
					
					// let list = result.valid;
					// if (list.length > 0) {
						
						// self.status = list.length >= 2 ? "more" : "";
						// if (self.status == "more") {
						// 	self.page += 1;
							
						// 	console.log(self.page);
						// 	console.log('=================444444444444')
							
						// }

						self.cartList = result.valid
						self.cartListArr = result.valid
						
					// }else{
					// 	self.status = "";
					// }
					
					console.log(result);
					console.log(self.cartList)
					console.log('============456465456')
				})
			},
			nav(id){
				uni.navigateTo({
					url: "/pages/product/product?id="+id
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
			//监听image加载完成
			onImageLoad(key, index) {
				this.$set(this[key][index], 'loaded', 'loaded');
			},
			//监听image加载失败
			onImageError(key, index) {
				this[key][index].image = '/static/errorImage.jpg';
			},
			navToLogin(){
				uni.navigateTo({
					url: '/pages/public/login'
				})
			},
			 //选中状态处理
			check(type, index,e){
				console.log(type,index,e)
				if(type === 'item'){
					this.cartList = [];
					this.$nextTick(() => {
						this.cartList = this.cartListArr
						this.cartList[index].checked = !this.cartList[index].checked;
						let isshow = this.cartList[index].checked
						
						let val = this.selectValue;
						let id= this.cartList[index].id
						
						let isVal = val.indexOf(id)
						if(isVal == '-1'){
							console.log('不存在')
							val.push(id)
						}else{
							console.log('存在 不添加',isVal)
							val.splice(isVal,1)
						}
						this.calcTotal();
					})
					
				}else{
					let checked = !this.allChecked
					let list = this.cartList;
					list.forEach(item=>{
						item.checked = checked;
					})
					// this.allChecked = this.allChecked;
					
					console.log('dddddddddddddd',this.allChecked)
					
					if(this.allChecked){
						this.clickdel = true;
					}else{
						this.clickdel = false;
					}
					
					
					console.log('全选选中',this.allChecked);
					
					// 是否选中
					if(this.allChecked){
						this.selectValue = [];
					}else{
						// 选中
						this.selectValue = [];
						for(let i = 0 ; i < this.cartList.length ; i++ ){
							console.log('全选')
							let val = this.selectValue;
							let id= this.cartList[i].id
							
							let isVal = val.indexOf(id)
							// if(isVal == '-1'){
								val.push(id)
							// }else{
								// val.splice(isVal,1)
							// }
						}
					}
					
					
					
					// this.selectValue = [];
					
					console.log('全选',this.selectValue);
					

					this.calcTotal();

				}
			
			},
			addNum(index,item){
				let self = this;
				let info = self.cartList[index].productInfo;
				let infoNum = info.attrInfo?info.attrInfo.stock : info.stock
				console.log(infoNum)
				console.log('数量',index,item,self.cartList)
				if(item.cart_num >= infoNum){
					console.log('不加')
				}else{
					console.log('加')
					let id = self.cartList[index].id
					self.cartList[index].cart_num = item.cart_num +1;
					self.clickNum(id,self.cartList[index].cart_num)
				}
				self.calcTotal();
			},
			reduceNum(index,item){
				let self = this;
				console.log(self.cartList[index].cart_num);
				console.log('啊啊啊啊',index,item)
				
				if(item.cart_num == 1){
					console.log('不减')
				}else{
					let id = self.cartList[index].id
					self.cartList[index].cart_num = item.cart_num -1;
					self.clickNum(id,self.cartList[index].cart_num)
				}
				self.calcTotal();
			},
			clickNum(id,newValue){
				let path = '/gzh/auth_api/change_cart_num'
				let data = {
					cartId:id,
					cartNum:newValue
				};
				getApp().http(path, data, function(res) {
					console.log(res);
				})
			},
			//删除
			deleteCartItem(item){
				// let list = this.cartList;
				// let row = list[index];
				
				let self = this;
				let id = item.id;
				
				
				uni.showModal({
					title: '删除',
					content: '确定删除商品吗',
					success: (md) => {
						console.log('sssss')
						if(md.confirm == true){
							let path = '/gzh/auth_api/remove_cart'
							let data = {ids:id}
							getApp().http(path, data, function(result) {
								
								if(result.code == 200){
									getApp().showTip(result.msg, "success")
									self.getCartList();
								}
								console.log(result)
							},true)
							
							
							console.log('删除',item)
							
							// this.cartList.splice(index, 1);
							self.calcTotal();
							uni.hideLoading();
						}
					}
				})
				
				
			},
			//清空
			clearCart(){
				uni.showModal({
					content: '清空购物车？',
					success: (e)=>{
						if(e.confirm){
							this.cartList = [];
						}
					}
				})
			},
			//计算总价
			calcTotal(){
				let self = this;
				// total
				let validList = self.cartList;
				let selectVal = self.selectValue;//选中
				let selectCountPrice  = 0.00;
				// let id = validList[index].id
				
				console.log('选中',selectVal.length);
				console.log('列表',validList.length)
				
				if(selectVal.length == validList.length){
					self.allChecked = true;
				}else{
					self.allChecked = false;
				}
				
				if (selectVal.length < 1){
					self.total =0
				}else{
					for (var info in validList){
						let isVal = selectVal.includes(validList[info].id)
						let valPrice = validList[info].productInfo.attrInfo ? validList[info].productInfo.attrInfo.price : validList[info].productInfo.price
						if(isVal){
							// console.log('选中？？？？？？')
							selectCountPrice = Number(selectCountPrice) + Number(validList[info].cart_num) * Number(valPrice)
							self.total =selectCountPrice
						}
						// console.log('顶顶顶顶',selectCountPrice);
					}
				}
				
				
				
			},
			// 到底触发
			scroAddr(){
				let self = this;
				if(self.status){
					self.getCartList();
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".container");
						self.scrollHeight = res.windowHeight - 200
						// self.scrollHeight = 200
						console.log(info);
						console.log(self.scrollHeight);
						console.log('高度高度高度高度高度高度',res);
					}
				});
			},
			
			//创建订单
			createOrder(){
				let self = this;
				let cartArr = self.selectValue
				let cartId = cartArr.toString();
				console.log('的风格的风格地方',cartId);
				let path = '/gzh/deal_api/now_buy'
				
				if(cartArr == ''){
					getApp().showTip('您还没有选择要购买的商品', "none")
				}else{
					uni.navigateTo({
						url: `/pages/order/createOrder?cartId=`+cartId
					})
				}
				
				
			}
		}
	}
</script>

<style lang='scss'>
	@import url("cart.css");
	.container{
		padding-bottom: 134upx;
		/* 空白页 */
		.empty{
			position:fixed;
			left: 0;
			top:0;
			width: 100%;
			height: 100vh;
			padding-bottom:100upx;
			display:flex;
			justify-content: center;
			flex-direction: column;
			align-items:center;
			background: #fff;
			image{
				width: 240upx;
				height: 160upx;
				margin-bottom:30upx;
			}
			.empty-tips{
				display:flex;
				font-size: $font-sm+2upx;
				color: $font-color-disabled;
				.navigator{
					color: $uni-color-primary;
					margin-left: 16upx;
				}
			}
		}
	}
	/* 购物车列表项 */
	.cart-item{
		display:flex;
		position:relative;
		padding:30upx 40upx;
		.image-wrapper{
			width: 230upx;
			height: 230upx;
			flex-shrink: 0;
			position:relative;
			image{
				border-radius:8upx;
			}
		}
		.checkbox{
			position:absolute;
			left:-16upx;
			top: -16upx;
			z-index: 8;
			font-size: 44upx;
			line-height: 1;
			padding: 4upx;
			color: $font-color-disabled;
			background:#fff;
			border-radius: 50px;
		}
		.item-right{
			display:flex;
			flex-direction: column;
			flex: 1;
			overflow: hidden;
			position:relative;
			padding-left: 30upx;
			.title,.price{
				font-size:$font-base + 2upx;
				color: $font-color-dark;
				height: 40upx;
				line-height: 40upx;
			}
			.attr{
				font-size: $font-sm + 2upx;
				color: $font-color-light;
				height: 50upx;
				line-height: 50upx;
			}
			.price{
				height: 50upx;
				line-height:50upx;
			}
		}
		.del-btn{
			padding:4upx 10upx;
			font-size:34upx; 
			height: 50upx;
			color: $font-color-light;
		}
	}
	/* 底部栏 */
	.action-section{
		/* #ifdef H5 */
		margin-bottom:100upx;
		/* #endif */
		position:fixed;
		left: 30upx;
		bottom:30upx;
		z-index: 95;
		display: flex;
		align-items: center;
		width: 690upx;
		height: 100upx;
		padding: 0 30upx;
		background: rgba(255,255,255,.9);
		box-shadow: 0 0 20upx 0 rgba(0,0,0,.5);
		border-radius: 16upx;
		.checkbox{
			height:52upx;
			position:relative;
			image{
				width: 52upx;
				height: 100%;
				position:relative;
				z-index: 5;
			}
		}
		.clear-btn{
			position:absolute;
			left: 26upx;
			top: 0;
			z-index: 4;
			width: 0;
			height: 52upx;
			line-height: 52upx;
			padding-left: 38upx;
			font-size: $font-base;
			color: #fff;
			background: $font-color-disabled;
			border-radius:0 50px 50px 0;
			opacity: 0;
			transition: .2s;
			&.show{
				opacity: 1;
				width: 120upx;
			}
		}
		.total-box{
			flex: 1;
			display:flex;
			flex-direction: column;
			text-align:right;
			padding-right: 40upx;
			.price{
				font-size: $font-lg;
				color: $font-color-dark;
			}
			.coupon{
				font-size: $font-sm;
				color: $font-color-light;
				text{
					color: $font-color-dark;
				}
			}
		}
		.confirm-btn{
			padding: 0 38upx;
			margin: 0;
			border-radius: 100px;
			height: 76upx;
			line-height: 76upx;
			font-size: $font-base + 2upx;
			background: $uni-color-primary;
			box-shadow: 1px 2px 5px rgba(217, 60, 93, 0.72)
		}
	}
	/* 复选框选中状态 */
	.action-section .checkbox.checked,
	.cart-item .checkbox.checked{
		color: $uni-color-primary;
	}
</style>
