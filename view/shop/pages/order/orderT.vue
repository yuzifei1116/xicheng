<template>
	<view class="con">
		<view class="acea-row evcon" v-for="(item,index) in cartInfo.cartInfo" :key="index">
			<view>
				<image class="evimg" :src='indexUrl(item.productInfo.image)'></image>
			</view>
			<view class="evt">
				<view class="aces-space-between">
					<view>{{item.productInfo.store_name}} </view>
					<view class="ncol">￥{{item.truePrice}}</view>
				</view>
				<view class="acea-row-reverse ncol">X{{item.cart_num}}</view>
			</view>
		</view>
		
		<view class="evxing">
			<view class="aces-space-between evalx">
				<view>退货件数</view>
				<view class="evrate">
					{{cartInfo.total_num}}
				</view>
			</view>
			<view class="aces-space-between evalx">
				<view>退款金额</view>
				<view class="evrate">
					￥{{cartInfo.total_price}}
				</view>
			</view>
			<view class="aces-space-between evalx">
				<view>退款原因</view>
				<view class="evrate acea-row">
					<picker @change="bindPickerChange" :value="index" :range="array">
						<view class="uni-input">{{array[index]}}</view>
					</picker>
					<text class="yticon icon-you eva-icon"></text>
				</view>
			</view>
			<view class="aces-space-between evalx">
				<view style="margin-top:15upx">备注说明</view>
				<view class="evrate">
					<textarea class="evtext" @input="area" maxlength="100" placeholder="请填写备注信息,100字以内"/>
				</view>
			</view>
			<button class='button btn' type="warn" @click="btnComment">申请退款</button>
		</view>
	</view>
</template>

<script>
	export default {
		components: {
		},
		data() {
			return {
				unique:'',
				cart_info:[],
				product_score:0,
				service_score:0,
				comment:'',
				cartInfo:[],
				array: ['收货地址填错了', '与描述不符', '信息填错了，重拍', '收到商品损坏了','未按预定时间发货','其他原因'],
				index: 0,
			}
		},
		onLoad(option) {
			console.log(option);
			this.unique = option.uni
			this.register();
		},
		onShow() {
		},
		methods: {
			register(){
				let self = this;
				let path = '/gzh/user_api/get_order'
				let data = {
					uni: self.unique
				}
				
				getApp().http(path, data, function(res) {
					console.log(res,'商品')
					self.cartInfo = res;
				})
				
			},
			area(e){
				this.comment = e.detail.value;
			},
			bindPickerChange: function(e) {
				console.log('picker发送选择改变，携带值为', e)
				this.index = e.target.value
			},
			
			btnComment(){
				let self = this;
				
				
				
				let path = '/gzh/deal_api/apply_order_refund'
				let te = self.array[self.index]
				
				console.log(te,'水电费水电费');
				// return;
				let data = {
					uni: self.unique,
					text:te,
					refund_reason_wap_explain:self.comment,
				}
				
				getApp().http(path, data, function(res) {
					console.log(res)
					if(res.code == 200){
						
						getApp().showTip(res.msg,'success')
						
						setTimeout(() => {
							uni.navigateBack(2)
						}, 1500);
					}else{
						getApp().showTip(res.msg,'none')
					}
				},true,'post')
				
			},
			indexUrl(icon) {
				let http = icon.indexOf('http')
				if (http == -1) {
					return this.webUrl + '/' + icon
				} else {
					return icon;
				}
			},
			

		
		}
	}
</script>

<style lang="scss">
	@import url("orderT.css");
	page {
		background: $page-color-base;
		padding-bottom: 100upx;
	}
</style>
