<template>
	<view class="con">
		<view class="acea-row evcon">
			<view>
				<image class="evimg" :src='indexUrl(cart_info.productInfo.image)'></image>
			</view>
			<view class="evt">
				<view class="aces-space-between">
					<view>{{cart_info.productInfo.store_name}} </view>
					<view class="ncol">￥{{cart_info.truePrice}}</view>
				</view>
				<view class="acea-row-reverse ncol">X{{cart_info.cart_num}}</view>
			</view>
		</view>
		<view class="evxing">
			<view class="acea-row evalx">
				<view>商品质量</view>
				<view class="evrate">
					<uni-rate max="5" size="23" type="1" margin='8' ></uni-rate>
				</view>
				<view v-if="product_score" class="product">{{product_score}}分</view>
			</view>
			<view class="acea-row">
				<view>服务态度</view>
				<view class="evrate">
					<uni-rate max="5"  size="23" type="2" margin='8' ></uni-rate>
				</view>
				<view v-if="service_score" class="product">{{service_score}}分</view>
			</view>
			
			<view class="evtext">
				<view class="evarea">
					<textarea @input="area" placeholder="商品满足你的期待吗？说说你的想法"/>
				</view>
				<!-- <view class="upimg">
					<view class="uplimg"  @click="upImg">+</view>
				</view> -->
			</view>
			
			<button class='button btn' type="warn" @click="btnComment">立即评价</button>
		</view>
	</view>
</template>

<script>
	import uniRate from '@/components/uni-rate/uni-rate.vue'
	export default {
		components: {
			uniRate
		},
		data() {
			return {
				unique:'',
				cart_info:[],
				product_score:0,
				service_score:0,
				comment:'',
			}
		},
		onLoad(option) {
			console.log(option);
			this.unique = option.unique
			this.register();
		},
		onShow() {
		},
		methods: {
			register(){
				let self = this;
				let path = '/gzh/store_api/get_order_product'
				let data = {
					unique: self.unique
				}
				
				getApp().http(path, data, function(res) {
					console.log(res,'商品')
					self.cart_info = res.cart_info;
				})
				
				uni.$on('change',function(data){
					console.log(data);
				    console.log('监听到事件来自' + data.value);
					if(data.type == 1){
						self.product_score = data.value;
					}else{
						self.service_score = data.value;
					}
				});
			},
			area(e){
				this.comment = e.detail.value;
			},
			btnComment(){
				let self = this;
				
				// console.log(self.comment,'水电费水电费');
				// return;
				
				if(self.product_score == 0 || self.service_score == 0){
					getApp().showTip('请给商品评分','none');
					return;
				}
				if(self.comment == ''){
					getApp().showTip('请填写您对该商品的评价','none');
					return;
				}
				
				let path = '/gzh/user_api/user_comment_product'
				let data = {
					unique: self.unique,
					comment:self.comment,
					product_score:self.product_score,
					service_score:self.service_score
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
			
			
			upImg(){
				console.log(this.webUrl)
				
				let self = this;
				let url = '/gzh/public_api/upload';
				
				uni.chooseImage({
					count:1,
					success: function(chooseRes) {
						// uni.showLoading({})
						let tempFilePaths = chooseRes.tempFilePaths;
						console.log('大哥的风格的风格',chooseRes,tempFilePaths[0])
						uni.uploadFile({
							url: self.webUrl+url,
							filePath: tempFilePaths[0],
							name: 'filename',
							success: (res) => {
								// console.log('成功',res);
								console.log(JSON.parse(res.data))
								console.log('sddddddddddddddddddddddddd')
								
							}
						})
					},
				})
			},
		}
	}
</script>

<style lang="scss">
	@import url("orderEval.css");
	page {
		background: $page-color-base;
		padding-bottom: 100upx;
	}
</style>
