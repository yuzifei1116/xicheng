<template>
	<view>
		<form @submit="formSubmit" @reset="formReset">
			<!-- 地址 -->
			<view @click="clickAdd" class="address-section">
				<view class="order-content">
					<text class="yticon icon-shouhuodizhi"></text>
					<view class="cen">
						<view v-if="addressData == ''">
							<view class="">
								设置收货地址
							</view>
						</view>
						<view v-else>
							<view class="top">
								<text class="name">{{addressData.real_name}}</text>
								<text class="mobile">{{addressData.phone}}</text>
							</view>
							<text class="address">{{addressData.province}}{{addressData.city}}{{addressData.district}} {{addressData.detail}}</text>
						</view>
					</view>
					<text class="yticon icon-you"></text>
				</view>
			</view>
			<image class="a-bg" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAu4AAAAFCAYAAAAaAWmiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Rjk3RjkzMjM2NzMxMTFFOUI4RkU4OEZGMDcxQzgzOEYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Rjk3RjkzMjQ2NzMxMTFFOUI4RkU4OEZGMDcxQzgzOEYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpGOTdGOTMyMTY3MzExMUU5QjhGRTg4RkYwNzFDODM4RiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpGOTdGOTMyMjY3MzExMUU5QjhGRTg4RkYwNzFDODM4RiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PrEOZlQAAAiuSURBVHjazJp7bFvV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"></image>

			<view class="goods-section">
				<view class="g-header b-b">
					<!-- <image class="logo" src="http://duoduo.qibukj.cn/./Upload/Images/20190321/201903211727515.png"></image> -->
					<text class="name">共{{cartInfo.length}}件商品</text>
				</view>
				<!-- 商品列表 -->
				<view v-for="(item,index) in cartInfo" :key="index">
					<view class="g-item">
						<image :src="indexUrl(item.productInfo.image)"></image>
						<view class="right">
							<text class="title clamp">{{item.productInfo.store_name}}</text>
							<text class="spec">{{item.productInfo.attrInfo?item.productInfo.attrInfo.suk :''}}</text>
							<view class="price-box">
								<text class="price">￥{{item.productInfo.attrInfo?item.productInfo.attrInfo.price:item.productInfo.price}}</text>
								<text class="number">x {{item.cart_num}}</text>
							</view>
						</view>
					</view>
				</view>

			</view>

			<!-- {{confirmOrder.seckill_id}} -->
			<!-- 优惠明细 -->
			<view class="yt-list" v-if="confirmOrder.seckill_id <= 0 && confirmOrder.combination_id <=0 && confirmOrder.presale_id <=0">
				<view class="yt-list-cell b-b" v-if="!check" @click="toggleMask('show')">
					<view class="cell-icon">
						券
					</view>
					<text class="cell-tit clamp">优惠券</text>
					<text v-if="isCoupon">
						<text class="cell-tip active">
							{{couponName}}
						</text>
					</text>
					<text v-else>
						<text class="cell-tip active">
							{{isCouponName}}
						</text>
					</text>
					<text class="cell-more wanjia wanjia-gengduo-d"></text>
				</view>
				<view v-if="!isCoupon">
					<view class="yt-list-cell b-b" v-if="confirmOrder.integralRatio != 0 ">
						<view class="cell-icon hb">
							减
						</view>
						<text class="cell-tit clamp">积分抵扣</text>
						<text class="cell-tip disabled"> 剩余积分 <text class="int-s">{{userInfo.integral}}</text></text>
						<checkbox-group  @change="checkboxChange">
							<checkbox value="cb" color="#FA436A" style="transform:scale(0.8)"  />
						</checkbox-group>
						
					</view>
				</view>
				
			</view>
			<!-- 金额明细 -->
			<view class="yt-list">
				<view class="yt-list-cell b-b">
					<text class="cell-tit clamp">商品金额</text>
					<text class="cell-tip">￥{{total}}</text>
				</view>
				<view class="yt-list-cell b-b"  v-if="isCoupon">
					<text class="cell-tit clamp">优惠金额</text>
					<text class="cell-tip red">-￥{{discountPrice}}</text>
				</view>
				<view class="yt-list-cell b-b"  v-if="check">
					<text class="cell-tit clamp">抵扣金额</text>
					<text class="cell-tip red">-￥{{deductIntPrice}}</text>
				</view>
				<view class="yt-list-cell b-b" v-if="confirmOrder.priceGroup.vipPrice">
					<text class="cell-tit clamp">会员减免</text>
					<text class="cell-tip">{{confirmOrder.priceGroup.vipPrice}}</text>
				</view>
				<view class="yt-list-cell b-b" v-if="Number(confirmOrder.priceGroup.storePostage) <=0">
					<text class="cell-tit clamp">运费</text>
					<text class="cell-tip">免运费</text>
				</view>
				<view class="yt-list-cell b-b" v-else>
					<text class="cell-tit clamp">运费</text>
					<text class="cell-tip red">￥{{confirmOrder.priceGroup.storePostage}}</text>
				</view>
				<view class="yt-list-cell desc-cell">
					<text class="cell-tit clamp">备注</text>
					<input class="desc" type="text" v-model="desc" name="mark" placeholder="请填写备注信息" placeholder-class="placeholder" />
				</view>
			</view>


			<!-- 支付方式 -->
			<view class="goods-section">
				<view class="g-header b-b">
					<text class="name">支付方式</text>
				</view>
				<view class="zf acea-row" :class="{is: isZf ==1}" @click="payment('1')">
					<view class="zfimg aces-center">
						<image class="zhfimg" src="../../static/9Q3S.png"></image>
						<view class="">微信支付</view>
					</view>
					<view class="zftit">微信快捷支付</view>
				</view>
				<view class="zf acea-row" :class="{is: isZf ==2}" @click="payment('2')">
					<view class="zfimg aces-center">
						<image class="zhfimg" src="../../static/9Q3Sw.png"></image>
						<view class="">余额支付</view>
					</view>
					<view class="zftit">可用余额:{{now_money}}</view>
				</view>

			</view>


			<!-- 底部 -->
			<view class="footer">
				<view class="price-content">
					<text>实付款</text>
					<text class="price-tip">￥</text>
					<text class="price">{{isTotal}}</text>
				</view>
				<view>
					<button class="submit" form-type="submit">立即结算</button>
				</view>
			</view>

			<!-- 优惠券面板 -->
			<view class="mask" :class="maskState===0 ? 'none' : maskState===1 ? 'show' : ''" @click="toggleMask">
				<view class="mask-content" @click.stop.prevent="stopPrevent">
					<!-- 优惠券页面，仿mt -->
					<radio-group @change="radioChange">


						<view v-if="couponList.length == 0" class="iscou">
							<image class="nocou" src="../../static/noCoupon.png"></image>
						</view>
						<view v-else>
							<view class="coupon-item" v>
								<view class="con">
									<view class="left">
										<text class="title ">不使用</text>
									</view>
									<view class="right">
										<radio value="no" color="#FA436A" />
									</view>
								</view>
								<!-- <text class="tips">满{{item.use_min_price}}可用</text> -->
							</view>

							<view class="coupon-item" v-for="(item,index) in couponList" :key="index">
								<view class="con" >
									<view class="left">
										<text class="title coutit">{{item.coupon_title}}</text>
										
										
										<text class="time">有效期至 {{item.coupon_start_time}}~{{item.coupon_end_time}}</text>
									</view>
									<view class="right">
										<!-- <checkbox :value="index" color="#FA436A" style="transform:scale(0.7)" />选中 -->
										<radio :value="item" color="#FA436A" />
									</view>

									<view class="circle l"></view>
									<view class="circle r"></view>
								</view>
								<!-- <text class="tips">满{{item.use_min_price}}可用</text> -->
								
								<text class="tips">
									
									<view v-if="item.coupon_type==1">
										<text class='time'>￥{{item.coupon_price}}</text>
									</view>
									<view v-else-if="item.coupon_type==2">
										<text class='time'>{{item.coupon_discount*10}}折</text>
									</view>
								</text>
								
								
							</view>
						</view>


					</radio-group>
				</view>
			</view>


		</form>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				maskState: 0, //优惠券面板显示状态
				desc: '', //备注
				payType: 1, //1微信 2支付宝
				couponList: [],
				addressData: [{
						real_name: '',
						phone: '',
						province: '',
						city: '',
						district: '',
						detail: '',
					}

				],
				cartId: 0,
				cartInfo: [],
				cartInfoId: '',
				total: 0, //总价
				coupon: [], //选中的优惠券
				isCoupon: false, //是否使用优惠券
				couponName: '',
				priceA: 0,
				couponInfoA: [],
				isTotal: 0, //实付款
				discountPrice: 0, //优惠金额
				isCouponName: '选择优惠券',
				isZf: 1,
				now_money: 0, //余额
				confirmOrder: null,
				address:'',
				isClick:true,
				userInfo:[],
				check:false,
				deductIntPrice:0,
				integral:0,
				isMer:false,
				merIds:'',
			}
		},
		onLoad(option) {
			//商品数据
			getApp().user_api(true);
			this.cartId = option.cartId
			this.address = option.address
			this.confirm_order();
		},
		onShow() {
			this.addressData = [];
			this.addressIist();
		},
		methods: {
			confirm_order() {
				let self = this;
				let path = '/gzh/deal_api/confirm_order'
				let data = {
					cartId: self.cartId
				}
				console.log('啊啊啊啊啊',self.cartId, getApp().globalData.userInfo)
				getApp().http(path, data, function(res) {
					self.cartInfo = res.cartInfo
					self.confirmOrder = res;
					
					for(let i = 0 ; i < res.cartInfo.length ; i++ ){
						let mer = res.cartInfo[i].mer_id
						
						if(mer == 1){
							self.couponName= '选择优惠券'
							self.isCoupon = true
							// isMer = true;
						}
						
						console.log('产品', self.cartInfo,mer)
					}
					
					let pa = '/gzh/user_api/my';
					getApp().http(pa, {}, function(res) {
						console.log('个人信息',res)
						
						self.userInfo = res
						self.now_money = res.now_money 
						self.integral = res.integral
						self.totalCount();
						self.getUIseCouponOrder();
					});
					
				}, false, 'post')
			},
			totalCount() {
				let self = this;
				let validList = self.cartInfo;
				let selectCountPrice = 0.00;
				// let storePostage = self.confirmOrder.priceGroup ? self.confirmOrder.priceGroup.storePostage :

				for (var info in validList) {
					let valPrice = validList[info].productInfo.attrInfo ? validList[info].productInfo.attrInfo.price : validList[info]
						.productInfo.price
					selectCountPrice = Number(selectCountPrice) + Number(validList[info].cart_num) * Number(valPrice)
					self.total = selectCountPrice
					// self.isTotal = selectCountPrice
					self.isTotal = Number(selectCountPrice) + Number(self.confirmOrder.priceGroup.storePostage);

					self.cartInfoId += validList[info].productInfo.id + ',';
					self.merIds += validList[info].mer_id + ',';
					
					console.log(self.merIds,'商铺id')
				}
			},
			getUIseCouponOrder() {
				let self = this;

				console.log('商品id', self.cartInfo)

				let path = '/gzh/coupons_api/get_use_coupon_order'
				let data = {
					totalPrice: self.total,
					pids: self.cartInfoId,
					mer_ids : self.merIds,
				};
				getApp().http(path, data, function(res) {
					self.couponList = res
					console.log('地址', self.coupon)
				})
			},

			radioChange: function(e) {
				let self = this;
				self.coupon = e.detail.value;
				let couponInfo = self.coupon; // 优惠券
				let coupon_discount = couponInfo.coupon_discount; //折扣券金额
				let coupon_price = couponInfo.coupon_price; //代金券价格
				let cartInfo = self.cartInfo; // 商品
				let coupon_type = couponInfo.coupon_type; //优惠券类型
				let isPrice = 0;
				let coupon_products_arr = couponInfo.coupon_products; //优惠券对应商品id
				console.log('sdfsdf对对对', couponInfo, self.coupon);
				// console.log('的点点滴滴',couponInfo,coupon_products_arr);

				// 不使用
				if (couponInfo == 'no') {
					self.isCoupon = false
					self.discountPrice = 0;
					self.isTotal = self.total+Number(self.confirmOrder.priceGroup.storePostage);
					self.isCouponName = '不使用'
					console.log('不使用')

				} else {
					self.isCoupon = true
					self.couponName = couponInfo.coupon_title
					if (coupon_products_arr == '') {
						for (let i = 0; i < cartInfo.length; i++) {
							if (self.priceA < cartInfo[i].truePrice) {
								self.priceA = cartInfo[i].truePrice;
								self.couponInfoA = cartInfo[i];
							}
						}
						console.log('全部商品', self.priceA)
					} else {
						for (let j = 0; j < coupon_products_arr.length; j++) {
							for (let i = 0; i < cartInfo.length; i++) {
								if (cartInfo[i].product_id === parseInt(coupon_products_arr[j])) {
									if (self.priceA < cartInfo[i].truePrice) {
										self.priceA = cartInfo[i].truePrice;
										self.couponInfoA = cartInfo[i];
									}
								}
							}
						}
					}

					// 折扣券
					if (coupon_type == 2) {
						// console.log('折扣')
						let truePrice = self.couponInfoA.truePrice
						let price = truePrice - (truePrice * coupon_discount);
						let totalPrice = self.total - price
						self.discountPrice = price;
						self.isTotal = totalPrice + Number(self.confirmOrder.priceGroup.storePostage);
						console.log('折扣金额', self.couponList, totalPrice);
					} else if (coupon_type == 1) {
						// 满减 coupon_price

						let truePrice = self.couponInfoA.truePrice
						if (coupon_price >= truePrice) {
							console.log('大')
							let totalPrice = self.total - truePrice
							self.discountPrice = truePrice;
							self.isTotal = totalPrice + Number(self.confirmOrder.priceGroup.storePostage);

						} else {
							console.log('小')
							let price = truePrice - (truePrice - coupon_price);
							let totalPrice = self.total - price
							self.discountPrice = price + Number(self.confirmOrder.priceGroup.storePostage);
							self.isTotal = totalPrice;
						}
						// console.log('代金券',coupon_price,truePrice)
						// console.log('满减金额',truePrice,price);

					}

				}



			},

			// 地址
			addressIist() {
				let self = this;
				let path = '/gzh/user_api/user_address_list'
				getApp().http(path, {}, function(res) {
					console.log('地址少时诵诗书', self.address)
					
					if(self.address == '' || self.address == undefined || self.address == null){
						console.log('灌灌灌灌', self.address)
						self.addressData = res[0] ? res[0] : self.addressData
					}else{
						console.log('是是是', self.address)
						self.addressData = res[self.address]
					}
					
				})
			},
			clickAdd(){
				uni.navigateTo({
					url: "/pages/address/address?source=1&cartId="+this.cartId+'&adr=3'
				})

				
			},

			indexUrl(icon) {
				let http = icon.indexOf('http')
				if (http == -1) {
					return this.webUrl + '/' + icon
				} else {
					return icon;
				}
			},

			// 积分
			checkboxChange: function (e) {
				let self = this;
				self.check = !self.check;
				let integral = self.userInfo.integral;//剩余积分
				let integralRatio = self.confirmOrder.integralRatio;//1积分多少钱
				if(self.check){
					// 选中
					let intPrice = integral * integralRatio
					let totalPrice = self.total / integralRatio
					
					if(intPrice > self.total){
						let integ = integral -totalPrice
						self.userInfo.integral = integ.toFixed(2)
						self.deductIntPrice = self.total
						self.isTotal = self.isTotal - self.deductIntPrice;
						console.log('积分大',totalPrice,intPrice,self.userInfo.integral)
					}else{
						
						self.userInfo.integral = 0
						self.deductIntPrice = intPrice.toFixed(2)
						
						let to = self.isTotal - self.deductIntPrice;
						self.isTotal = to.toFixed(2)
						
						console.log('商品金额大',self.isTotal,totalPrice,intPrice)
					}
					
				}else{
					// let to = self.isTotal + self.deductIntPrice;
					// console.log('积分',to,self.isTotal,self.deductIntPrice)
					// self.isTotal = to.toFixed(2)
					self.isTotal = self.total+Number(self.confirmOrder.priceGroup.storePostage);
					self.userInfo.integral = self.integral
					
					
				}
				
			},

			//显示优惠券面板
			toggleMask(type) {
				let timer = type === 'show' ? 10 : 300;
				let state = type === 'show' ? 1 : 0;
				this.maskState = 2;
				setTimeout(() => {
					this.maskState = state;
				}, timer)
			},
			numberChange(data) {
				this.number = data.number;
			},
			changePayType(type) {
				this.payType = type;
			},

			payment(e) {
				let self = this;
				self.isZf = e;

				if (self.isZf == 1) {
					console.log('微信支付')
				} else {
					console.log('余额支付')
				}
			},
			formSubmit(e) {
				
				uni.showLoading({mask: true,})
				let self = this;
				let isType = self.isZf;
				if (isType == 1) {
					var payType = 'weixin'
				} else if (isType == 2) {
					var payType = 'yue'
					if (self.now_money < self.isTotal) {
						getApp().showTip("余额不足", "none")
						return false;
					}
				}
				// useIntegral
				let addressId = self.addressData.id //地址id
				let couponId = self.coupon.id ? self.coupon.id : '0' //优惠券id
				let mark = e.detail.value.mark //备注
				
				let combination_id = self.confirmOrder.combination_id
				let seckill_id = self.confirmOrder.seckill_id
				let presale_id = self.confirmOrder.presale_id
				let useIntegral =  self.check ? 1 :0
				
				console.log('提交', self.check,useIntegral);
				// return;
			
				let path = '/gzh/deal_api/create_order?key=' + self.confirmOrder.orderKey
				let data = {
					payType: payType,
					addressId: addressId,
					couponId: couponId,
					mark: mark,
					pinkId: 0,
					useIntegral:useIntegral,
					presale_id:presale_id,
					combinationId: combination_id,
					seckill_id: seckill_id,
					bargainId: 0,
				}
				
				// return false;
				
				
				getApp().http(path, data, function(res) {
					console.log("创建订单返回", payType, res)
					if (res.code == 200) {
						var status = res.data.status,
							orderId = res.data.result.orderId,
							jsConfig = res.data.result.jsConfig,
							goPages = '/pages/money/paySuccess?order_id=' + orderId + '&msg=' + res.msg,
							page = '/pages/order/order?state=0';
						console.log("创建订单返回2", status)
						switch (status) {
							case 'ORDER_EXIST':
							case 'EXTEND_ORDER':
							case 'PAY_ERROR':
								uni.hideLoading();
								getApp().showTip(res.msg, "none")
								break;
							case 'SUCCESS':
								uni.hideLoading();

								uni.redirectTo({
									url: goPages
								})
								getApp().showTip(res.msg, "success")

								break;
							case 'WECHAT_PAY':
								console.log("微信支付")
								let path = '/gzh/auth_api/get_jsdk';
								uni.request({
									url: self.webUrl + path,
									method: "GET",
									header: {
										"X-Requested-With": "XMLHttpRequest"
									},
									data: {
										url: btoa(window.location.href)
									},
									success: (res) => {
										console.log('微信支付')
										let jssdk = JSON.parse(res.data);
										console.log('微信支付11',jssdk)
										mapleWx(jssdk, function() {
											this.chooseWXPay(
												jsConfig,
												function() {
													console.log("执行着了么#####")
													uni.hideLoading();
													uni.redirectTo({
														url: goPages
													})
												}, {
													fail: function(res) {
														console.log("失败", res)
														
														uni.showToast({
															title: "支付失败"
														})
														// window.history.back(-1);
														uni.redirectTo({
															url: page
														})

													},
													cancel: function() {
														uni.showToast({
															title: "您已取消支付"
														})
														// window.history.back(-1);
														uni.redirectTo({
															url: page
														})
													},
													success: function() {
														console.log("成功过过")
														window.history.back(-1);
													}

											});
										})

									}
								})

								break;
						}



					} else {
						getApp().showTip(res.msg, "none")
					}
				}, true, 'post')

				// uni.redirectTo({
				// 	url: '/pages/money/pay'
				// })
			},
			stopPrevent() {}
		}
	}
</script>

<style lang="scss">
	page {
		background: $page-color-base;
		padding-bottom: 100upx;
	}

	.int-s{
		color: #FA436A;
		font-size: 35upx;
		margin:0 9upx;
	}

	.coutit {
		color: #FA436A;
	}

	.iscou {
		text-align: center;
	}

	.nocou {
		width: 414upx;
		height: 336upx;
	}

	/*  支付 */
	.zf {
		border: 1px solid #EEEEEE;
		border-radius: 6upx;
		margin: 10px 15px;
		padding: 16upx 0upx;
		text-align: center;
		font-size: 28upx;
		// colro:#888;
	}

	.zfimg {
		border-right: 1px solid #EEEEEE;
		width: 50%;
		text-align: center;
	}

	.zhfimg {
		width: 40upx;
		height: 40upx;
		margin-right: 8upx;
		// margin-top:5upx;
	}

	.zftit {
		width: 50%;
		// font-size:35upx;
		color: #797979;
	}

	.is {
		border: 1px solid #FA436A;
		color: #FA436A;
	}


	.address-section {
		padding: 30upx 0;
		background: #fff;
		position: relative;

		.order-content {
			display: flex;
			align-items: center;
		}

		.icon-shouhuodizhi {
			flex-shrink: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 90upx;
			color: #888;
			font-size: 44upx;
		}

		.cen {
			display: flex;
			flex-direction: column;
			flex: 1;
			font-size: 28upx;
			color: $font-color-dark;
		}

		.name {
			font-size: 34upx;
			margin-right: 24upx;
		}

		.address {
			margin-top: 16upx;
			margin-right: 20upx;
			color: $font-color-light;
		}

		.icon-you {
			font-size: 32upx;
			color: $font-color-light;
			margin-right: 30upx;
		}


	}

	.a-bg {
		// position: absolute;
		// left: 0;
		// bottom: 0;
		display: block;
		width: 100%;
		height: 5upx;
	}

	.goods-section {
		margin-top: 16upx;
		background: #fff;
		padding-bottom: 1px;

		.g-header {
			display: flex;
			align-items: center;
			height: 84upx;
			padding: 0 30upx;
			position: relative;
		}

		.logo {
			display: block;
			width: 50upx;
			height: 50upx;
			border-radius: 100px;
		}

		.name {
			font-size: 30upx;
			color: $font-color-base;
			margin-left: 24upx;
		}

		.g-item {
			display: flex;
			margin: 20upx 30upx;

			image {
				flex-shrink: 0;
				display: block;
				width: 140upx;
				height: 140upx;
				border-radius: 4upx;
			}

			.right {
				flex: 1;
				padding-left: 24upx;
				overflow: hidden;
			}

			.title {
				font-size: 30upx;
				color: $font-color-dark;
				// color: red
			}

			.spec {
				font-size: 26upx;
				color: $font-color-light;
			}

			.price-box {
				display: flex;
				align-items: center;
				font-size: 32upx;
				color: $font-color-dark;
				padding-top: 10upx;

				.price {
					margin-bottom: 4upx;
				}

				.number {
					font-size: 26upx;
					color: $font-color-base;
					margin-left: 20upx;
				}
			}

			.step-box {
				position: relative;
			}
		}
	}

	.yt-list {
		margin-top: 16upx;
		background: #fff;
	}

	.yt-list-cell {
		display: flex;
		align-items: center;
		padding: 10upx 30upx 10upx 40upx;
		line-height: 70upx;
		position: relative;

		&.cell-hover {
			background: #fafafa;
		}

		&.b-b:after {
			left: 30upx;
		}

		.cell-icon {
			height: 32upx;
			width: 32upx;
			font-size: 22upx;
			color: #fff;
			text-align: center;
			line-height: 32upx;
			background: #f85e52;
			border-radius: 4upx;
			margin-right: 12upx;

			&.hb {
				background: #ffaa0e;
			}

			&.lpk {
				background: #3ab54a;
			}

		}

		.cell-more {
			align-self: center;
			font-size: 24upx;
			color: $font-color-light;
			margin-left: 8upx;
			margin-right: -10upx;
		}

		.cell-tit {
			flex: 1;
			font-size: 26upx;
			color: $font-color-light;
			margin-right: 10upx;
		}

		.cell-tip {
			font-size: 26upx;
			color: $font-color-dark;

			&.disabled {
				color: $font-color-light;
			}

			&.active {
				color: $base-color;
			}

			&.red {
				color: $base-color;
			}
		}

		&.desc-cell {
			.cell-tit {
				max-width: 90upx;
			}
		}

		.desc {
			flex: 1;
			font-size: $font-base;
			color: $font-color-dark;
		}
	}

	/* 支付列表 */
	.pay-list {
		padding-left: 40upx;
		margin-top: 16upx;
		background: #fff;

		.pay-item {
			display: flex;
			align-items: center;
			padding-right: 20upx;
			line-height: 1;
			height: 110upx;
			position: relative;
		}

		.icon-weixinzhifu {
			width: 80upx;
			font-size: 40upx;
			color: #6BCC03;
		}

		.icon-alipay {
			width: 80upx;
			font-size: 40upx;
			color: #06B4FD;
		}

		.icon-xuanzhong2 {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 60upx;
			height: 60upx;
			font-size: 40upx;
			color: $base-color;
		}

		.tit {
			font-size: 32upx;
			color: $font-color-dark;
			flex: 1;
		}
	}

	.footer {
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 995;
		display: flex;
		// align-items: center;
		line-height: 90upx;
		width: 100%;
		height: 90upx;
		justify-content: space-between;
		font-size: 30upx;
		background-color: #fff;
		z-index: 998;
		color: $font-color-base;
		box-shadow: 0 -1px 5px rgba(0, 0, 0, .1);

		// border:1px solid red;
		.price-content {
			padding-left: 30upx;
		}

		.price-tip {
			color: $base-color;
			margin-left: 8upx;
		}

		.price {
			font-size: 36upx;
			color: $base-color;
		}

		.submit {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 280upx;
			height: 100%;
			color: #fff;
			font-size: 32upx;
			background-color: $base-color;
			border-radius: 0upx;
			// height:100%;
			margin: 0;
		}
	}

	/* 优惠券面板 */
	.mask {
		display: flex;
		align-items: flex-end;
		position: fixed;
		left: 0;
		top: var(--window-top);
		bottom: 0;
		width: 100%;
		background: rgba(0, 0, 0, 0);
		z-index: 9995;
		transition: .3s;

		.mask-content {
			width: 100%;
			min-height: 40vh;
			max-height: 70vh;
			background: #f3f3f3;
			transform: translateY(100%);
			transition: .3s;
			overflow-y: scroll;
		}

		&.none {
			display: none;
		}

		&.show {
			background: rgba(0, 0, 0, .4);

			.mask-content {
				transform: translateY(0);
			}
		}
	}

	/* 优惠券列表 */
	.coupon-item {
		display: flex;
		flex-direction: column;
		margin: 20upx 24upx;
		background: #fff;

		.con {
			display: flex;
			align-items: center;
			position: relative;
			height: 120upx;
			padding: 0 30upx;

			&:after {
				position: absolute;
				left: 0;
				bottom: 0;
				content: '';
				width: 100%;
				height: 0;
				border-bottom: 1px dashed #f3f3f3;
				transform: scaleY(50%);
			}
		}

		.left {
			display: flex;
			flex-direction: column;
			justify-content: center;
			flex: 1;
			overflow: hidden;
			height: 100upx;
		}

		.title {
			font-size: 32upx;
			// color: $font-color-dark;
			margin-bottom: 10upx;
		}

		.time {
			font-size: 24upx;
			color: $font-color-light;
		}

		.right {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			font-size: 26upx;
			color: $font-color-base;
			height: 100upx;
		}

		.price {
			font-size: 44upx;
			color: $base-color;

			&:before {
				content: '￥';
				font-size: 34upx;
			}
		}

		.tips {
			font-size: 24upx;
			color: $font-color-light;
			line-height: 60upx;
			padding-left: 30upx;
		}

		.circle {
			position: absolute;
			left: -6upx;
			bottom: -10upx;
			z-index: 10;
			width: 20upx;
			height: 20upx;
			background: #f3f3f3;
			border-radius: 100px;

			&.r {
				left: auto;
				right: -6upx;
			}
		}
	}
</style>
