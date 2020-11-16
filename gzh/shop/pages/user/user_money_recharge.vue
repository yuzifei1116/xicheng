<template>
	<view class="container">
		 <form @submit="formSubmit">
			<view class="rec">输入金额</view>
			<view class="rech aces-center">
				<text class="rech-q">￥</text>
				<input class="rech-inp" name="input" type="number" placeholder="" />
			</view>
			<view class="rec-t">提示：当前余额为 <text class="red">￥{{userInfo.now_money}}</text></view>
			<view class="">
				<button class="btn" form-type="submit">立即充值</button>
			</view>
		</form>
		
		
		<view class="con-ch" v-if="recInfo.length > 0">
			<view  v-for="(item, index) in recInfo" :key="index">
				<view class="con-ch-d">*优惠：充{{item.price}}送{{item.money}}</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default{
		data(){
			return{
				userInfo:[],
				recInfo:[],
			}
		},
		onLoad() {
			getApp().user_api(true);
			this.my();
			this.rechargeMarketing();
		},
		onShow() {
		},
		onHide() {
			
		},
		methods:{
			my(){
				let self = this;
				let path = '/gzh/user_api/my'
				getApp().http(path, {}, function(res) {
					console.log('个人信息',res)
					self.userInfo = res;
				})
			},
			rechargeMarketing(){
				let self = this;
				let path = '/gzh/auth_api/recharge_marketing'
				getApp().http(path, {}, function(res) {
					console.log('个人信1111息',res)
					self.recInfo = res;
				})
			},
			formSubmit: function(e) {
				let self = this;
				let price = e.detail.value.input
				console.log(price)
				
				let path = '/gzh/user_api/user_wechat_recharge'
				let data = {
					price: price,
				}
				// getApp().showTip();
				uni.showLoading({
				    // title: '加载中'
					mask: true,
				})

				getApp().http(path, data, function(res) {
					console.log("创建订单返回", res)
					if (res.code == 200) {
						uni.hideLoading();
						var jsConfig = res.data
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
								console.log('微信支付',res)
								let jssdk = JSON.parse(res.data);
								console.log('微信支付11',jssdk)
								mapleWx(jssdk, function() {
									this.chooseWXPay(
										jsConfig,
										function() {
											console.log("执行着了么#####")
											uni.hideLoading();
											uni.showToast({
												title: "充值成功"
											})
											
											setTimeout(() => {
												window.history.back(-1);
											}, 1000);
											
										}, {
											fail: function(res) {
												console.log("失败", res)
												
												uni.showToast({
													title: "充值失败"
												})
												setTimeout(() => {
													window.history.back(-1);
												}, 1000);
										
											},
											cancel: function() {
												uni.showToast({
													title: "您已取消支付"
												})
												// window.history.back(-1);
												// uni.redirectTo({
												// 	url: page
												// })
											},
											success: function() {
												console.log("成功过过")
												window.history.back(-1);
											}
		
									});
								})
		
							}
						})
			
					} else {
						getApp().showTip(res.msg, "none")
					}
				}, true, 'post')
				
				
				
				
				
				
				
			},
			navTo(e){
				uni.navigateTo({
					url: e,
				})
			},
		}
	}
</script>

<style lang='scss'>
	@import url("user_money_recharge.css");
	
	.btn{
		border-radius: 50upx;
		border:0upx;
		background: $base-color;
		color:#fff;
	}
</style>