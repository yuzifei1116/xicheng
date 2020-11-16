<template>
	<view class="content b-t">
		<scroll-view scroll-with-animation scroll-y class="right-aside" :style="{height:scrollHeight+'px'}" @scrolltolower="scroAddr">
		
			<view class="list b-b" v-for="(item, index) in addressList" :key="index" >
				<view class="wrapper" @click="checkAddress(index)">
					<view class="address-box">
						<text v-if="item.is_default" class="tag">默认</text>
						<text class="address">{{item.province}}{{item.city}}{{item.district}} {{item.detail}}</text>
					</view>
					<view class="u-box">
						<text class="name">{{item.real_name}}</text>
						<text class="mobile">{{item.phone}}</text>
					</view>
				</view>
				<view class="aces-space-between">
					<view class="yticon acea-row" @click.stop="addAddress('edit', item.id)">
						<text class="icon-bianji ed-icon"></text>
						<text class="ed">编辑</text>
					</view>
					<view class="iconfg">|</view>
					<view class="yticon acea-row" @click.stop="delAddress(item.id)">
						<text class="ed-icon icon-iconfontshanchu1"></text>
						<text class="ed">删除</text>
					</view>
				</view>
			</view>
			<uni-load-more :status="status" :icon-size="16" :content-text="contentText" />
		</scroll-view>
		
		<button class="add-btn" @click="addAddress('add')">新增地址</button>
	</view>
</template>

<script>
	import uniListItem from "@/components/uni-list-item/uni-list-item.vue";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		components: {
			uniListItem
		},
		data() {
			return {
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
				cartId:0,
				id:0,
				num:0,
				adr:0,
			}
		},
		onShow(){
			this.addressList = [];
			this.userAddressIist();
		},
		onLoad(option){
			console.log(option.source);
			this.source = option.source;
			this.cartId = option.cartId
			
			this.id = option.id;
			this.num = option.num
			this.adr = option.adr
			
			
			getApp().user_api(true);
			// this.userAddressIist();
			this.calcSize();
		},
		methods: {
			
			userAddressIist(){
				let self = this;
				let path = '/gzh/user_api/user_address_list'
				let data = {page:self.page,limit:self.limit};
				getApp().http(path, data, function(result) {
					
					// self.addressList = result;
					console.log(result);
					
					
					let list = result;
					if (list.length > 0) {
						console.log(list.length)
						self.addressList = self.addressList.concat(list);
						self.status = list.length >= self.limit ? "more" : "";
						if (self.status == "more") {
							self.page += 1;
						}
						// self.status = list.length >= 20 ? "more" : "";
						// if (self.status == "more") {
						// 	self.page += 1;
						// }
						// self.addressList = self.addressList.concat(list);
					} else {
						self.status = "";
					}
					
					
					
				})
			},
			delAddress(e){
				let self = this;
				
				uni.showModal({
					title: '删除',
					content: '确定删除吗？',
					success: (md) => {
						if(md.confirm == true){
							console.log('确定')
							let path = '/gzh/user_api/remove_user_address';
							let data = {addressId:e};
							getApp().http(path, data, function(result) {
								
								console.log(result)
								console.log(result.code);
								console.log('------------00000000000000---------')
								
								if(result.code == 200){
									self.addressList = [];
									self.userAddressIist();
									getApp().showTip(result.msg,'success');
								}else{
									getApp().showTip(result.msg);
								}
							},true)
						}
					}
				})
				
				console.log(e);
				console.log('-----------------------------')
			},
			
			
			scroAddr(){
				let self = this;
				if(self.status){
					self.userAddressIist();
				}
			},
			//计算右侧栏每个tab的高度等信息
			calcSize(){
				var self = this;
				uni.getSystemInfo({
					success: function(res) { // res - 各种参数
						let info = uni.createSelectorQuery().select(".content");
						self.scrollHeight = res.windowHeight - 55
						// self.scrollHeight = 200
						console.log(info);
						console.log('啊啊啊啊',res);
					}
				});
			},
			
			
			//选择地址
			checkAddress(item){
				console.log('sdfsdfsdfsd',item);
				
				if(this.adr == 2){
					uni.navigateTo({
						url: `/pages/integral/integralOrder?id=`+this.id+'&num='+this.num+'&address='+item
					})
				}else if(this.adr == 3){
					uni.navigateTo({
						url: `/pages/order/createOrder?cartId=`+this.cartId+'&address='+item
					})
				}
			},
			addAddress(type, item){
				uni.navigateTo({
					url: `/pages/address/addressManage?type=${type}&data=${JSON.stringify(item)}`
				})
			},
			//添加或修改成功之后回调
			refreshList(data, type){
				//添加或修改后事件，这里直接在最前面添加了一条数据，实际应用中直接刷新地址列表即可
				this.addressList.unshift(data);
				
				console.log(data, type);
			}
		}
	}
</script>

<style lang='scss'>
	
	@import url("address.css");
	page{
		padding-bottom: 120upx;
	}
	.tag{
		font-size: 24upx;
		color: $base-color;
		margin-right: 10upx;
		background: #fffafb;
		border: 1px solid #ffb4c7;
		border-radius: 4upx;
		padding: 4upx 10upx;
		line-height: 1;
	}
	.address{
		font-size: 30upx;
		color: $font-color-dark;
	}
	.u-box{
		font-size: 28upx;
		color: $font-color-light;
		margin-top: 16upx;
		
	}
	.ed-icon{
		display: flex;
		align-items: center;
		height: 80upx;
		font-size: 40upx;
		color: $font-color-light;
		padding-left: 30upx;
	}
	
	.add-btn{
		position: fixed;
		left: 30upx;
		right: 30upx;
		bottom: 16upx;
		z-index: 95;
		display: flex;
		align-items: center;
		justify-content: center;
		width: 690upx;
		height: 80upx;
		font-size: 32upx;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);		
	}
</style>
